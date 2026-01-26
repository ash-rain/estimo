<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuoteTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'is_default',
        'is_industry_preset',
        'template_data',
        'sections',
        'terms_conditions',
        'email_template',
        'created_by',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_industry_preset' => 'boolean',
        'template_data' => 'array',
        'sections' => 'array',
    ];

    /**
     * Get the user who created this template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get quotes using this template.
     */
    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class, 'template_id');
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get default templates.
     */
    public function scopeDefaults($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to get industry presets.
     */
    public function scopeIndustryPresets($query)
    {
        return $query->where('is_industry_preset', true);
    }

    /**
     * Scope to get user-created templates.
     */
    public function scopeUserCreated($query)
    {
        return $query->where('is_industry_preset', false);
    }

    /**
     * Apply this template to a quote.
     */
    public function applyToQuote(Quote $quote): void
    {
        $data = $this->template_data ?? [];

        // Apply basic quote data
        if (isset($data['title'])) {
            $quote->title = $data['title'];
        }
        if (isset($data['notes'])) {
            $quote->notes = $data['notes'];
        }
        if (isset($data['footer'])) {
            $quote->footer = $data['footer'];
        }

        // Apply terms and conditions
        if ($this->terms_conditions) {
            $quote->terms_conditions = $this->terms_conditions;
        }

        // Apply valid_until if specified as offset
        if (isset($data['valid_until_days'])) {
            $quote->valid_until = now()->addDays($data['valid_until_days']);
        }

        // Set template reference
        $quote->template_id = $this->id;
        $quote->save();

        // Apply quote items if they exist
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $itemData) {
                $quote->items()->create([
                    'catalog_item_id' => $itemData['catalog_item_id'] ?? null,
                    'description' => $itemData['description'] ?? '',
                    'quantity' => $itemData['quantity'] ?? 1,
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'tax_rate' => $itemData['tax_rate'] ?? 0,
                    'discount_percent' => $itemData['discount_percent'] ?? 0,
                    'notes' => $itemData['notes'] ?? null,
                ]);
            }
        }
    }

    /**
     * Get a preview of this template.
     */
    public function preview(): array
    {
        $data = $this->template_data ?? [];

        return [
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'items_count' => isset($data['items']) ? count($data['items']) : 0,
            'has_terms' => !empty($this->terms_conditions),
            'sections_count' => isset($this->sections) ? count($this->sections) : 0,
            'created_by' => $this->creator?->name ?? 'System',
        ];
    }

    /**
     * Duplicate this template.
     */
    public function duplicate(string $newName = null): self
    {
        $template = $this->replicate();
        $template->name = $newName ?? ($this->name . ' (Copy)');
        $template->is_default = false;
        $template->is_industry_preset = false;
        $template->created_by = auth()->id();
        $template->save();

        return $template;
    }

    /**
     * Get the display name with category.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->category) {
            return "{$this->name} ({$this->category})";
        }
        return $this->name;
    }

    /**
     * Check if this template has items.
     */
    public function hasItems(): bool
    {
        $data = $this->template_data ?? [];
        return isset($data['items']) && is_array($data['items']) && count($data['items']) > 0;
    }
}

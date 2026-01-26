<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SectionTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'content',
        'order',
        'is_default',
        'created_by',
    ];

    protected $casts = [
        'content' => 'array',
        'is_default' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the user who created this template.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope to filter by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get default sections.
     */
    public function scopeDefaults($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope to order by the order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Apply this section to a quote or document.
     */
    public function applyContent(): array
    {
        return $this->content ?? [];
    }

    /**
     * Get formatted content for display.
     */
    public function getFormattedContentAttribute(): string
    {
        $content = $this->content ?? [];
        
        if (isset($content['text'])) {
            return $content['text'];
        }
        
        if (isset($content['html'])) {
            return $content['html'];
        }
        
        return '';
    }

    /**
     * Duplicate this section template.
     */
    public function duplicate(string $newName = null): self
    {
        $section = $this->replicate();
        $section->name = $newName ?? ($this->name . ' (Copy)');
        $section->is_default = false;
        $section->created_by = auth()->id();
        $section->save();

        return $section;
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
}

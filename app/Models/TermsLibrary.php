<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TermsLibrary extends Model
{
    use HasFactory;

    protected $table = 'terms_library';

    protected $fillable = [
        'title',
        'content',
        'category',
        'is_default',
        'order',
        'created_by',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the user who created this terms entry.
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
     * Scope to get default terms.
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
     * Duplicate this terms entry.
     */
    public function duplicate(string $newTitle = null): self
    {
        $terms = $this->replicate();
        $terms->title = $newTitle ?? ($this->title . ' (Copy)');
        $terms->is_default = false;
        $terms->created_by = auth()->id();
        $terms->save();

        return $terms;
    }

    /**
     * Get the display name with category.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->category) {
            return "{$this->title} ({$this->category})";
        }
        return $this->title;
    }

    /**
     * Get a preview of the content (first 150 characters).
     */
    public function getPreviewAttribute(): string
    {
        return strlen($this->content) > 150
            ? substr($this->content, 0, 150) . '...'
            : $this->content;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CatalogItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'category_id',
        'cost_price',
        'selling_price',
        'currency',
        'unit_type',
        'minimum_quantity',
        'is_taxable',
        'parent_id',
        'has_variants',
        'variant_attributes',
        'track_inventory',
        'stock_quantity',
        'low_stock_threshold',
        'tags',
        'notes',
        'image_url',
        'is_active',
        'created_by',
        'last_used_at',
    ];

    protected $casts = [
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'minimum_quantity' => 'decimal:2',
        'is_taxable' => 'boolean',
        'has_variants' => 'boolean',
        'variant_attributes' => 'array',
        'track_inventory' => 'boolean',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'tags' => 'array',
        'is_active' => 'boolean',
        'last_used_at' => 'datetime',
    ];

    /**
     * Get the category this item belongs to.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the user who created this item.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the parent item (if this is a variant).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class, 'parent_id');
    }

    /**
     * Get variants of this item.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(CatalogItem::class, 'parent_id');
    }

    /**
     * Scope to get only active items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to search items by name, SKU, or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('sku', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to get items by category.
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to get only parent items (not variants).
     */
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Scope to get items with low stock.
     */
    public function scopeLowStock($query)
    {
        return $query->where('track_inventory', true)
            ->whereColumn('stock_quantity', '<=', 'low_stock_threshold');
    }

    /**
     * Calculate the profit margin.
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->selling_price == 0) {
            return 0;
        }

        return (($this->selling_price - $this->cost_price) / $this->selling_price) * 100;
    }

    /**
     * Calculate the markup percentage.
     */
    public function getMarkupAttribute(): float
    {
        if ($this->cost_price == 0) {
            return 0;
        }

        return (($this->selling_price - $this->cost_price) / $this->cost_price) * 100;
    }

    /**
     * Check if item is a variant.
     */
    public function isVariant(): bool
    {
        return ! is_null($this->parent_id);
    }

    /**
     * Check if item has variants.
     */
    public function hasVariants(): bool
    {
        return $this->has_variants && $this->variants()->exists();
    }

    /**
     * Check if stock is low.
     */
    public function isLowStock(): bool
    {
        return $this->track_inventory
               && $this->low_stock_threshold
               && $this->stock_quantity <= $this->low_stock_threshold;
    }

    /**
     * Check if item is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->track_inventory && $this->stock_quantity <= 0;
    }

    /**
     * Get the display name (includes variant attributes if applicable).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->isVariant() && $this->variant_attributes) {
            $attributes = collect($this->variant_attributes)
                ->map(fn ($value, $key) => "$key: $value")
                ->implode(', ');

            return "{$this->name} ({$attributes})";
        }

        return $this->name;
    }
}

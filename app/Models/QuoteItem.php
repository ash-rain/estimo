<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuoteItem extends Model
{
    protected $fillable = [
        'quote_id',
        'catalog_item_id',
        'name',
        'sku',
        'description',
        'quantity',
        'unit_type',
        'unit_price',
        'discount_rate',
        'discount_amount',
        'subtotal',
        'is_taxable',
        'metadata',
        'notes',
        'sort_order',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'discount_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'is_taxable' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->calculateSubtotal();
        });

        static::saved(function ($item) {
            $item->quote->calculate();
            $item->quote->save();
        });

        static::deleted(function ($item) {
            $item->quote->calculate();
            $item->quote->save();
        });
    }

    /**
     * Get the quote this item belongs to.
     */
    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }

    /**
     * Get the catalog item reference.
     */
    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }

    /**
     * Calculate the subtotal for this item.
     */
    public function calculateSubtotal(): void
    {
        $lineTotal = $this->quantity * $this->unit_price;

        if ($this->discount_rate > 0) {
            $this->discount_amount = $lineTotal * ($this->discount_rate / 100);
        } else {
            $this->discount_amount = 0;
        }

        $this->subtotal = $lineTotal - $this->discount_amount;
    }

    /**
     * Create from catalog item.
     */
    public static function createFromCatalogItem(CatalogItem $catalogItem, float $quantity = 1): array
    {
        return [
            'catalog_item_id' => $catalogItem->id,
            'name' => $catalogItem->name,
            'sku' => $catalogItem->sku,
            'description' => $catalogItem->description,
            'quantity' => $quantity,
            'unit_type' => $catalogItem->unit_type,
            'unit_price' => $catalogItem->selling_price,
            'is_taxable' => $catalogItem->is_taxable,
            'discount_rate' => 0,
            'discount_amount' => 0,
        ];
    }

    /**
     * Get formatted line total.
     */
    public function getLineTotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Get formatted subtotal.
     */
    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 2);
    }
}

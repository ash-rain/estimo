<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientPricing extends Model
{
    use HasFactory;

    protected $table = 'client_pricing';

    protected $fillable = [
        'client_id',
        'catalog_item_id',
        'custom_price',
        'price_type',
        'valid_from',
        'valid_until',
        'notes',
        'active',
    ];

    protected $casts = [
        'custom_price' => 'decimal:2',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'active' => 'boolean',
    ];

    /**
     * Get the client associated with this pricing.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the catalog item associated with this pricing.
     */
    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }

    /**
     * Scope to get only active pricing rules.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope to filter by client.
     */
    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    /**
     * Scope to filter by catalog item.
     */
    public function scopeForItem($query, int $catalogItemId)
    {
        return $query->where('catalog_item_id', $catalogItemId);
    }

    /**
     * Check if this pricing is currently valid.
     */
    public function isValid(): bool
    {
        if (!$this->active) {
            return false;
        }

        $now = now();

        if ($this->valid_from && $now->lt($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->gt($this->valid_until)) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the effective price based on the pricing type.
     */
    public function calculatePrice(float $basePrice): float
    {
        if (!$this->isValid()) {
            return $basePrice;
        }

        switch ($this->price_type) {
            case 'fixed':
                return (float) $this->custom_price;

            case 'discount_percentage':
                $discount = $basePrice * ($this->custom_price / 100);
                return max(0, $basePrice - $discount);

            case 'markup_percentage':
                $markup = $basePrice * ($this->custom_price / 100);
                return $basePrice + $markup;

            default:
                return $basePrice;
        }
    }

    /**
     * Get a formatted display of the pricing rule.
     */
    public function getFormattedPriceAttribute(): string
    {
        switch ($this->price_type) {
            case 'fixed':
                return '$' . number_format($this->custom_price, 2);

            case 'discount_percentage':
                return $this->custom_price . '% discount';

            case 'markup_percentage':
                return $this->custom_price . '% markup';

            default:
                return '$' . number_format($this->custom_price, 2);
        }
    }
}

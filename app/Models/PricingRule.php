<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'type',
        'value_type',
        'value',
        'conditions',
        'priority',
        'active',
        'applies_to',
    ];

    protected $casts = [
        'conditions' => 'array',
        'value' => 'decimal:2',
        'priority' => 'integer',
        'active' => 'boolean',
    ];

    /**
     * Scope to get only active rules.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope to order by priority (highest first).
     */
    public function scopeByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Check if this rule applies to a specific catalog item.
     */
    public function appliesToItem(int $catalogItemId, ?int $categoryId = null): bool
    {
        if ($this->applies_to === 'all') {
            return true;
        }

        $conditions = $this->conditions ?? [];

        if ($this->applies_to === 'items' && isset($conditions['item_ids'])) {
            return in_array($catalogItemId, $conditions['item_ids']);
        }

        if ($this->applies_to === 'categories' && isset($conditions['category_ids']) && $categoryId) {
            return in_array($categoryId, $conditions['category_ids']);
        }

        return false;
    }

    /**
     * Check if this rule applies to a specific client.
     */
    public function appliesToClient(?int $clientId): bool
    {
        if ($this->applies_to !== 'clients') {
            return true;
        }

        $conditions = $this->conditions ?? [];

        if (isset($conditions['client_ids']) && $clientId) {
            return in_array($clientId, $conditions['client_ids']);
        }

        return false;
    }

    /**
     * Check if this rule applies based on quantity.
     */
    public function appliesToQuantity(float $quantity): bool
    {
        $conditions = $this->conditions ?? [];

        $minQty = $conditions['min_quantity'] ?? null;
        $maxQty = $conditions['max_quantity'] ?? null;

        if ($minQty !== null && $quantity < $minQty) {
            return false;
        }

        if ($maxQty !== null && $quantity > $maxQty) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the adjusted price based on this rule.
     */
    public function calculatePrice(float $basePrice, float $quantity = 1): float
    {
        if (!$this->active) {
            return $basePrice;
        }

        switch ($this->type) {
            case 'discount':
                return $this->applyDiscount($basePrice, $quantity);

            case 'markup':
                return $this->applyMarkup($basePrice, $quantity);

            case 'fixed_price':
                return (float) $this->value;

            case 'volume_discount':
                return $this->applyVolumeDiscount($basePrice, $quantity);

            default:
                return $basePrice;
        }
    }

    /**
     * Apply discount to price.
     */
    protected function applyDiscount(float $basePrice, float $quantity): float
    {
        if ($this->value_type === 'percentage') {
            $discount = $basePrice * ($this->value / 100);
            return max(0, $basePrice - $discount);
        }

        // Fixed discount
        return max(0, $basePrice - $this->value);
    }

    /**
     * Apply markup to price.
     */
    protected function applyMarkup(float $basePrice, float $quantity): float
    {
        if ($this->value_type === 'percentage') {
            $markup = $basePrice * ($this->value / 100);
            return $basePrice + $markup;
        }

        // Fixed markup
        return $basePrice + $this->value;
    }

    /**
     * Apply volume-based discount.
     */
    protected function applyVolumeDiscount(float $basePrice, float $quantity): float
    {
        $conditions = $this->conditions ?? [];
        $tiers = $conditions['tiers'] ?? [];

        // Find the applicable tier based on quantity
        $applicableTier = null;
        foreach ($tiers as $tier) {
            $minQty = $tier['min_quantity'] ?? 0;
            $maxQty = $tier['max_quantity'] ?? PHP_FLOAT_MAX;

            if ($quantity >= $minQty && $quantity <= $maxQty) {
                $applicableTier = $tier;
                break;
            }
        }

        if (!$applicableTier) {
            return $basePrice;
        }

        $discountValue = $applicableTier['discount'] ?? 0;
        $discountType = $applicableTier['discount_type'] ?? 'percentage';

        if ($discountType === 'percentage') {
            return $basePrice * (1 - ($discountValue / 100));
        }

        return max(0, $basePrice - $discountValue);
    }

    /**
     * Get a human-readable description of what this rule does.
     */
    public function getDescriptionAttribute(): string
    {
        $desc = '';

        switch ($this->type) {
            case 'discount':
                $desc = $this->value_type === 'percentage'
                    ? "{$this->value}% discount"
                    : "\${$this->value} discount";
                break;

            case 'markup':
                $desc = $this->value_type === 'percentage'
                    ? "{$this->value}% markup"
                    : "\${$this->value} markup";
                break;

            case 'fixed_price':
                $desc = "Fixed price of \${$this->value}";
                break;

            case 'volume_discount':
                $desc = 'Volume-based discount';
                break;
        }

        return $desc;
    }
}

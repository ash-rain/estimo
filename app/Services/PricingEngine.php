<?php

namespace App\Services;

use App\Models\CatalogItem;
use App\Models\ClientPricing;
use App\Models\PricingRule;

class PricingEngine
{
    /**
     * Calculate the effective price for a catalog item.
     *
     * Priority order:
     * 1. Client-specific pricing (if exists and valid)
     * 2. Volume pricing from catalog item (if applicable)
     * 3. Pricing rules (by priority)
     * 4. Base catalog price
     */
    public function calculatePrice(
        CatalogItem $catalogItem,
        float $quantity = 1,
        ?int $clientId = null
    ): array {
        $basePrice = (float) $catalogItem->price;
        $effectivePrice = $basePrice;
        $appliedRule = null;
        $priceSource = 'base';

        // 1. Check for client-specific pricing first (highest priority)
        if ($clientId) {
            $clientPricing = $this->getClientPricing($catalogItem->id, $clientId);
            if ($clientPricing) {
                $effectivePrice = $clientPricing->calculatePrice($basePrice);
                $appliedRule = $clientPricing;
                $priceSource = 'client_pricing';
                return $this->buildResult($basePrice, $effectivePrice, $quantity, $priceSource, $appliedRule);
            }
        }

        // 2. Check for volume pricing on the catalog item
        if ($catalogItem->pricing_model === 'volume' && $catalogItem->volume_pricing) {
            $volumePrice = $this->applyVolumePricing($basePrice, $quantity, $catalogItem->volume_pricing);
            if ($volumePrice !== $basePrice) {
                $effectivePrice = $volumePrice;
                $priceSource = 'volume_pricing';
                return $this->buildResult($basePrice, $effectivePrice, $quantity, $priceSource, null);
            }
        }

        // 3. Check for applicable pricing rules
        $pricingRule = $this->findApplicablePricingRule($catalogItem, $quantity, $clientId);
        if ($pricingRule) {
            $effectivePrice = $pricingRule->calculatePrice($basePrice, $quantity);
            $appliedRule = $pricingRule;
            $priceSource = 'pricing_rule';
        }

        return $this->buildResult($basePrice, $effectivePrice, $quantity, $priceSource, $appliedRule);
    }

    /**
     * Get client-specific pricing if it exists and is valid.
     */
    protected function getClientPricing(int $catalogItemId, int $clientId): ?ClientPricing
    {
        $pricing = ClientPricing::active()
            ->forClient($clientId)
            ->forItem($catalogItemId)
            ->first();

        return $pricing && $pricing->isValid() ? $pricing : null;
    }

    /**
     * Apply volume pricing tiers.
     */
    protected function applyVolumePricing(float $basePrice, float $quantity, array $volumePricing): float
    {
        $tiers = $volumePricing['tiers'] ?? [];

        // Find the applicable tier based on quantity
        foreach ($tiers as $tier) {
            $minQty = $tier['min_quantity'] ?? 0;
            $maxQty = $tier['max_quantity'] ?? PHP_FLOAT_MAX;

            if ($quantity >= $minQty && $quantity <= $maxQty) {
                $tierPrice = $tier['price'] ?? null;
                $tierDiscount = $tier['discount'] ?? null;
                $tierDiscountType = $tier['discount_type'] ?? 'percentage';

                // Use tier price if set
                if ($tierPrice !== null) {
                    return (float) $tierPrice;
                }

                // Apply tier discount
                if ($tierDiscount !== null) {
                    if ($tierDiscountType === 'percentage') {
                        return $basePrice * (1 - ($tierDiscount / 100));
                    }

                    return max(0, $basePrice - $tierDiscount);
                }
            }
        }

        return $basePrice;
    }

    /**
     * Find the first applicable pricing rule.
     */
    protected function findApplicablePricingRule(
        CatalogItem $catalogItem,
        float $quantity,
        ?int $clientId
    ): ?PricingRule {
        $rules = PricingRule::active()
            ->byPriority()
            ->get();

        foreach ($rules as $rule) {
            if ($this->ruleApplies($rule, $catalogItem, $quantity, $clientId)) {
                return $rule;
            }
        }

        return null;
    }

    /**
     * Check if a pricing rule applies to the current context.
     */
    protected function ruleApplies(
        PricingRule $rule,
        CatalogItem $catalogItem,
        float $quantity,
        ?int $clientId
    ): bool {
        // Check if rule applies to this item
        if (!$rule->appliesToItem($catalogItem->id, $catalogItem->category_id)) {
            return false;
        }

        // Check if rule applies to this client
        if (!$rule->appliesToClient($clientId)) {
            return false;
        }

        // Check if rule applies to this quantity
        if (!$rule->appliesToQuantity($quantity)) {
            return false;
        }

        return true;
    }

    /**
     * Build the result array with pricing details.
     */
    protected function buildResult(
        float $basePrice,
        float $effectivePrice,
        float $quantity,
        string $source,
        $appliedRule
    ): array {
        $discount = $basePrice - $effectivePrice;
        $discountPercentage = $basePrice > 0 ? ($discount / $basePrice) * 100 : 0;
        $totalPrice = $effectivePrice * $quantity;

        return [
            'base_price' => round($basePrice, 2),
            'effective_price' => round($effectivePrice, 2),
            'quantity' => $quantity,
            'discount_amount' => round($discount, 2),
            'discount_percentage' => round($discountPercentage, 2),
            'total_price' => round($totalPrice, 2),
            'price_source' => $source,
            'applied_rule' => $appliedRule,
        ];
    }

    /**
     * Calculate prices for multiple items (bulk).
     */
    public function calculateBulkPrices(array $items, ?int $clientId = null): array
    {
        $results = [];

        foreach ($items as $item) {
            $catalogItem = $item['catalog_item'] ?? null;
            $quantity = $item['quantity'] ?? 1;

            if ($catalogItem instanceof CatalogItem) {
                $results[] = array_merge(
                    ['item_id' => $catalogItem->id],
                    $this->calculatePrice($catalogItem, $quantity, $clientId)
                );
            }
        }

        return $results;
    }

    /**
     * Get all applicable pricing rules for a catalog item.
     */
    public function getApplicableRules(CatalogItem $catalogItem, ?int $clientId = null): array
    {
        $rules = PricingRule::active()
            ->byPriority()
            ->get()
            ->filter(function ($rule) use ($catalogItem, $clientId) {
                return $rule->appliesToItem($catalogItem->id, $catalogItem->category_id)
                    && $rule->appliesToClient($clientId);
            });

        return $rules->all();
    }
}

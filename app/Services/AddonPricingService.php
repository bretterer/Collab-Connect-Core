<?php

namespace App\Services;

use App\Enums\AccountType;
use App\Models\AddonPriceMapping;
use App\Models\StripePrice;
use Illuminate\Database\Eloquent\Collection;

class AddonPricingService
{
    /**
     * Get available addon prices for a specific credit key and account type.
     */
    public function getAddonPricesForCreditKey(
        string $creditKey,
        AccountType $accountType
    ): Collection {
        return AddonPriceMapping::query()
            ->with('stripePrice.product')
            ->active()
            ->forCreditKey($creditKey)
            ->forAccountType($accountType)
            ->whereHas('stripePrice', fn ($q) => $q->where('active', true))
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get the primary/default addon price for a credit key.
     * Returns the first active price (by sort order).
     */
    public function getPrimaryAddonPrice(
        string $creditKey,
        AccountType $accountType
    ): ?AddonPriceMapping {
        return $this->getAddonPricesForCreditKey($creditKey, $accountType)->first();
    }

    /**
     * Get all purchasable credit addons for an account type.
     */
    public function getAllPurchasableAddons(AccountType $accountType): Collection
    {
        return AddonPriceMapping::query()
            ->with('stripePrice.product')
            ->active()
            ->forAccountType($accountType)
            ->whereHas('stripePrice', fn ($q) => $q->where('active', true))
            ->orderBy('credit_key')
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get available one-time prices that could be mapped.
     */
    public function getAvailableOneTimePrices(): Collection
    {
        return StripePrice::query()
            ->with('product')
            ->where('active', true)
            ->where('type', 'one_time')
            ->orderBy('product_name')
            ->orderBy('unit_amount')
            ->get();
    }

    /**
     * Create a new addon price mapping.
     */
    public function createMapping(
        StripePrice $price,
        string $creditKey,
        int $creditsGranted,
        string $accountType,
        array $options = []
    ): AddonPriceMapping {
        return AddonPriceMapping::create([
            'stripe_price_id' => $price->id,
            'credit_key' => $creditKey,
            'credits_granted' => $creditsGranted,
            'account_type' => $accountType,
            'is_active' => $options['is_active'] ?? true,
            'sort_order' => $options['sort_order'] ?? 0,
            'display_name' => $options['display_name'] ?? null,
        ]);
    }
}

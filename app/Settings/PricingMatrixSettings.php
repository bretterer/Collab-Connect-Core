<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PricingMatrixSettings extends Settings
{
    /**
     * Feature categories for business plans.
     * Structure: [['key' => string, 'label' => string, 'features' => [...]]]
     */
    public array $business_categories;

    /**
     * Feature categories for influencer plans.
     * Structure: [['key' => string, 'label' => string, 'features' => [...]]]
     */
    public array $influencer_categories;

    /**
     * The Stripe price ID that should be highlighted as "Most Popular" for business plans.
     */
    public ?string $highlighted_business_price_id;

    /**
     * The Stripe price ID that should be highlighted as "Most Popular" for influencer plans.
     */
    public ?string $highlighted_influencer_price_id;

    public static function group(): string
    {
        return 'pricing_matrix';
    }

    /**
     * Get all feature keys for a given account type.
     *
     * @return array<string>
     */
    public function getFeatureKeys(string $accountType): array
    {
        $categories = $accountType === 'business' ? $this->business_categories : $this->influencer_categories;
        $keys = [];

        foreach ($categories as $category) {
            foreach ($category['features'] ?? [] as $feature) {
                $keys[] = $feature['key'];
            }
        }

        return $keys;
    }

    /**
     * Get a flat list of all features for a given account type.
     */
    public function getAllFeatures(string $accountType): array
    {
        $categories = $accountType === 'business' ? $this->business_categories : $this->influencer_categories;
        $features = [];

        foreach ($categories as $category) {
            foreach ($category['features'] ?? [] as $feature) {
                $features[] = [
                    'key' => $feature['key'],
                    'label' => $feature['label'],
                    'type' => $feature['type'],
                    'category' => $category['label'],
                    'category_key' => $category['key'],
                ];
            }
        }

        return $features;
    }

    /**
     * Check if a price should be highlighted.
     */
    public function isHighlighted(string $priceId, string $accountType): bool
    {
        if ($accountType === 'business') {
            return $this->highlighted_business_price_id === $priceId;
        }

        return $this->highlighted_influencer_price_id === $priceId;
    }
}

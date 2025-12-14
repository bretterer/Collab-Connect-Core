<?php

namespace App\Models\Traits;

use App\Models\StripePrice;

/**
 * Provides subscription tier checking functionality for Billable models.
 *
 * This trait should be used on models that use Laravel Cashier's Billable trait.
 * It provides methods to check subscription tiers and feature access based on
 * the configuration in config/collabconnect.php.
 */
trait HasSubscriptionTier
{
    /**
     * Get the account type key for tier configuration.
     * Override this in the model if needed.
     */
    public function getAccountTypeKey(): string
    {
        return strtolower(class_basename($this));
    }

    /**
     * Get the tier configuration for this account type.
     *
     * @return array<string, mixed>|null
     */
    protected function getTierConfig(): ?array
    {
        return config('collabconnect.subscription_tiers.'.$this->getAccountTypeKey());
    }

    /**
     * Get the current subscription tier name.
     * Returns null if not subscribed or tier cannot be determined.
     */
    public function getSubscriptionTier(): ?string
    {
        if (! $this->subscribed('default')) {
            return null;
        }

        $subscription = $this->subscription('default');

        if (! $subscription) {
            return null;
        }

        // Get the price lookup key from the subscription
        $price = StripePrice::where('stripe_id', $subscription->stripe_price)->first();

        if (! $price || ! $price->lookup_key) {
            return null;
        }

        $config = $this->getTierConfig();

        if (! $config) {
            return null;
        }

        return $config['lookup_keys'][$price->lookup_key] ?? null;
    }

    /**
     * Check if the user is on a specific subscription tier.
     */
    public function isOnTier(string $tier): bool
    {
        return $this->getSubscriptionTier() === $tier;
    }

    /**
     * Check if the user is on the specified tier or higher.
     */
    public function isOnTierOrAbove(string $tier): bool
    {
        $currentTier = $this->getSubscriptionTier();

        if (! $currentTier) {
            return false;
        }

        $config = $this->getTierConfig();

        if (! $config || ! isset($config['hierarchy'])) {
            return false;
        }

        $hierarchy = $config['hierarchy'];
        $currentIndex = array_search($currentTier, $hierarchy);
        $targetIndex = array_search($tier, $hierarchy);

        if ($currentIndex === false || $targetIndex === false) {
            return false;
        }

        return $currentIndex >= $targetIndex;
    }

    /**
     * Get the feature limit/access for a specific feature.
     * Returns the configured value (int, bool, or null if not found).
     */
    public function getFeatureLimit(string $feature): int|bool|null
    {
        $tier = $this->getSubscriptionTier();

        if (! $tier) {
            return null;
        }

        $config = $this->getTierConfig();

        if (! $config || ! isset($config['features'][$tier])) {
            return null;
        }

        return $config['features'][$tier][$feature] ?? null;
    }

    /**
     * Check if the user has access to a specific feature.
     * For boolean features, returns the boolean value.
     * For numeric limits, returns true if limit > 0 or is unlimited (-1).
     */
    public function hasFeatureAccess(string $feature): bool
    {
        $limit = $this->getFeatureLimit($feature);

        if ($limit === null) {
            return false;
        }

        if (is_bool($limit)) {
            return $limit;
        }

        // Numeric limit: -1 means unlimited, any positive number means access
        return $limit === -1 || $limit > 0;
    }

    /**
     * Check if the user can add more items within their feature limit.
     * Useful for checking if more links, campaigns, etc. can be added.
     */
    public function canAddMoreItems(string $feature, int $currentCount): bool
    {
        $limit = $this->getFeatureLimit($feature);

        if ($limit === null) {
            return false;
        }

        if (is_bool($limit)) {
            return $limit;
        }

        // -1 means unlimited
        if ($limit === -1) {
            return true;
        }

        return $currentCount < $limit;
    }

    /**
     * Get the tier required for a specific feature.
     * Returns the lowest tier that has access to the feature.
     */
    public function getTierRequiredFor(string $feature): ?string
    {
        $config = $this->getTierConfig();

        if (! $config || ! isset($config['features'])) {
            return null;
        }

        foreach ($config['hierarchy'] as $tier) {
            $featureValue = $config['features'][$tier][$feature] ?? null;

            if ($featureValue === true || (is_int($featureValue) && $featureValue !== 0)) {
                return $tier;
            }
        }

        return null;
    }

    /**
     * Get display name for a tier.
     */
    public function getTierDisplayName(?string $tier = null): string
    {
        $tier = $tier ?? $this->getSubscriptionTier();

        if (! $tier) {
            return 'Free';
        }

        return ucfirst($tier);
    }

    /**
     * Get the next tier upgrade option.
     * Returns null if already on highest tier or not subscribed.
     */
    public function getNextTier(): ?string
    {
        $currentTier = $this->getSubscriptionTier();

        if (! $currentTier) {
            // Not subscribed, return the lowest tier
            $config = $this->getTierConfig();

            return $config['hierarchy'][0] ?? null;
        }

        $config = $this->getTierConfig();

        if (! $config || ! isset($config['hierarchy'])) {
            return null;
        }

        $hierarchy = $config['hierarchy'];
        $currentIndex = array_search($currentTier, $hierarchy);

        if ($currentIndex === false || $currentIndex >= count($hierarchy) - 1) {
            return null; // Already on highest tier
        }

        return $hierarchy[$currentIndex + 1];
    }

    /**
     * Get the Stripe price lookup key for a specific tier.
     */
    public function getPriceLookupKeyForTier(string $tier): ?string
    {
        $config = $this->getTierConfig();

        if (! $config || ! isset($config['lookup_keys'])) {
            return null;
        }

        return array_search($tier, $config['lookup_keys']) ?: null;
    }
}

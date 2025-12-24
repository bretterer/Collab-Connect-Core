<?php

namespace App\Subscription;

use App\Enums\AccountType;

/**
 * Central definition of all Stripe price metadata keys for subscription limits.
 *
 * This class serves as the single source of truth for metadata keys used
 * in Stripe prices to control subscription-based limits and credits.
 */
class SubscriptionMetadataSchema
{
    // Influencer credit/limit keys
    public const ACTIVE_APPLICATIONS_LIMIT = 'active_applications_limit';

    // Shared credit/limit keys (used by both influencers and businesses)
    public const COLLABORATION_LIMIT = 'collaboration_limit';

    public const PROFILE_PROMOTION_CREDITS = 'profile_promotion_credits';

    // Business credit/limit keys
    public const CAMPAIGNS_PUBLISHED_LIMIT = 'campaigns_published_limit';

    public const CAMPAIGN_BOOST_CREDITS = 'campaign_boost_credits';

    public const TEAM_MEMBER_LIMIT = 'team_member_limit';

    /**
     * Special value indicating unlimited (no cap).
     */
    public const UNLIMITED = -1;

    /**
     * Get all metadata keys for influencer subscriptions.
     *
     * @return array<string>
     */
    public static function getInfluencerKeys(): array
    {
        return [
            self::ACTIVE_APPLICATIONS_LIMIT,
            self::COLLABORATION_LIMIT,
            self::PROFILE_PROMOTION_CREDITS,
        ];
    }

    /**
     * Get all metadata keys for business subscriptions.
     *
     * @return array<string>
     */
    public static function getBusinessKeys(): array
    {
        return [
            self::CAMPAIGNS_PUBLISHED_LIMIT,
            self::COLLABORATION_LIMIT,
            self::CAMPAIGN_BOOST_CREDITS,
            self::PROFILE_PROMOTION_CREDITS,
            self::TEAM_MEMBER_LIMIT,
        ];
    }

    /**
     * Get all metadata keys regardless of account type.
     *
     * @return array<string>
     */
    public static function getAllKeys(): array
    {
        return array_unique(array_merge(
            self::getInfluencerKeys(),
            self::getBusinessKeys()
        ));
    }

    /**
     * Get keys that represent consumable credits (reset each billing cycle).
     *
     * @return array<string>
     */
    public static function getCreditKeys(): array
    {
        return [
            self::ACTIVE_APPLICATIONS_LIMIT,
            self::CAMPAIGNS_PUBLISHED_LIMIT,
        ];
    }

    /**
     * Get keys that represent one-time grants (given at signup, never reset).
     *
     * @return array<string>
     */
    public static function getOneTimeGrantKeys(): array
    {
        return [
            self::CAMPAIGN_BOOST_CREDITS,
            self::PROFILE_PROMOTION_CREDITS,
        ];
    }

    /**
     * Get keys that represent hard limits (not consumable).
     *
     * @return array<string>
     */
    public static function getLimitKeys(): array
    {
        return [
            self::COLLABORATION_LIMIT,
            self::TEAM_MEMBER_LIMIT,
        ];
    }

    /**
     * Get default metadata values for a given account type.
     * All values default to 0 to force explicit configuration in Stripe.
     *
     * @return array<string, int>
     */
    public static function getDefaultsForAccountType(AccountType $type): array
    {
        $keys = match ($type) {
            AccountType::INFLUENCER => self::getInfluencerKeys(),
            AccountType::BUSINESS => self::getBusinessKeys(),
            default => [],
        };

        $defaults = [];
        foreach ($keys as $key) {
            $defaults[$key] = 0;
        }

        return $defaults;
    }

    /**
     * Get keys appropriate for a given account type.
     *
     * @return array<string>
     */
    public static function getKeysForAccountType(AccountType $type): array
    {
        return match ($type) {
            AccountType::INFLUENCER => self::getInfluencerKeys(),
            AccountType::BUSINESS => self::getBusinessKeys(),
            default => [],
        };
    }

    /**
     * Get human-readable labels for each metadata key.
     *
     * @return array<string, string>
     */
    public static function getLabels(): array
    {
        return [
            self::ACTIVE_APPLICATIONS_LIMIT => 'Application Credits per Billing Cycle',
            self::COLLABORATION_LIMIT => 'Max Concurrent Collaborations',
            self::CAMPAIGNS_PUBLISHED_LIMIT => 'Campaign Publish Credits per Billing Cycle',
            self::CAMPAIGN_BOOST_CREDITS => 'Campaign Boost Credits (One-Time Grant)',
            self::PROFILE_PROMOTION_CREDITS => 'Profile Promotion Credits (One-Time Grant)',
            self::TEAM_MEMBER_LIMIT => 'Additional Team Members Allowed',
        ];
    }

    /**
     * Get description for a metadata key.
     */
    public static function getDescription(string $key): string
    {
        return match ($key) {
            self::ACTIVE_APPLICATIONS_LIMIT => 'Number of campaign applications an influencer can submit per billing cycle. Use -1 for unlimited.',
            self::COLLABORATION_LIMIT => 'Maximum number of active collaborations at any time. Use -1 for unlimited.',
            self::CAMPAIGNS_PUBLISHED_LIMIT => 'Number of campaigns a business can publish per billing cycle. Use -1 for unlimited.',
            self::CAMPAIGN_BOOST_CREDITS => 'One-time grant of campaign boosts at signup. Boosted campaigns get +5% match score. Does not reset.',
            self::PROFILE_PROMOTION_CREDITS => 'One-time grant of profile promotions at signup. Does not reset.',
            self::TEAM_MEMBER_LIMIT => 'Number of additional team members that can be invited (excludes owner). Use -1 for unlimited.',
            default => '',
        };
    }

    /**
     * Check if a value represents unlimited.
     */
    public static function isUnlimited(int $value): bool
    {
        return $value === self::UNLIMITED;
    }

    /**
     * Check if a key represents a credit (consumable) vs a hard limit.
     * Includes both renewing credits and one-time grants.
     */
    public static function isCredit(string $key): bool
    {
        return in_array($key, self::getCreditKeys(), true)
            || in_array($key, self::getOneTimeGrantKeys(), true);
    }

    /**
     * Check if a key represents a one-time grant (not renewing).
     */
    public static function isOneTimeGrant(string $key): bool
    {
        return in_array($key, self::getOneTimeGrantKeys(), true);
    }
}

<?php

namespace App\Enums;

enum CollaborationGoal: string
{
    case BRAND_AWARENESS = 'brand_awareness';
    case DRIVE_FOOT_TRAFFIC = 'drive_foot_traffic';
    case PROMOTE_EVENTS = 'promote_events';
    case SEASONAL_CAMPAIGNS = 'seasonal_campaigns';
    case PRODUCT_LAUNCHES = 'product_launches';
    case REPUTATION_MANAGEMENT = 'reputation_management';
    case CUSTOMER_ACQUISITION = 'customer_acquisition';
    case SOCIAL_MEDIA_GROWTH = 'social_media_growth';
    case MONETARY_COMPENSATION = 'monetary_compensation';
    case BARTER_TRADE = 'barter';
    case FREE_PRODUCT = 'free_product';
    case DISCOUNT_CODES = 'discounting';
    case LONG_TERM_PARTNERSHIPS = 'long_term_partnerships';
    case EVENT_INVITATIONS = 'event_invitations';

    public function label(): string
    {
        return match ($this) {
            self::BRAND_AWARENESS => 'Increase brand awareness in local community',
            self::DRIVE_FOOT_TRAFFIC => 'Drive foot traffic to physical locations',
            self::PROMOTE_EVENTS => 'Promote special events and grand openings',
            self::SEASONAL_CAMPAIGNS => 'Run seasonal marketing campaigns',
            self::PRODUCT_LAUNCHES => 'Launch new products or services',
            self::REPUTATION_MANAGEMENT => 'Build and maintain online reputation',
            self::CUSTOMER_ACQUISITION => 'Acquire new customers',
            self::SOCIAL_MEDIA_GROWTH => 'Grow social media following',
            self::MONETARY_COMPENSATION => 'Monetary compensation',
            self::BARTER_TRADE => 'Barter/trade',
            self::FREE_PRODUCT => 'Free products',
            self::DISCOUNT_CODES => 'Discount codes for followers',
            self::LONG_TERM_PARTNERSHIPS => 'Long-term partnerships',
            self::EVENT_INVITATIONS => 'Event invitations',
        };
    }

    /**
     * Get collaboration goals relevant to businesses
     */
    public static function forBusinesses(): array
    {
        return [
            self::BRAND_AWARENESS,
            self::DRIVE_FOOT_TRAFFIC,
            self::PROMOTE_EVENTS,
            self::SEASONAL_CAMPAIGNS,
            self::PRODUCT_LAUNCHES,
            self::REPUTATION_MANAGEMENT,
            self::CUSTOMER_ACQUISITION,
            self::SOCIAL_MEDIA_GROWTH,
        ];
    }

    /**
     * Get collaboration preferences relevant to influencers
     */
    public static function forInfluencers(): array
    {
        return [
            self::MONETARY_COMPENSATION,
            self::BARTER_TRADE,
            self::FREE_PRODUCT,
            self::DISCOUNT_CODES,
            self::LONG_TERM_PARTNERSHIPS,
            self::EVENT_INVITATIONS,
        ];
    }

    /**
     * Get all values as associative array for form options
     */
    public static function toOptions(?array $cases = null): array
    {
        $cases = $cases ?? self::cases();

        return array_combine(
            array_map(fn ($case) => $case->value, $cases),
            array_map(fn ($case) => $case->label(), $cases)
        );
    }
}

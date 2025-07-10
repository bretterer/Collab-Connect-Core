<?php

namespace App\Enums;

enum CampaignType: string
{
    case SPONSORED_POSTS = 'sponsored_posts';
    case PRODUCT_REVIEWS = 'product_reviews';
    case EVENT_COVERAGE = 'event_coverage';
    case GIVEAWAYS = 'giveaways';
    case BRAND_PARTNERSHIPS = 'brand_partnerships';
    case SEASONAL_CONTENT = 'seasonal_content';
    case BEHIND_SCENES = 'behind_scenes';
    case USER_GENERATED = 'user_generated';

    public function label(): string
    {
        return match ($this) {
            self::SPONSORED_POSTS => 'Sponsored social media posts',
            self::PRODUCT_REVIEWS => 'Product or service reviews',
            self::EVENT_COVERAGE => 'Event coverage and live posting',
            self::GIVEAWAYS => 'Giveaways and contests',
            self::BRAND_PARTNERSHIPS => 'Long-term brand partnerships',
            self::SEASONAL_CONTENT => 'Seasonal and holiday content',
            self::BEHIND_SCENES => 'Behind-the-scenes content',
            self::USER_GENERATED => 'User-generated content campaigns',
        };
    }

    /**
     * Get all values as associative array for form options
     */
    public static function toOptions(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }

    /**
     * Get campaign types that work well for businesses
     */
    public static function forBusinesses(): array
    {
        return self::cases(); // All campaign types are relevant for businesses
    }

    /**
     * Get campaign types that influencers typically work with
     */
    public static function forInfluencers(): array
    {
        return self::cases(); // All campaign types are relevant for influencers
    }
}

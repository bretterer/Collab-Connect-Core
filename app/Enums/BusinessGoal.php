<?php

namespace App\Enums;

enum BusinessGoal: string
{
    case BRAND_AWARENESS = 'brand_awareness';
    case PRODUCT_PROMOTION = 'product_promotion';
    case GROWTH_SCALING = 'growth_scaling';
    case NEW_MARKET_ENTRY = 'new_market_entry';
    case COMMUNITY_BUILDING = 'community_building';
    case CUSTOMER_RETENTION = 'customer_retention';

    public function label(): string
    {
        return match ($this) {
            self::BRAND_AWARENESS => 'Brand Awareness',
            self::PRODUCT_PROMOTION => 'Product Promotion',
            self::GROWTH_SCALING => 'Growth & Scaling',
            self::NEW_MARKET_ENTRY => 'New Market Entry',
            self::COMMUNITY_BUILDING => 'Community Building',
            self::CUSTOMER_RETENTION => 'Customer Retention',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::BRAND_AWARENESS => 'Increase visibility and awareness of your brand.',
            self::PRODUCT_PROMOTION => 'Promote specific products to drive sales.',
            self::GROWTH_SCALING => 'Strategies focused on business growth and scaling.',
            self::NEW_MARKET_ENTRY => 'Entering new markets with tailored strategies.',
            self::COMMUNITY_BUILDING => 'Fostering a community around your brand.',
            self::CUSTOMER_RETENTION => 'Strategies aimed at retaining existing customers.',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::BRAND_AWARENESS => '📢',
            self::PRODUCT_PROMOTION => '🛍️',
            self::GROWTH_SCALING => '📈',
            self::NEW_MARKET_ENTRY => '🌍',
            self::COMMUNITY_BUILDING => '🤝',
            self::CUSTOMER_RETENTION => '🔄',
        };
    }
}

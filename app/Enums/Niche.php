<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum Niche: string
{
    use HasFormOptions;

    case FOOD = 'food';
    case FASHION = 'fashion';
    case BEAUTY = 'beauty';
    case TRAVEL = 'travel';
    case LOCAL_EVENTS = 'local_events';
    case FITNESS = 'fitness';
    case LIFESTYLE = 'lifestyle';
    case HOME = 'home';
    case FAMILY = 'family';
    case BUSINESS = 'business';
    case ENTERTAINMENT = 'entertainment';
    case AUTOMOTIVE = 'automotive';
    case TECHNOLOGY = 'technology';
    case PETS = 'pets';
    case HEALTH = 'health';
    case EDUCATION = 'education';
    case FINANCE = 'finance';
    case REAL_ESTATE = 'real_estate';
    case RETAIL = 'retail';
    case HOSPITALITY = 'hospitality';

    public function label(): string
    {
        return match ($this) {
            self::FOOD => 'Food & Dining',
            self::FASHION => 'Fashion & Style',
            self::BEAUTY => 'Beauty & Skincare',
            self::TRAVEL => 'Travel & Tourism',
            self::LOCAL_EVENTS => 'Local Events',
            self::FITNESS => 'Fitness & Health',
            self::LIFESTYLE => 'Lifestyle',
            self::HOME => 'Home & Decor',
            self::FAMILY => 'Family & Parenting',
            self::BUSINESS => 'Business & Professional',
            self::ENTERTAINMENT => 'Entertainment',
            self::AUTOMOTIVE => 'Automotive',
            self::TECHNOLOGY => 'Technology',
            self::PETS => 'Pets & Animals',
            self::HEALTH => 'Health & Wellness',
            self::EDUCATION => 'Education & Learning',
            self::FINANCE => 'Finance & Investment',
            self::REAL_ESTATE => 'Real Estate',
            self::RETAIL => 'Retail & Shopping',
            self::HOSPITALITY => 'Hospitality & Service',
        };
    }

    /**
     * Get niches most relevant for business industries
     */
    public static function forBusinesses(): array
    {
        return [
            self::FOOD,
            self::FASHION,
            self::BEAUTY,
            self::FITNESS,
            self::HOME,
            self::AUTOMOTIVE,
            self::TECHNOLOGY,
            self::HEALTH,
            self::EDUCATION,
            self::FINANCE,
            self::REAL_ESTATE,
            self::RETAIL,
            self::HOSPITALITY,
        ];
    }

    /**
     * Get niches most relevant for influencer content
     */
    public static function forInfluencers(): array
    {
        return [
            self::FOOD,
            self::FASHION,
            self::BEAUTY,
            self::TRAVEL,
            self::LOCAL_EVENTS,
            self::FITNESS,
            self::LIFESTYLE,
            self::HOME,
            self::FAMILY,
            self::BUSINESS,
            self::ENTERTAINMENT,
            self::AUTOMOTIVE,
            self::TECHNOLOGY,
            self::PETS,
        ];
    }
}

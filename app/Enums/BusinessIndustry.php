<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum BusinessIndustry: string
{
    use HasFormOptions;

    case RETAIL = 'retail';
    case FOOD_BEVERAGE = 'food-beverage';
    case TECHNOLOGY = 'technology';
    case HEALTHCARE = 'healthcare';
    case BEAUTY_COSMETICS = 'beauty-cosmetics';
    case FITNESS_WELLNESS = 'fitness-wellness';
    case FASHION_APPAREL = 'fashion-apparel';
    case TRAVEL_TOURISM = 'travel-tourism';
    case HOME_GARDEN = 'home-garden';
    case AUTOMOTIVE = 'automotive';
    case EDUCATION = 'education';
    case FINANCE = 'finance';
    case REAL_ESTATE = 'real-estate';
    case ENTERTAINMENT = 'entertainment';
    case SPORTS = 'sports';
    case PET_CARE = 'pet-care';
    case BABY_KIDS = 'baby-kids';
    case PROFESSIONAL_SERVICES = 'professional-services';
    case NONPROFIT = 'nonprofit';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::RETAIL => 'Retail & E-commerce',
            self::FOOD_BEVERAGE => 'Food & Beverage',
            self::TECHNOLOGY => 'Technology',
            self::HEALTHCARE => 'Healthcare & Medical',
            self::BEAUTY_COSMETICS => 'Beauty & Cosmetics',
            self::FITNESS_WELLNESS => 'Fitness & Wellness',
            self::FASHION_APPAREL => 'Fashion & Apparel',
            self::TRAVEL_TOURISM => 'Travel & Tourism',
            self::HOME_GARDEN => 'Home & Garden',
            self::AUTOMOTIVE => 'Automotive',
            self::EDUCATION => 'Education & Training',
            self::FINANCE => 'Finance & Insurance',
            self::REAL_ESTATE => 'Real Estate',
            self::ENTERTAINMENT => 'Entertainment & Media',
            self::SPORTS => 'Sports & Recreation',
            self::PET_CARE => 'Pet Care & Animals',
            self::BABY_KIDS => 'Baby & Kids',
            self::PROFESSIONAL_SERVICES => 'Professional Services',
            self::NONPROFIT => 'Non-profit & Charity',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::RETAIL => 'Businesses that sell goods directly to consumers.',
            self::FOOD_BEVERAGE => 'Businesses in the food and beverage industry.',
            self::TECHNOLOGY => 'Companies involved in technology and software.',
            self::HEALTHCARE => 'Organizations providing medical services.',
            self::BEAUTY_COSMETICS => 'Businesses in the beauty and cosmetics sector.',
            self::FITNESS_WELLNESS => 'Companies focused on fitness and wellness.',
            self::FASHION_APPAREL => 'Businesses in the fashion and apparel industry.',
            self::TRAVEL_TOURISM => 'Companies in the travel and tourism sector.',
            self::HOME_GARDEN => 'Businesses related to home and garden products.',
            self::AUTOMOTIVE => 'Companies in the automotive industry.',
            self::EDUCATION => 'Organizations providing educational services.',
            self::FINANCE => 'Businesses in the finance and insurance sector.',
            self::REAL_ESTATE => 'Companies involved in real estate and property.',
            self::ENTERTAINMENT => 'Businesses in the entertainment and media industry.',
            self::SPORTS => 'Organizations focused on sports and recreation.',
            self::PET_CARE => 'Businesses providing pet care products and services.',
            self::BABY_KIDS => 'Companies focused on baby and kids products.',
            self::PROFESSIONAL_SERVICES => 'Businesses providing professional services.',
            self::NONPROFIT => 'Organizations operating for charitable purposes.',
            self::OTHER => 'Any other type of business not listed.',
        };
    }
}

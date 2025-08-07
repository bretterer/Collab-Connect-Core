<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum CampaignProductPlacement: int
{
    use HasFormOptions;

    case PRODUCT_MENTION = 1;
    case PRODUCT_SHOWCASE = 2;
    case BRAND_LOGO = 3;
    case LOCATION_TAG = 4;
    case HASHTAG_REQUIRED = 5;
    case DISCOUNT_CODE = 6;

    public function name(): string
    {
        return match ($this) {
            self::PRODUCT_MENTION => 'Product Mention',
            self::PRODUCT_SHOWCASE => 'Product Showcase',
            self::BRAND_LOGO => 'Brand Logo Visible',
            self::LOCATION_TAG => 'Location Tag',
            self::HASHTAG_REQUIRED => 'Specific Hashtags',
            self::DISCOUNT_CODE => 'Discount Code Sharing',
        };
    }

    public function label(): string
    {
        return $this->name();
    }
}

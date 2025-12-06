<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum SuccessMetric: string
{
    use HasFormOptions;

    case IMPRESSIONS = 'impressions';
    case ENGAGEMENT_RATE = 'engagement_rate';
    case CLICKS = 'clicks';
    case CONVERSIONS = 'conversions';
    case FOLLOWER_GROWTH = 'follower_growth';
    case BRAND_AWARENESS = 'brand_awareness';

    public function label(): string
    {
        return match ($this) {
            self::IMPRESSIONS => 'Impressions',
            self::ENGAGEMENT_RATE => 'Engagement Rate',
            self::CLICKS => 'Clicks',
            self::CONVERSIONS => 'Conversions',
            self::FOLLOWER_GROWTH => 'Follower Growth',
            self::BRAND_AWARENESS => 'Brand Awareness',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::IMPRESSIONS => 'Number of times content was viewed',
            self::ENGAGEMENT_RATE => 'Percentage of followers who engaged with content',
            self::CLICKS => 'Number of clicks on links or call-to-action',
            self::CONVERSIONS => 'Number of desired actions taken',
            self::FOLLOWER_GROWTH => 'Increase in follower count',
            self::BRAND_AWARENESS => 'Increase in brand recognition',

        };
    }
}

<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum SuccessMetric: string
{
    use HasFormOptions;

    case IMPRESSIONS = 'impressions';
    case ENGAGEMENT_RATE = 'engagement_rate';
    case REACH = 'reach';
    case CLICKS = 'clicks';
    case CONVERSIONS = 'conversions';
    case FOLLOWER_GROWTH = 'follower_growth';
    case SAVE_RATE = 'save_rate';
    case SHARE_RATE = 'share_rate';
    case COMMENT_RATE = 'comment_rate';
    case LIKE_RATE = 'like_rate';
    case WEBSITE_TRAFFIC = 'website_traffic';
    case SALES = 'sales';
    case BRAND_AWARENESS = 'brand_awareness';
    case SENTIMENT = 'sentiment';
    case UGC_QUALITY = 'ugc_quality';

    public function label(): string
    {
        return match ($this) {
            self::IMPRESSIONS => 'Impressions',
            self::ENGAGEMENT_RATE => 'Engagement Rate',
            self::REACH => 'Reach',
            self::CLICKS => 'Clicks',
            self::CONVERSIONS => 'Conversions',
            self::FOLLOWER_GROWTH => 'Follower Growth',
            self::SAVE_RATE => 'Save Rate',
            self::SHARE_RATE => 'Share Rate',
            self::COMMENT_RATE => 'Comment Rate',
            self::LIKE_RATE => 'Like Rate',
            self::WEBSITE_TRAFFIC => 'Website Traffic',
            self::SALES => 'Sales',
            self::BRAND_AWARENESS => 'Brand Awareness',
            self::SENTIMENT => 'Sentiment Analysis',
            self::UGC_QUALITY => 'UGC Quality Score',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::IMPRESSIONS => 'Number of times content was viewed',
            self::ENGAGEMENT_RATE => 'Percentage of followers who engaged with content',
            self::REACH => 'Number of unique users who saw the content',
            self::CLICKS => 'Number of clicks on links or call-to-action',
            self::CONVERSIONS => 'Number of desired actions taken',
            self::FOLLOWER_GROWTH => 'Increase in follower count',
            self::SAVE_RATE => 'Percentage of users who saved the content',
            self::SHARE_RATE => 'Percentage of users who shared the content',
            self::COMMENT_RATE => 'Percentage of users who commented',
            self::LIKE_RATE => 'Percentage of users who liked the content',
            self::WEBSITE_TRAFFIC => 'Increase in website visits',
            self::SALES => 'Direct sales attributed to campaign',
            self::BRAND_AWARENESS => 'Increase in brand recognition',
            self::SENTIMENT => 'Positive vs negative sentiment analysis',
            self::UGC_QUALITY => 'Quality score of user-generated content',
        };
    }
}
<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class LinkInBioAnalyticsSettings extends Settings
{
    public int $dataRetentionDays;

    public int $viewRateLimitMinutes;

    public static function group(): string
    {
        return 'link_in_bio_analytics';
    }
}

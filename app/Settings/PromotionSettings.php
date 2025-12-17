<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class PromotionSettings extends Settings
{
    public int $profilePromotionDays;

    public static function group(): string
    {
        return 'promotion';
    }
}

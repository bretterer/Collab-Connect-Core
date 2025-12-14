<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SubscriptionSettings extends Settings
{
    public int $trialPeriodDays;

    public static function group(): string
    {
        return 'subscription';
    }
}

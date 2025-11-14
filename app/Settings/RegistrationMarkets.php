<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class RegistrationMarkets extends Settings
{
    public bool $enabled;

    public static function group(): string
    {
        return 'registration_markets';
    }
}

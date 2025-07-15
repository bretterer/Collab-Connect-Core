<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum AccountType: int
{
    use HasFormOptions;

    case UNDEFINED = 0;
    case INFLUENCER = 1;
    case BUSINESS = 2;
    case ADMIN = 99;

    public function label(): string
    {
        return match ($this) {
            self::UNDEFINED => 'Undefined',
            self::INFLUENCER => 'Influencer',
            self::BUSINESS => 'Business',
            self::ADMIN => 'Admin',
        };
    }
}

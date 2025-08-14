<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum CompanySize: string
{
    use HasFormOptions;

    case SOLO = 'solo';
    case SMALL_TEAM = '1-10';
    case GROWING_BUSINESS = '11-50';
    case MEDIUM_BUSINESS = '51-200';
    case LARGE_BUSINESS = '201-1000';
    case ENTERPRISE = '1000+';

    public function label(): string
    {
        return match ($this) {
            self::SOLO => 'Solo (Just me)',
            self::SMALL_TEAM => 'Small Team (1-10 employees)',
            self::GROWING_BUSINESS => 'Growing Business (11-50 employees)',
            self::MEDIUM_BUSINESS => 'Medium Business (51-200 employees)',
            self::LARGE_BUSINESS => 'Large Business (201-1000 employees)',
            self::ENTERPRISE => 'Enterprise (1000+ employees)',
        };
    }

    public function employeeCount(): ?int
    {
        return match ($this) {
            self::SOLO => 1,
            self::SMALL_TEAM => 10,
            self::GROWING_BUSINESS => 50,
            self::MEDIUM_BUSINESS => 200,
            self::LARGE_BUSINESS => 1000,
            self::ENTERPRISE => null, // 1000+
        };
    }

    public function category(): string
    {
        return match ($this) {
            self::SOLO => 'Solopreneur',
            self::SMALL_TEAM, self::GROWING_BUSINESS => 'Small Business',
            self::MEDIUM_BUSINESS => 'Medium Business',
            self::LARGE_BUSINESS, self::ENTERPRISE => 'Large Business',
        };
    }
}

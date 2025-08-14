<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum YearsInBusiness: string
{
    use HasFormOptions;

    case STARTUP = 'startup';
    case NEW = 'new';
    case ESTABLISHED = 'established';
    case MATURE = 'mature';

    public function label(): string
    {
        return match ($this) {
            self::STARTUP => 'Startup (Less than 1 year)',
            self::NEW => 'New Business (1-2 years)',
            self::ESTABLISHED => 'Established (3-5 years)',
            self::MATURE => 'Mature Business (5+ years)',
        };
    }

    public function yearsRange(): array
    {
        return match ($this) {
            self::STARTUP => [0, 1],
            self::NEW => [1, 2],
            self::ESTABLISHED => [3, 5],
            self::MATURE => [5, null], // 5+
        };
    }

    public function experience(): string
    {
        return match ($this) {
            self::STARTUP => 'Getting started',
            self::NEW => 'Building momentum',
            self::ESTABLISHED => 'Proven track record',
            self::MATURE => 'Industry veteran',
        };
    }
}

<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum PercentageChangeType: string
{
    use HasFormOptions;

    case ENROLLMENT = 'enrollment';
    case PERMANENT = 'permanent';
    case TEMPORARY_DATE = 'temporary_date';
    case TEMPORARY_MONTHS = 'temporary_months';

    public function label(): string
    {
        return match ($this) {
            self::ENROLLMENT => 'Enrollment Setup',
            self::PERMANENT => 'Permanent Change',
            self::TEMPORARY_DATE => 'Temporary Until Date',
            self::TEMPORARY_MONTHS => 'Temporary For X Months',
        };
    }
}

<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum ReferralStatus: string
{
    use HasFormOptions;

    case PENDING = 'pending';
    case ACTIVE = 'active';
    case CHURNED = 'churned';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ACTIVE => 'Active',
            self::CHURNED => 'Churned',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'yellow',
            self::ACTIVE => 'green',
            self::CHURNED => 'red',
            self::CANCELLED => 'zinc',
        };
    }
}

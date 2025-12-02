<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum SequenceMode: string
{
    use HasFormOptions;

    case AFTER_SUBSCRIPTION = 'after_subscription';
    case BEFORE_ANCHOR_DATE = 'before_anchor_date';

    public function label(): string
    {
        return match ($this) {
            self::AFTER_SUBSCRIPTION => 'After Subscription',
            self::BEFORE_ANCHOR_DATE => 'Before Anchor Date',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::AFTER_SUBSCRIPTION => 'Send emails X days/hours after someone subscribes',
            self::BEFORE_ANCHOR_DATE => 'Send emails X days/hours before a specific date (e.g., event countdown)',
        };
    }
}

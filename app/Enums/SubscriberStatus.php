<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum SubscriberStatus: string
{
    use HasFormOptions;

    case ACTIVE = 'active';
    case UNSUBSCRIBED = 'unsubscribed';
    case BOUNCED = 'bounced';
    case COMPLAINED = 'complained';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::UNSUBSCRIBED => 'Unsubscribed',
            self::BOUNCED => 'Bounced',
            self::COMPLAINED => 'Complained',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'green',
            self::UNSUBSCRIBED => 'gray',
            self::BOUNCED => 'red',
            self::COMPLAINED => 'orange',
        };
    }
}

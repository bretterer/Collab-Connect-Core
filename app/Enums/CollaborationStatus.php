<?php

namespace App\Enums;

enum CollaborationStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::COMPLETED => 'Completed',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'blue',
            self::COMPLETED => 'green',
            self::CANCELLED => 'red',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::ACTIVE => 'Influencer is actively working on the campaign',
            self::COMPLETED => 'All deliverables have been submitted and approved',
            self::CANCELLED => 'Collaboration was terminated early',
        };
    }
}

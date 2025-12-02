<?php

namespace App\Enums;

enum CampaignApplicationStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case CONTRACTED = 'contracted';
    case REJECTED = 'rejected';
    case WITHDRAWN = 'withdrawn';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::ACCEPTED => 'Accepted',
            self::CONTRACTED => 'Contracted',
            self::REJECTED => 'Rejected',
            self::WITHDRAWN => 'Withdrawn',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'amber',
            self::ACCEPTED => 'green',
            self::CONTRACTED => 'blue',
            self::REJECTED => 'red',
            self::WITHDRAWN => 'gray',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::PENDING => 'Application is awaiting review',
            self::ACCEPTED => 'Application has been accepted by the business',
            self::CONTRACTED => 'Influencer has formally agreed and is working on the campaign',
            self::REJECTED => 'Application was not selected',
            self::WITHDRAWN => 'Influencer withdrew their application',
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return match ($this) {
            self::PENDING => in_array($newStatus, [self::ACCEPTED, self::REJECTED, self::WITHDRAWN]),
            self::ACCEPTED => in_array($newStatus, [self::CONTRACTED, self::REJECTED, self::WITHDRAWN]),
            self::CONTRACTED => false,
            self::REJECTED => false,
            self::WITHDRAWN => false,
        };
    }
}

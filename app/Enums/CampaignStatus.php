<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case SCHEDULED = 'scheduled';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::SCHEDULED => 'Scheduled',
            self::IN_PROGRESS => 'In Progress',
            self::COMPLETED => 'Completed',
            self::ARCHIVED => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::PUBLISHED => 'green',
            self::SCHEDULED => 'blue',
            self::IN_PROGRESS => 'orange',
            self::COMPLETED => 'lime',
            self::ARCHIVED => 'red',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::DRAFT => 'Campaign is being created or edited',
            self::PUBLISHED => 'Campaign is live and accepting applications',
            self::SCHEDULED => 'Campaign will be published on the scheduled date',
            self::IN_PROGRESS => 'Campaign is active, influencers are working on deliverables',
            self::COMPLETED => 'Campaign has been successfully completed',
            self::ARCHIVED => 'Campaign has been cancelled or deactivated',
        };
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PUBLISHED, self::IN_PROGRESS]);
    }

    public function isEditable(): bool
    {
        return in_array($this, [self::DRAFT, self::SCHEDULED]);
    }

    public function isTerminal(): bool
    {
        return in_array($this, [self::COMPLETED, self::ARCHIVED]);
    }
}

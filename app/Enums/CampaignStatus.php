<?php

namespace App\Enums;

enum CampaignStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case SCHEDULED = 'scheduled';
    case IN_PROGRESS = 'in_progress';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::PUBLISHED => 'Published',
            self::SCHEDULED => 'Scheduled',
            self::IN_PROGRESS => 'In Progress',
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
            self::ARCHIVED => 'red',
        };
    }
}

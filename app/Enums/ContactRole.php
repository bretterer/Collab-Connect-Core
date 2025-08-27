<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum ContactRole: string
{
    use HasFormOptions;

    case OWNER = 'owner';
    case MARKETING_MANAGER = 'marketing-manager';
    case MARKETING_DIRECTOR = 'marketing-director';
    case SOCIAL_MEDIA_MANAGER = 'social-media-manager';
    case AGENCY_REPRESENTATIVE = 'agency-representative';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Business Owner',
            self::MARKETING_MANAGER => 'Marketing Manager',
            self::MARKETING_DIRECTOR => 'Marketing Director',
            self::SOCIAL_MEDIA_MANAGER => 'Social Media Manager',
            self::AGENCY_REPRESENTATIVE => 'Agency Representative',
            self::OTHER => 'Other',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::OWNER => 'Primary decision maker and business owner',
            self::MARKETING_MANAGER => 'Manages marketing campaigns and strategies',
            self::MARKETING_DIRECTOR => 'Oversees marketing department and initiatives',
            self::SOCIAL_MEDIA_MANAGER => 'Manages social media presence and content',
            self::AGENCY_REPRESENTATIVE => 'Represents a marketing or advertising agency',
            self::OTHER => 'Other role not listed above',
        };
    }
}

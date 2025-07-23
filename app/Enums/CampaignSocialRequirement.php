<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum CampaignSocialRequirement: int
{
    use HasFormOptions;
    case FACEBOOK = 1;
    case INSTAGRAM = 2;
    case TWITTER = 3;
    case TIKTOK = 4;
    case YOUTUBE = 5;
    case LINKEDIN = 6;
    case PINTEREST = 7;
    case SNAPCHAT = 8;

    public function name(): string
    {
        return match ($this) {
            self::FACEBOOK => 'Facebook',
            self::INSTAGRAM => 'Instagram',
            self::TWITTER => 'Twitter',
            self::TIKTOK => 'TikTok',
            self::YOUTUBE => 'YouTube',
            self::LINKEDIN => 'LinkedIn',
            self::PINTEREST => 'Pinterest',
            self::SNAPCHAT => 'Snapchat',
        };
    }

    public function label(): string
    {
        return $this->name();
    }
}

<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum TargetPlatform: string
{
    use HasFormOptions;

    case INSTAGRAM = 'instagram';
    case TIKTOK = 'tiktok';
    case YOUTUBE = 'youtube';
    case FACEBOOK = 'facebook';

    public function label(): string
    {
        return match ($this) {
            self::INSTAGRAM => 'Instagram',
            self::TIKTOK => 'TikTok',
            self::YOUTUBE => 'YouTube',
            self::FACEBOOK => 'Facebook',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::INSTAGRAM => 'instagram',
            self::TIKTOK => 'tiktok',
            self::YOUTUBE => 'youtube',
            self::FACEBOOK => 'facebook',
        };
    }
}

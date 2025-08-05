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
    case TWITTER = 'twitter';
    case LINKEDIN = 'linkedin';
    case PINTEREST = 'pinterest';
    case SNAPCHAT = 'snapchat';
    case TWITCH = 'twitch';
    case BLOG = 'blog';

    public function label(): string
    {
        return match ($this) {
            self::INSTAGRAM => 'Instagram',
            self::TIKTOK => 'TikTok',
            self::YOUTUBE => 'YouTube',
            self::FACEBOOK => 'Facebook',
            self::TWITTER => 'Twitter/X',
            self::LINKEDIN => 'LinkedIn',
            self::PINTEREST => 'Pinterest',
            self::SNAPCHAT => 'Snapchat',
            self::TWITCH => 'Twitch',
            self::BLOG => 'Blog/Website',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::INSTAGRAM => 'instagram',
            self::TIKTOK => 'tiktok',
            self::YOUTUBE => 'youtube',
            self::FACEBOOK => 'facebook',
            self::TWITTER => 'twitter',
            self::LINKEDIN => 'linkedin',
            self::PINTEREST => 'pinterest',
            self::SNAPCHAT => 'snapchat',
            self::TWITCH => 'twitch',
            self::BLOG => 'document-text',
        };
    }
}
<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum DeliverableType: string
{
    use HasFormOptions;

    case INSTAGRAM_POST = 'instagram_post';
    case INSTAGRAM_STORY = 'instagram_story';
    case INSTAGRAM_REEL = 'instagram_reel';
    case TIKTOK_VIDEO = 'tiktok_video';
    case YOUTUBE_VIDEO = 'youtube_video';
    case FACEBOOK_POST = 'facebook_post';

    public function label(): string
    {
        return match ($this) {
            self::INSTAGRAM_POST => 'Instagram Post',
            self::INSTAGRAM_STORY => 'Instagram Story',
            self::INSTAGRAM_REEL => 'Instagram Reel',
            self::TIKTOK_VIDEO => 'TikTok Video',
            self::YOUTUBE_VIDEO => 'YouTube Video',
            self::FACEBOOK_POST => 'Facebook Post',
        };
    }

    public function platform(): string
    {
        return match ($this) {
            self::INSTAGRAM_POST, self::INSTAGRAM_STORY, self::INSTAGRAM_REEL => 'Instagram',
            self::TIKTOK_VIDEO => 'TikTok',
            self::YOUTUBE_VIDEO => 'YouTube',
            self::FACEBOOK_POST => 'Facebook',
        };
    }
}

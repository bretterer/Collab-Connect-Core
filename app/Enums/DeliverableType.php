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
    case TWITTER_POST = 'twitter_post';
    case BLOG_POST = 'blog_post';
    case PINTEREST_PIN = 'pinterest_pin';
    case SNAPCHAT_STORY = 'snapchat_story';
    case TWITCH_STREAM = 'twitch_stream';
    case UGC_CONTENT = 'ugc_content';
    case PHOTO_SHOOT = 'photo_shoot';
    case VIDEO_CONTENT = 'video_content';

    public function label(): string
    {
        return match ($this) {
            self::INSTAGRAM_POST => 'Instagram Post',
            self::INSTAGRAM_STORY => 'Instagram Story',
            self::INSTAGRAM_REEL => 'Instagram Reel',
            self::TIKTOK_VIDEO => 'TikTok Video',
            self::YOUTUBE_VIDEO => 'YouTube Video',
            self::FACEBOOK_POST => 'Facebook Post',
            self::TWITTER_POST => 'Twitter/X Post',
            self::BLOG_POST => 'Blog Post',
            self::PINTEREST_PIN => 'Pinterest Pin',
            self::SNAPCHAT_STORY => 'Snapchat Story',
            self::TWITCH_STREAM => 'Twitch Stream',
            self::UGC_CONTENT => 'User-Generated Content',
            self::PHOTO_SHOOT => 'Photo Shoot',
            self::VIDEO_CONTENT => 'Video Content',
        };
    }

    public function platform(): string
    {
        return match ($this) {
            self::INSTAGRAM_POST, self::INSTAGRAM_STORY, self::INSTAGRAM_REEL => 'Instagram',
            self::TIKTOK_VIDEO => 'TikTok',
            self::YOUTUBE_VIDEO => 'YouTube',
            self::FACEBOOK_POST => 'Facebook',
            self::TWITTER_POST => 'Twitter/X',
            self::BLOG_POST => 'Blog',
            self::PINTEREST_PIN => 'Pinterest',
            self::SNAPCHAT_STORY => 'Snapchat',
            self::TWITCH_STREAM => 'Twitch',
            self::UGC_CONTENT, self::PHOTO_SHOOT, self::VIDEO_CONTENT => 'Multi-platform',
        };
    }
}
<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum SocialPlatform: string
{
    use HasFormOptions;

    case INSTAGRAM = 'instagram';
    case TIKTOK = 'tiktok';
    case YOUTUBE = 'youtube';
    case FACEBOOK = 'facebook';
    case X = 'x';
    case SNAPCHAT = 'snapchat';

    public function label(): string
    {
        return match ($this) {
            self::INSTAGRAM => 'Instagram',
            self::TIKTOK => 'TikTok',
            self::YOUTUBE => 'YouTube',
            self::FACEBOOK => 'Facebook',
            self::X => 'X (Twitter)',
            self::SNAPCHAT => 'Snapchat',
        };
    }

    public function baseUrl(): string
    {
        return match ($this) {
            self::INSTAGRAM => 'https://instagram.com/',
            self::TIKTOK => 'https://tiktok.com/@',
            self::YOUTUBE => 'https://youtube.com/@',
            self::FACEBOOK => 'https://facebook.com/',
            self::X => 'https://x.com/',
            self::SNAPCHAT => 'https://snapchat.com/add/',
        };
    }

    public function generateUrl(string $username): string
    {
        return $this->baseUrl().$username;
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::INSTAGRAM => '📸',
            self::TIKTOK => '🎵',
            self::YOUTUBE => '📺',
            self::FACEBOOK => '👥',
            self::X => '🐦',
            self::SNAPCHAT => '👻',
        };
    }

    /**
     * Get platforms most relevant for businesses
     */
    public static function forBusinesses(): array
    {
        return self::cases();
    }

    /**
     * Get platforms most relevant for influencers
     */
    public static function forInfluencers(): array
    {
        return self::cases(); // All platforms are relevant for influencers
    }
}

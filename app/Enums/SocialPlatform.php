<?php

namespace App\Enums;

enum SocialPlatform: string
{
    case INSTAGRAM = 'instagram';
    case TIKTOK = 'tiktok';
    case YOUTUBE = 'youtube';
    case FACEBOOK = 'facebook';
    case X = 'x';
    case LINKEDIN = 'linkedin';
    case PINTEREST = 'pinterest';
    case SNAPCHAT = 'snapchat';

    public function label(): string
    {
        return match ($this) {
            self::INSTAGRAM => 'Instagram',
            self::TIKTOK => 'TikTok',
            self::YOUTUBE => 'YouTube',
            self::FACEBOOK => 'Facebook',
            self::X => 'X (Twitter)',
            self::LINKEDIN => 'LinkedIn',
            self::PINTEREST => 'Pinterest',
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
            self::LINKEDIN => 'https://linkedin.com/in/',
            self::PINTEREST => 'https://pinterest.com/',
            self::SNAPCHAT => 'https://snapchat.com/add/',
        };
    }

    public function generateUrl(string $username): string
    {
        return $this->baseUrl().$username;
    }

    /**
     * Get all values as associative array for form options
     */
    public static function toOptions(): array
    {
        return array_combine(
            array_map(fn ($case) => $case->value, self::cases()),
            array_map(fn ($case) => $case->label(), self::cases())
        );
    }

    /**
     * Get platforms most relevant for businesses
     */
    public static function forBusinesses(): array
    {
        return [
            self::INSTAGRAM,
            self::FACEBOOK,
            self::LINKEDIN,
            self::YOUTUBE,
            self::X,
        ];
    }

    /**
     * Get platforms most relevant for influencers
     */
    public static function forInfluencers(): array
    {
        return self::cases(); // All platforms are relevant for influencers
    }
}

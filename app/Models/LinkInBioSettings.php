<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LinkInBioSettings extends Model
{
    protected $fillable = [
        'influencer_id',
        'settings',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'is_published' => 'boolean',
        ];
    }

    /**
     * Get the default settings structure.
     *
     * @return array<string, mixed>
     */
    public static function getDefaultSettings(): array
    {
        return [
            // Design Settings
            'design' => [
                'themeColor' => '#dc2626',
                'font' => 'sans',
                'containerStyle' => 'round',
                'containerShadow' => true,
                'containerOutline' => false,
                'containerTransparency' => 0,
            ],

            // Header Settings
            'header' => [
                'enabled' => true,
                'profilePictureShape' => 'round',
                'profilePictureSize' => 100,
                'profilePictureBorder' => true,
                'headerType' => 'text',
                'displayName' => '',
                'displayNameSize' => 'medium',
                'location' => '',
                'bio' => '',
                'headerFormat' => 'vertical',
                'showShareButton' => true,
            ],

            // Links Section
            'links' => [
                'enabled' => true,
                'title' => '',
                'subtitle' => '',
                'visibility' => 'exposed',
                'layout' => 'classic',
                'size' => 'medium',
                'textAlign' => 'center',
                'shadow' => true,
                'outline' => false,
                'items' => [],
            ],

            // Rates Section
            'rates' => [
                'enabled' => true,
                'title' => 'My Rates',
                'subtitle' => '',
                'size' => 'small',
                'items' => [],
            ],

            // Social Icons
            'social' => [
                'iconSize' => 'small',
                'openNewTab' => true,
            ],

            // Work With Me Section
            'workWithMe' => [
                'enabled' => true,
                'text' => 'Work With Me',
                'style' => 'secondary',
                'buttonColor' => '#000000',
            ],

            // Join Referral Section (Elite only, requires referral enrollment)
            'joinReferral' => [
                'enabled' => false,
                'text' => 'Join CollabConnect',
                'style' => 'secondary',
                'buttonColor' => '#000000',
            ],
        ];
    }

    /**
     * Get a setting value with dot notation support.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Set a setting value with dot notation support.
     */
    public function setSetting(string $key, mixed $value): self
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;

        return $this;
    }

    /**
     * Merge settings with defaults.
     *
     * @return array<string, mixed>
     */
    public function getMergedSettings(): array
    {
        return array_replace_recursive(
            static::getDefaultSettings(),
            $this->settings ?? []
        );
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }
}

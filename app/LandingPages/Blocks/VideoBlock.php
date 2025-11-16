<?php

namespace App\LandingPages\Blocks;

class VideoBlock extends BaseBlock
{
    public static function type(): string
    {
        return 'video';
    }

    public static function label(): string
    {
        return 'Video';
    }

    public static function description(): string
    {
        return 'Video player with upload and playback controls';
    }

    public static function icon(): string
    {
        return 'film';
    }

    /**
     * Define content-specific defaults for this block
     */
    protected static function contentDefaultData(): array
    {
        return [
            'video_url' => '',
            'video_title' => '',
            'poster_url' => '',
            'thumbnail_url' => '',
            'autoplay' => false,
            'loop' => false,
            'muted' => false,
            'controls' => true,
            'show_play_button' => true,
            'show_progress' => true,
            'show_current_time' => true,
            'show_duration' => true,
            'show_mute' => true,
            'show_volume' => true,
            'show_fullscreen' => true,
            'width' => 'full', // full, large, medium, small
            'alignment' => 'center', // left, center, right
        ];
    }

    /**
     * Define content-specific validation rules
     */
    protected function contentRules(): array
    {
        return [
            'video_url' => ['nullable', 'string', 'max:2048'],
            'video_title' => ['nullable', 'string', 'max:255'],
            'poster_url' => ['nullable', 'string', 'max:2048'],
            'thumbnail_url' => ['nullable', 'string', 'max:2048'],
            'autoplay' => ['boolean'],
            'loop' => ['boolean'],
            'muted' => ['boolean'],
            'controls' => ['boolean'],
            'show_play_button' => ['boolean'],
            'show_progress' => ['boolean'],
            'show_current_time' => ['boolean'],
            'show_duration' => ['boolean'],
            'show_mute' => ['boolean'],
            'show_volume' => ['boolean'],
            'show_fullscreen' => ['boolean'],
            'width' => ['required', 'in:full,large,medium,small'],
            'alignment' => ['required', 'in:left,center,right'],
        ];
    }
}

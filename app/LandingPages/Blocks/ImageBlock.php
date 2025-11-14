<?php

namespace App\LandingPages\Blocks;

class ImageBlock extends BaseBlock
{
    public static function type(): string
    {
        return 'image';
    }

    public static function label(): string
    {
        return 'Image';
    }

    public static function description(): string
    {
        return 'Description for Image';
    }

    public static function icon(): string
    {
        return 'photo';
    }

    /**
     * Define content-specific defaults for this block
     */
    protected static function contentDefaultData(): array
    {
        return [
            'content' => '',
        ];
    }

    /**
     * Override layout defaults with larger padding for images
     */
    protected static function layoutDefaultData(): array
    {
        return array_merge(parent::layoutDefaultData(), [
            'desktop_padding_top' => 64,
            'desktop_padding_bottom' => 64,
            'mobile_padding_top' => 48,
            'mobile_padding_bottom' => 48,
        ]);
    }

    /**
     * Override style defaults - images only need basic colors
     */
    protected static function styleDefaultData(): array
    {
        return [
            'background_color' => 'transparent',
            'text_color' => 'inherit',
        ];
    }

    /**
     * Define content-specific validation rules
     */
    protected function contentRules(): array
    {
        return [
            'content' => ['nullable', 'string'],
        ];
    }

    /**
     * Override style rules - images don't need borders/shadows
     */
    protected function styleRules(): array
    {
        return [
            'background_color' => ['nullable', 'string'],
            'text_color' => ['nullable', 'string'],
        ];
    }
}

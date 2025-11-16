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
            'image_url' => '',
            'image_alt' => '',
            'image_width' => null,
            'image_height' => null,
            'brightness' => 0,
            'contrast' => 0,
            'saturation' => 0,
            'blur' => 0,
        ];
    }

    /**
     * Define settings-specific defaults for display configuration
     */
    protected static function settingsDefaultData(): array
    {
        return [
            'display_width' => null,
            'display_height' => null,
            'maintain_aspect_ratio' => true,
            'alignment' => 'center', // left, center, right
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
            'image_url' => ['nullable', 'string', 'max:2048'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'image_width' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'image_height' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'brightness' => ['nullable', 'integer', 'min:-100', 'max:100'],
            'contrast' => ['nullable', 'integer', 'min:-100', 'max:100'],
            'saturation' => ['nullable', 'integer', 'min:-100', 'max:100'],
            'blur' => ['nullable', 'integer', 'min:0', 'max:100'],
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

    /**
     * Define settings validation rules
     */
    protected function settingsRules(): array
    {
        return [
            'display_width' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'display_height' => ['nullable', 'integer', 'min:1', 'max:5000'],
            'maintain_aspect_ratio' => ['boolean'],
            'alignment' => ['required', 'in:left,center,right'],
        ];
    }
}

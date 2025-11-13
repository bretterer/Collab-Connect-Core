<?php

namespace App\LandingPages\Blocks;

use Illuminate\View\View;

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
        return 'document-text';
    }

    public static function defaultData(): array
    {
        return [
            // Content
            'content' => '',

            // Layout - Desktop
            'desktop_hide' => false,
            'desktop_padding_top' => 64,
            'desktop_padding_bottom' => 64,
            'desktop_padding_left' => 16,
            'desktop_padding_right' => 16,
            'desktop_margin_top' => 0,
            'desktop_margin_bottom' => 0,

            // Layout - Mobile
            'mobile_hide' => false,
            'mobile_padding_top' => 48,
            'mobile_padding_bottom' => 48,
            'mobile_padding_left' => 16,
            'mobile_padding_right' => 16,
            'mobile_margin_top' => 0,
            'mobile_margin_bottom' => 0,

            // Style
            'background_color' => 'transparent',
            'text_color' => 'inherit',
        ];
    }

    protected function rules(): array
    {
        return [
            'content' => ['nullable', 'string'],

            'desktop_hide' => ['boolean'],
            'desktop_padding_top' => ['integer', 'min:0', 'max:256'],
            'desktop_padding_bottom' => ['integer', 'min:0', 'max:256'],
            'desktop_padding_left' => ['integer', 'min:0', 'max:256'],
            'desktop_padding_right' => ['integer', 'min:0', 'max:256'],
            'desktop_margin_top' => ['integer', 'min:-128', 'max:256'],
            'desktop_margin_bottom' => ['integer', 'min:-128', 'max:256'],

            'mobile_hide' => ['boolean'],
            'mobile_padding_top' => ['integer', 'min:0', 'max:256'],
            'mobile_padding_bottom' => ['integer', 'min:0', 'max:256'],
            'mobile_padding_left' => ['integer', 'min:0', 'max:256'],
            'mobile_padding_right' => ['integer', 'min:0', 'max:256'],
            'mobile_margin_top' => ['integer', 'min:-128', 'max:256'],
            'mobile_margin_bottom' => ['integer', 'min:-128', 'max:256'],

            'background_color' => ['nullable', 'string'],
            'text_color' => ['nullable', 'string'],
        ];
    }

    public function renderEditor(array $data, string $propertyPrefix = 'blockData'): View
    {
        return view('landing-pages.blocks.image.editor', [
            'data' => array_merge(self::defaultData(), $data),
            'propertyPrefix' => $propertyPrefix,
            'tabs' => $this->editorTabs(),
        ]);
    }

    public function render(array $data): View
    {
        return view('landing-pages.blocks.image.render', [
            'data' => array_merge(self::defaultData(), $data),
        ]);
    }
}

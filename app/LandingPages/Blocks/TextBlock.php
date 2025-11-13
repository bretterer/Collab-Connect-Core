<?php

namespace App\LandingPages\Blocks;

use Illuminate\View\View;

class TextBlock extends BaseBlock
{
    public static function type(): string
    {
        return 'text';
    }

    public static function label(): string
    {
        return 'Text Block';
    }

    public static function description(): string
    {
        return 'Rich text content with formatting and customizable styling';
    }

    public static function icon(): string
    {
        return 'document-text';
    }

    public static function defaultData(): array
    {
        return [
            // Content
            'content' => '<p>Add your content here...</p>',
            'text_align' => 'left',
            'max_width' => 'prose',

            // Layout - Desktop
            'desktop_hide' => false,
            'desktop_padding_top' => 32,
            'desktop_padding_bottom' => 32,
            'desktop_padding_left' => 16,
            'desktop_padding_right' => 16,
            'desktop_margin_top' => 0,
            'desktop_margin_bottom' => 0,

            // Layout - Mobile
            'mobile_hide' => false,
            'mobile_padding_top' => 24,
            'mobile_padding_bottom' => 24,
            'mobile_padding_left' => 16,
            'mobile_padding_right' => 16,
            'mobile_margin_top' => 0,
            'mobile_margin_bottom' => 0,

            // Style
            'background_color' => 'transparent',
            'text_color' => 'inherit',
            'border_type' => 'none',
            'border_width' => 1,
            'border_color' => '#e5e7eb',
            'border_radius' => 0,
            'box_shadow' => 'none',
        ];
    }

    protected function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'text_align' => ['required', 'in:left,center,right,justify'],
            'max_width' => ['required', 'in:sm,prose,lg,xl,full'],

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
            'border_type' => ['required', 'in:none,solid,dashed,dotted'],
            'border_width' => ['integer', 'min:0', 'max:8'],
            'border_color' => ['nullable', 'string'],
            'border_radius' => ['integer', 'min:0', 'max:64'],
            'box_shadow' => ['required', 'in:none,sm,md,lg,xl'],
        ];
    }

    public function renderEditor(array $data, string $propertyPrefix = 'blockData'): View
    {
        return view('landing-pages.blocks.text.editor', [
            'data' => array_merge(self::defaultData(), $data),
            'propertyPrefix' => $propertyPrefix,
            'tabs' => $this->editorTabs(),
        ]);
    }

    public function render(array $data): View
    {
        return view('landing-pages.blocks.text.render', [
            'data' => array_merge(self::defaultData(), $data),
        ]);
    }
}

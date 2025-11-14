<?php

namespace App\LandingPages\Blocks;

class CTABlock extends BaseBlock
{
    public static function type(): string
    {
        return 'cta';
    }

    public static function label(): string
    {
        return 'CTA';
    }

    public static function description(): string
    {
        return 'Call-to-action button with customizable styling and actions';
    }

    public static function icon(): string
    {
        return 'megaphone';
    }

    /**
     * Define content-specific defaults for this block
     */
    protected static function contentDefaultData(): array
    {
        return [
            'text' => 'Click Here',
            'action' => 'url', // url, two_step_optin, section
            'url' => '',
            'section_id' => '',
            'open_new_tab' => false,
            'button_bg_color' => '#DFAD42',
            'button_text_color' => '#000000',
            'button_width' => 'full', // full, auto
            'button_style' => 'solid', // solid, outline
            'button_size' => 'large', // small, medium, large
            'border_radius' => 0,
        ];
    }

    /**
     * Define content-specific validation rules
     */
    protected function contentRules(): array
    {
        return [
            'text' => ['required', 'string', 'max:255'],
            'action' => ['required', 'in:url,two_step_optin,section'],
            'url' => ['nullable', 'string', 'max:2048'],
            'section_id' => ['nullable', 'string'],
            'open_new_tab' => ['boolean'],
            'button_bg_color' => ['required', 'string'],
            'button_text_color' => ['required', 'string'],
            'button_width' => ['required', 'in:full,auto'],
            'button_style' => ['required', 'in:solid,outline'],
            'button_size' => ['required', 'in:small,medium,large'],
            'border_radius' => ['required', 'integer', 'min:0', 'max:999'],
        ];
    }
}

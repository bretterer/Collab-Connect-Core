<?php

namespace App\LandingPages\Blocks;

class FormBlock extends BaseBlock
{
    public static function type(): string
    {
        return 'form';
    }

    public static function label(): string
    {
        return 'Form';
    }

    public static function description(): string
    {
        return 'Embed a customizable form with automatic submission handling';
    }

    public static function icon(): string
    {
        return 'clipboard-document-list';
    }

    /**
     * Define content-specific defaults for this block
     */
    protected static function contentDefaultData(): array
    {
        return [
            'form_id' => null,
            'submit_button_text' => 'Submit',
            'success_message' => 'Thank you! Your submission has been received.',
            'thank_you_action' => 'message', // message, landing_page, url
            'thank_you_landing_page_id' => null,
            'thank_you_url' => '',
            'disclaimer_text' => '',
            'disclaimer_text_color' => '#6B7280',
            'button_text' => 'Submit',
            'button_bg_color' => '#DFAD42',
            'button_text_color' => '#000000',
            'button_width' => 'full', // full, auto
            'button_size' => 'large', // small, medium, large
            'border_radius' => 8,
            'fire_event' => true, // Whether to fire the form-submitted event
        ];
    }

    /**
     * Define content-specific validation rules
     */
    protected function contentRules(): array
    {
        return [
            'form_id' => ['required', 'exists:forms,id'],
            'submit_button_text' => ['required', 'string', 'max:100'],
            'success_message' => ['nullable', 'string', 'max:500'],
            'thank_you_action' => ['required', 'in:message,landing_page,url'],
            'thank_you_landing_page_id' => ['nullable', 'exists:landing_pages,id'],
            'thank_you_url' => ['nullable', 'string', 'max:2048'],
            'disclaimer_text' => ['nullable', 'string', 'max:1000'],
            'disclaimer_text_color' => ['nullable', 'string'],
            'button_text' => ['required', 'string', 'max:100'],
            'button_bg_color' => ['required', 'string'],
            'button_text_color' => ['required', 'string'],
            'button_width' => ['required', 'in:full,auto'],
            'button_size' => ['required', 'in:small,medium,large'],
            'border_radius' => ['required', 'integer', 'min:0', 'max:50'],
            'fire_event' => ['boolean'],
        ];
    }
}

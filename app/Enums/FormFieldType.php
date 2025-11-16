<?php

namespace App\Enums;

use App\Enums\Traits\HasFormOptions;

enum FormFieldType: string
{
    use HasFormOptions;

    case TEXT = 'text';
    case EMAIL = 'email';
    case PHONE = 'phone';
    case TEXTAREA = 'textarea';
    case SELECT = 'select';
    case CHECKBOX = 'checkbox';
    case RADIO = 'radio';
    case DATE = 'date';
    case NUMBER = 'number';
    case URL = 'url';

    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Text Field',
            self::EMAIL => 'Email',
            self::PHONE => 'Phone Number',
            self::TEXTAREA => 'Text Area',
            self::SELECT => 'Dropdown',
            self::CHECKBOX => 'Checkbox',
            self::RADIO => 'Radio Buttons',
            self::DATE => 'Date',
            self::NUMBER => 'Number',
            self::URL => 'URL',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::TEXT => 'pencil',
            self::EMAIL => 'envelope',
            self::PHONE => 'phone',
            self::TEXTAREA => 'document-text',
            self::SELECT => 'list-bullet',
            self::CHECKBOX => 'check-circle',
            self::RADIO => 'circle-stack',
            self::DATE => 'calendar',
            self::NUMBER => 'hashtag',
            self::URL => 'link',
        };
    }

    public function hasOptions(): bool
    {
        return in_array($this, [self::SELECT, self::CHECKBOX, self::RADIO]);
    }
}

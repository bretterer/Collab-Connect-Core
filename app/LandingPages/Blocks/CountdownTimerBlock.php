<?php

namespace App\LandingPages\Blocks;

class CountdownTimerBlock extends BaseBlock
{
    public static function type(): string
    {
        return 'countdown-timer';
    }

    public static function label(): string
    {
        return 'Countdown Timer';
    }

    public static function description(): string
    {
        return 'Live countdown timer to a specific date and time';
    }

    public static function icon(): string
    {
        return 'clock';
    }

    /**
     * Define content-specific defaults for this block
     */
    protected static function contentDefaultData(): array
    {
        return [
            'target_datetime' => '',
            'number_color' => '#DFAD42',
            'label_color' => '#DFAD42',
            'background_color' => '#000000',
            'label_days' => 'Days',
            'label_hours' => 'Hours',
            'label_minutes' => 'Minutes',
            'label_seconds' => 'Seconds',
            'remove_on_completion' => false,
        ];
    }

    /**
     * Define content-specific validation rules
     */
    protected function contentRules(): array
    {
        return [
            'target_datetime' => ['required', 'string'],
            'number_color' => ['required', 'string'],
            'label_color' => ['required', 'string'],
            'background_color' => ['required', 'string'],
            'label_days' => ['required', 'string', 'max:20'],
            'label_hours' => ['required', 'string', 'max:20'],
            'label_minutes' => ['required', 'string', 'max:20'],
            'label_seconds' => ['required', 'string', 'max:20'],
            'remove_on_completion' => ['boolean'],
        ];
    }
}

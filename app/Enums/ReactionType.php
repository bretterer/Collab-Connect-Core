<?php

namespace App\Enums;

enum ReactionType: string
{
    case ThumbsUp = 'thumbs_up';
    case Star = 'star';
    case Heart = 'heart';
    case ThumbsDown = 'thumbs_down';

    public function label(): string
    {
        return match ($this) {
            self::ThumbsUp => 'Thumbs Up',
            self::Star => 'Star',
            self::Heart => 'Heart',
            self::ThumbsDown => 'Thumbs Down',
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::ThumbsUp => '👍',
            self::Star => '⭐',
            self::Heart => '❤️',
            self::ThumbsDown => '👎',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ThumbsUp => 'hand-thumb-up',
            self::Star => 'star',
            self::Heart => 'heart',
            self::ThumbsDown => 'hand-thumb-down',
        };
    }

    /**
     * Get all reactions for display in the UI.
     *
     * @return array<array{value: string, emoji: string, label: string}>
     */
    public static function toOptions(): array
    {
        return array_map(fn (self $type) => [
            'value' => $type->value,
            'emoji' => $type->emoji(),
            'label' => $type->label(),
            'icon' => $type->icon(),
        ], self::cases());
    }
}

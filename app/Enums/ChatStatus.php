<?php

namespace App\Enums;

enum ChatStatus: string
{
    case Active = 'active';
    case Archived = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Active',
            self::Archived => 'Archived',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Active => 'green',
            self::Archived => 'zinc',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Active => 'Chat is open and accepting messages',
            self::Archived => 'Chat is closed and read-only',
        };
    }

    public function isActive(): bool
    {
        return $this === self::Active;
    }

    public function canSendMessages(): bool
    {
        return $this === self::Active;
    }
}

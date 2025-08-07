<?php

namespace App\Enums\Traits;

trait HasFormOptions
{
    /**
     * Get all values as array of objects for form options
     */
    public static function toOptions(?array $cases = null): array
    {
        $cases = $cases ?? self::cases();

        return array_map(fn ($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], $cases);
    }

    /**
     * Get all values as a simple array
     */
    public static function values(): array
    {
        return array_map(fn ($case) => $case->value, self::cases());
    }

    /**
     * Get all labels as a simple array
     */
    public static function labels(): array
    {
        return array_map(fn ($case) => $case->label(), self::cases());
    }

    /**
     * Get enum case by value
     */
    public static function fromValue(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        return null;
    }

    /**
     * Check if a value is valid for this enum
     */
    public static function isValid(?string $value): bool
    {
        if ($value === null) {
            return false;
        }

        return in_array($value, self::values());
    }

    /**
     * Get validation rule for this enum
     */
    public static function validationRule(): string
    {
        return 'in:'.implode(',', self::values());
    }

    /**
     * Get random enum case
     */
    public static function random(): self
    {
        return fake()->randomElement(self::cases());
    }
}

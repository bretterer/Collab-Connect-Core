<?php

namespace App\Casts;

use BackedEnum;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * A safe enum cast that returns null for invalid enum values instead of throwing an exception.
 *
 * @template TEnum of BackedEnum
 */
class SafeEnumCast implements CastsAttributes
{
    /**
     * @param  class-string<TEnum>  $enumClass
     */
    public function __construct(
        protected string $enumClass
    ) {}

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     * @return TEnum|null
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?BackedEnum
    {
        if ($value === null) {
            return null;
        }

        return $this->enumClass::tryFrom($value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof BackedEnum) {
            return $value->value;
        }

        // Validate the value is a valid enum case before storing
        $enum = $this->enumClass::tryFrom($value);

        return $enum?->value;
    }
}

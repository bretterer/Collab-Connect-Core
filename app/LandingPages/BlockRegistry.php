<?php

namespace App\LandingPages;

use App\LandingPages\Blocks\BlockInterface;
use Illuminate\Support\Collection;

class BlockRegistry
{
    protected static array $blocks = [];

    /**
     * Register a block
     */
    public static function register(string $blockClass): void
    {
        if (! is_subclass_of($blockClass, BlockInterface::class)) {
            throw new \InvalidArgumentException('Block must implement BlockInterface');
        }

        self::$blocks[$blockClass::type()] = $blockClass;
    }

    /**
     * Get all registered blocks
     */
    public static function all(): Collection
    {
        return collect(self::$blocks)->map(fn ($class) => [
            'type' => $class::type(),
            'label' => $class::label(),
            'description' => $class::description(),
            'icon' => $class::icon(),
        ]);
    }

    /**
     * Get a block instance by type
     */
    public static function get(string $type): ?BlockInterface
    {
        $class = self::$blocks[$type] ?? null;

        return $class ? new $class : null;
    }

    /**
     * Check if a block type exists
     */
    public static function has(string $type): bool
    {
        return isset(self::$blocks[$type]);
    }

    /**
     * Get default data for a block type
     */
    public static function getDefaultData(string $type): array
    {
        $class = self::$blocks[$type] ?? null;

        return $class ? $class::defaultData() : [];
    }
}

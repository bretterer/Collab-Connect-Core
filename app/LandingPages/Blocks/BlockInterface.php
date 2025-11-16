<?php

namespace App\LandingPages\Blocks;

use Illuminate\View\View;

interface BlockInterface
{
    /**
     * Get the block type identifier
     */
    public static function type(): string;

    /**
     * Get the block label for display in the editor
     */
    public static function label(): string;

    /**
     * Get the block description
     */
    public static function description(): string;

    /**
     * Get the block icon (Heroicon name)
     */
    public static function icon(): string;

    /**
     * Get default data structure for this block
     */
    public static function defaultData(): array;

    /**
     * Validate the block data
     */
    public function validate(array $data): array;

    /**
     * Render the block editor view
     */
    public function renderEditor(array $data, string $propertyPrefix = 'blockData'): View;

    /**
     * Render the public-facing view
     */
    public function render(array $data): View;

    /**
     * Get the editor tabs configuration
     */
    public function editorTabs(): array;
}

<?php

namespace App\LandingPages\Blocks;

use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

abstract class BaseBlock implements BlockInterface
{
    /**
     * Get default data structure for this block
     * Automatically discovers and merges all *DefaultData() methods
     */
    public static function defaultData(): array
    {
        $data = [];

        // Discover all methods ending with 'DefaultData'
        $methods = static::discoverMethodsByPattern('DefaultData');

        foreach ($methods as $method) {
            $data = array_merge($data, static::$method());
        }

        return $data;
    }

    /**
     * Get default content-specific data for this block
     * Override this method in child classes to provide block-specific defaults
     */
    protected static function contentDefaultData(): array
    {
        return [];
    }

    /**
     * Get default layout data
     * Override this method to customize layout defaults for specific blocks
     */
    protected static function layoutDefaultData(): array
    {
        return [
            // Desktop Layout
            'desktop_hide' => false,
            'desktop_padding_top' => 0,
            'desktop_padding_bottom' => 0,
            'desktop_padding_left' => 0,
            'desktop_padding_right' => 0,
            'desktop_margin_top' => 0,
            'desktop_margin_bottom' => 0,

            // Mobile Layout
            'mobile_hide' => false,
            'mobile_padding_top' => 0,
            'mobile_padding_bottom' => 0,
            'mobile_padding_left' => 0,
            'mobile_padding_right' => 0,
            'mobile_margin_top' => 0,
            'mobile_margin_bottom' => 0,
        ];
    }

    /**
     * Get default style data
     * Override this method to customize style defaults for specific blocks
     */
    protected static function styleDefaultData(): array
    {
        return [
            'background_color' => 'transparent',
            'text_color' => 'inherit',
            'border_type' => 'none',
            'border_width' => 1,
            'border_color' => '#e5e7eb',
            'border_radius' => 0,
            'box_shadow' => 'none',
        ];
    }

    /**
     * Validate block data with the defined rules
     */
    public function validate(array $data): array
    {
        $validator = Validator::make($data, $this->rules());

        return $validator->validate();
    }

    /**
     * Get validation rules for this block
     * Automatically discovers and merges all *Rules() methods
     */
    protected function rules(): array
    {
        $rules = [];

        // Discover all methods ending with 'Rules'
        $methods = $this->discoverMethodsByPattern('Rules');

        foreach ($methods as $method) {
            $rules = array_merge($rules, $this->$method());
        }

        return $rules;
    }

    /**
     * Get content-specific validation rules
     * Override this method in child classes
     */
    protected function contentRules(): array
    {
        return [];
    }

    /**
     * Get layout validation rules
     * Override to customize for specific blocks
     */
    protected function layoutRules(): array
    {
        return [
            'desktop_hide' => ['boolean'],
            'desktop_padding_top' => ['integer', 'min:0', 'max:256'],
            'desktop_padding_bottom' => ['integer', 'min:0', 'max:256'],
            'desktop_padding_left' => ['integer', 'min:0', 'max:256'],
            'desktop_padding_right' => ['integer', 'min:0', 'max:256'],
            'desktop_margin_top' => ['integer', 'min:-128', 'max:256'],
            'desktop_margin_bottom' => ['integer', 'min:-128', 'max:256'],

            'mobile_hide' => ['boolean'],
            'mobile_padding_top' => ['integer', 'min:0', 'max:256'],
            'mobile_padding_bottom' => ['integer', 'min:0', 'max:256'],
            'mobile_padding_left' => ['integer', 'min:0', 'max:256'],
            'mobile_padding_right' => ['integer', 'min:0', 'max:256'],
            'mobile_margin_top' => ['integer', 'min:-128', 'max:256'],
            'mobile_margin_bottom' => ['integer', 'min:-128', 'max:256'],
        ];
    }

    /**
     * Get style validation rules
     * Override to customize for specific blocks
     */
    protected function styleRules(): array
    {
        return [
            'background_color' => ['nullable', 'string'],
            'text_color' => ['nullable', 'string'],
            'border_type' => ['required', 'in:none,solid,dashed,dotted'],
            'border_width' => ['integer', 'min:0', 'max:8'],
            'border_color' => ['nullable', 'string'],
            'border_radius' => ['integer', 'min:0', 'max:64'],
            'box_shadow' => ['required', 'in:none,sm,md,lg,xl'],
        ];
    }

    /**
     * Get the editor tabs configuration
     * Automatically discovers tabs from *DefaultData() methods
     *
     * Returns array of tabs with:
     * - name: tab identifier
     * - label: display label
     * - icon: Heroicon name (optional)
     */
    public function editorTabs(): array
    {
        $tabs = [];

        // Discover all *DefaultData methods to determine available tabs
        $dataMethods = static::discoverMethodsByPattern('DefaultData');

        foreach ($dataMethods as $method) {
            // Extract tab name (e.g., 'contentDefaultData' -> 'content')
            $tabName = $this->extractTabName($method, 'DefaultData');

            // Check if there's a custom tab configuration method
            $tabConfigMethod = $tabName.'Tab';
            if (method_exists($this, $tabConfigMethod)) {
                $tabs[] = $this->$tabConfigMethod();
            } else {
                // Generate default tab configuration
                $tabs[] = $this->generateDefaultTab($tabName);
            }
        }

        return $tabs;
    }

    /**
     * Helper to get nested data value with default
     */
    protected function get(array $data, string $key, mixed $default = null): mixed
    {
        return data_get($data, $key, $default);
    }

    /**
     * Discover methods in the class that match a specific pattern
     * Ensures consistent ordering: content, layout, style, then alphabetical for custom tabs
     */
    protected static function discoverMethodsByPattern(string $pattern): array
    {
        $reflection = new \ReflectionClass(static::class);
        $methods = [];

        // Get all methods including inherited ones from parent classes
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC | \ReflectionMethod::IS_PROTECTED) as $method) {
            $methodName = $method->getName();

            // Only include methods ending with the pattern
            if (str_ends_with($methodName, $pattern)) {
                // Avoid duplicates
                if (! in_array($methodName, $methods)) {
                    $methods[] = $methodName;
                }
            }
        }

        // Sort with priority order: content, layout, style, then alphabetical
        usort($methods, function ($a, $b) use ($pattern) {
            $priorityOrder = ['content', 'layout', 'style'];

            $tabA = lcfirst(str_replace($pattern, '', $a));
            $tabB = lcfirst(str_replace($pattern, '', $b));

            $posA = array_search($tabA, $priorityOrder);
            $posB = array_search($tabB, $priorityOrder);

            if ($posA !== false && $posB !== false) {
                return $posA - $posB;
            }

            if ($posA !== false) {
                return -1;
            }

            if ($posB !== false) {
                return 1;
            }

            return strcmp($tabA, $tabB);
        });

        return $methods;
    }

    /**
     * Extract tab name from method name
     * Examples:
     * - contentDefaultData -> content
     * - layoutDefaultData -> layout
     * - settingsDefaultData -> settings
     */
    protected function extractTabName(string $methodName, string $suffix): string
    {
        $tabName = str_replace($suffix, '', $methodName);

        return lcfirst($tabName);
    }

    /**
     * Generate default tab configuration based on tab name
     */
    protected function generateDefaultTab(string $tabName): array
    {
        $defaultIcons = [
            'content' => 'document-text',
            'layout' => 'adjustments-horizontal',
            'style' => 'paint-brush',
            'settings' => 'cog-6-tooth',
            'advanced' => 'beaker',
        ];

        return [
            'name' => $tabName,
            'label' => ucfirst($tabName),
            'icon' => $defaultIcons[$tabName] ?? 'square-3-stack-3d',
        ];
    }

    /**
     * Render the block editor view
     * Automatically discovers the editor view based on block type
     */
    public function renderEditor(array $data, string $propertyPrefix = 'blockData'): View
    {
        $viewPath = 'landing-pages.blocks.'.static::type().'.editor';

        return view($viewPath, [
            'data' => array_merge(static::defaultData(), $data),
            'propertyPrefix' => $propertyPrefix,
            'tabs' => $this->editorTabs(),
        ]);
    }

    /**
     * Render the block for the frontend
     * Automatically discovers the render view based on block type
     */
    public function render(array $data, array $context = []): View
    {
        $viewPath = 'landing-pages.blocks.'.static::type().'.render';

        return view($viewPath, array_merge([
            'data' => array_merge(static::defaultData(), $data),
        ], $context));
    }
}

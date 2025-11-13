<?php

namespace App\LandingPages\Blocks;

use Illuminate\Support\Facades\Validator;

abstract class BaseBlock implements BlockInterface
{
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
     */
    protected function rules(): array
    {
        return [];
    }

    /**
     * Get the editor tabs configuration
     *
     * Returns array of tabs with:
     * - name: tab identifier
     * - label: display label
     * - icon: Heroicon name (optional)
     */
    public function editorTabs(): array
    {
        return [
            ['name' => 'content', 'label' => 'Content', 'icon' => 'document-text'],
            ['name' => 'layout', 'label' => 'Layout', 'icon' => 'adjustments-horizontal'],
            ['name' => 'style', 'label' => 'Style', 'icon' => 'paint-brush'],
        ];
    }

    /**
     * Helper to get nested data value with default
     */
    protected function get(array $data, string $key, mixed $default = null): mixed
    {
        return data_get($data, $key, $default);
    }
}

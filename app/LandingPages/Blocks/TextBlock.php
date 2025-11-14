<?php

namespace App\LandingPages\Blocks;

use Illuminate\View\View;

class TextBlock extends BaseBlock
{
    public static function type(): string
    {
        return 'text';
    }

    public static function label(): string
    {
        return 'Text Block';
    }

    public static function description(): string
    {
        return 'Rich text content with formatting and customizable styling';
    }

    public static function icon(): string
    {
        return 'document-text';
    }

    /**
     * Define content-specific defaults for this block
     */
    protected static function contentDefaultData(): array
    {
        return [
            'content' => '<p>Add your content here...</p>',
            'text_align' => 'left',
            'max_width' => 'prose',
        ];
    }

    /**
     * Define content-specific validation rules
     */
    protected function contentRules(): array
    {
        return [
            'content' => ['required', 'string'],
            'text_align' => ['required', 'in:left,center,right,justify'],
            'max_width' => ['required', 'in:sm,prose,lg,xl,full'],
        ];
    }

    public function renderEditor(array $data, string $propertyPrefix = 'blockData'): View
    {
        return view('landing-pages.blocks.text.editor', [
            'data' => array_merge(self::defaultData(), $data),
            'propertyPrefix' => $propertyPrefix,
            'tabs' => $this->editorTabs(),
        ]);
    }

    public function render(array $data): View
    {
        return view('landing-pages.blocks.text.render', [
            'data' => array_merge(self::defaultData(), $data),
        ]);
    }
}

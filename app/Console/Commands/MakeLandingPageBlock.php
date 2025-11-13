<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

use function Laravel\Prompts\text;

class MakeLandingPageBlock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:landing-page-block {name? : The name of the block (e.g., Hero, Feature, Testimonial)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new landing page block with class and Blade templates';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');

        // Ask for name if not provided using Laravel Prompts
        if (! $name) {
            $name = text(
                label: 'What should the block be named?',
                placeholder: 'e.g. Hero',
                required: 'Block name is required',
                hint: 'The name will be converted to StudlyCase for the class name'
            );

            if (! $name) {
                $this->error('Block name is required!');

                return 1;
            }
        }

        // Convert to StudlyCase for class name
        $className = Str::studly($name).'Block';
        $blockType = Str::kebab($name);
        $folderName = Str::kebab($name);

        $this->info("Creating landing page block: {$className}");

        // Create the block class
        if ($this->createBlockClass($className, $blockType)) {
            $this->info("✓ Created block class: app/LandingPages/Blocks/{$className}.php");
        } else {
            $this->error('✗ Block class already exists!');

            return 1;
        }

        // Create the views directory and files
        if ($this->createBlockViews($folderName)) {
            $this->info("✓ Created editor view: resources/views/landing-pages/blocks/{$folderName}/editor.blade.php");
            $this->info("✓ Created render view: resources/views/landing-pages/blocks/{$folderName}/render.blade.php");
        } else {
            $this->error('✗ Views directory already exists!');

            return 1;
        }

        $this->newLine();
        $this->info('✓ Block created successfully!');
        $this->newLine();
        $this->comment('The block has been auto-discovered and is ready to use.');
        $this->comment('Next steps:');
        $this->comment('1. Customize the block\'s icon in the type() method');
        $this->comment('2. Update the defaultData() with your block\'s specific fields');
        $this->comment('3. Add validation rules in the rules() method');
        $this->comment('4. Customize the editor and render views');
        $this->newLine();

        return 0;
    }

    /**
     * Create the block class file
     */
    protected function createBlockClass(string $className, string $blockType): bool
    {
        $path = app_path("LandingPages/Blocks/{$className}.php");

        if (file_exists($path)) {
            return false;
        }

        $stub = $this->getBlockClassStub($className, $blockType);

        file_put_contents($path, $stub);

        return true;
    }

    /**
     * Create the block views directory and files
     */
    protected function createBlockViews(string $folderName): bool
    {
        $viewPath = resource_path("views/landing-pages/blocks/{$folderName}");

        if (is_dir($viewPath)) {
            return false;
        }

        mkdir($viewPath, 0755, true);

        // Create editor.blade.php
        $editorStub = $this->getEditorViewStub();
        file_put_contents("{$viewPath}/editor.blade.php", $editorStub);

        // Create render.blade.php
        $renderStub = $this->getRenderViewStub();
        file_put_contents("{$viewPath}/render.blade.php", $renderStub);

        return true;
    }

    /**
     * Get the block class stub
     */
    protected function getBlockClassStub(string $className, string $blockType): string
    {
        $label = Str::headline($blockType);
        $description = "Description for {$label}";

        return <<<PHP
<?php

namespace App\LandingPages\Blocks;

use Illuminate\View\View;

class {$className} extends BaseBlock
{
    public static function type(): string
    {
        return '{$blockType}';
    }

    public static function label(): string
    {
        return '{$label}';
    }

    public static function description(): string
    {
        return '{$description}';
    }

    public static function icon(): string
    {
        return 'document-text';
    }

    public static function defaultData(): array
    {
        return [
            // Content
            'content' => '',

            // Layout - Desktop
            'desktop_hide' => false,
            'desktop_padding_top' => 64,
            'desktop_padding_bottom' => 64,
            'desktop_padding_left' => 16,
            'desktop_padding_right' => 16,
            'desktop_margin_top' => 0,
            'desktop_margin_bottom' => 0,

            // Layout - Mobile
            'mobile_hide' => false,
            'mobile_padding_top' => 48,
            'mobile_padding_bottom' => 48,
            'mobile_padding_left' => 16,
            'mobile_padding_right' => 16,
            'mobile_margin_top' => 0,
            'mobile_margin_bottom' => 0,

            // Style
            'background_color' => 'transparent',
            'text_color' => 'inherit',
        ];
    }

    protected function rules(): array
    {
        return [
            'content' => ['nullable', 'string'],

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

            'background_color' => ['nullable', 'string'],
            'text_color' => ['nullable', 'string'],
        ];
    }

    public function renderEditor(array \$data, string \$propertyPrefix = 'blockData'): View
    {
        return view('landing-pages.blocks.{$blockType}.editor', [
            'data' => array_merge(self::defaultData(), \$data),
            'propertyPrefix' => \$propertyPrefix,
            'tabs' => \$this->editorTabs(),
        ]);
    }

    public function render(array \$data): View
    {
        return view('landing-pages.blocks.{$blockType}.render', [
            'data' => array_merge(self::defaultData(), \$data),
        ]);
    }
}

PHP;
    }

    /**
     * Get the editor view stub
     */
    protected function getEditorViewStub(): string
    {
        return <<<'BLADE'
<div>
    {{-- Tab Navigation --}}
    <flux:tab.group>
        <flux:tabs wire:model="activeEditorTab" class="mb-6">
            @foreach($tabs as $tab)
                <flux:tab name="{{ $tab['name'] }}" icon="{{ $tab['icon'] ?? null }}">
                    {{ $tab['label'] }}
                </flux:tab>
            @endforeach
        </flux:tabs>

        {{-- Content Tab --}}
        <flux:tab.panel name="content">
            <div class="space-y-6">
                <flux:field>
                    <flux:label>Content</flux:label>
                    <flux:description>Add your content here</flux:description>
                    <flux:input wire:model="{{ $propertyPrefix }}.content" placeholder="Enter content..." />
                </flux:field>
            </div>
        </flux:tab.panel>

        {{-- Layout Tab --}}
        <flux:tab.panel name="layout">
            <div class="space-y-8">
                {{-- Desktop Layout --}}
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Desktop Layout</h3>
                    <div class="space-y-4">
                        <flux:field>
                            <flux:checkbox wire:model="{{ $propertyPrefix }}.desktop_hide">
                                Hide on desktop
                            </flux:checkbox>
                        </flux:field>

                        <div class="grid grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Padding Top (px)</flux:label>
                                <flux:input type="number" wire:model="{{ $propertyPrefix }}.desktop_padding_top" min="0" max="256" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Padding Bottom (px)</flux:label>
                                <flux:input type="number" wire:model="{{ $propertyPrefix }}.desktop_padding_bottom" min="0" max="256" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Padding Left (px)</flux:label>
                                <flux:input type="number" wire:model="{{ $propertyPrefix }}.desktop_padding_left" min="0" max="256" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Padding Right (px)</flux:label>
                                <flux:input type="number" wire:model="{{ $propertyPrefix }}.desktop_padding_right" min="0" max="256" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Margin Top (px)</flux:label>
                                <flux:input type="number" wire:model="{{ $propertyPrefix }}.desktop_margin_top" min="-128" max="256" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Margin Bottom (px)</flux:label>
                                <flux:input type="number" wire:model="{{ $propertyPrefix }}.desktop_margin_bottom" min="-128" max="256" />
                            </flux:field>
                        </div>
                    </div>
                </div>

                {{-- Mobile Layout --}}
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Mobile Layout</h3>
                    <div class="space-y-4">
                        <flux:field>
                            <flux:checkbox wire:model="{{ $propertyPrefix }}.mobile_hide">
                                Hide on mobile
                            </flux:checkbox>
                        </flux:field>

                        <div class="grid grid-cols-2 gap-4">
                            <flux:field>
                                <flux:label>Padding Top (px)</flux:label>
                                <flux:input type="number" wire:model="{{ $propertyPrefix }}.mobile_padding_top" min="0" max="256" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Padding Bottom (px)</flux:label>
                                <flux:input type="number" wire:model="{{ $propertyPrefix }}.mobile_padding_bottom" min="0" max="256" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Padding Left (px)</flux:label>
                                <flux:input type="number" wire:model="{{ $propertyPrefix }}.mobile_padding_left" min="0" max="256" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Padding Right (px)</flux:label>
                                <flux:input type="number" wire:model="{{ $propertyPrefix }}.mobile_padding_right" min="0" max="256" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Margin Top (px)</flux:label>
                                <flux:input type="number" wire:model="{{ $propertyPrefix }}.mobile_margin_top" min="-128" max="256" />
                            </flux:field>

                            <flux:field>
                                <flux:label>Margin Bottom (px)</flux:label>
                                <flux:input type="number" wire:model="{{ $propertyPrefix }}.mobile_margin_bottom" min="-128" max="256" />
                            </flux:field>
                        </div>
                    </div>
                </div>
            </div>
        </flux:tab.panel>

        {{-- Style Tab --}}
        <flux:tab.panel name="style">
            <div class="space-y-6">
                {{-- Colors --}}
                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Background Color</flux:label>
                        <flux:input type="color" wire:model="{{ $propertyPrefix }}.background_color" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Text Color</flux:label>
                        <flux:input type="color" wire:model="{{ $propertyPrefix }}.text_color" />
                    </flux:field>
                </div>
            </div>
        </flux:tab.panel>
    </flux:tab.group>
</div>
BLADE;
    }

    /**
     * Get the render view stub
     */
    protected function getRenderViewStub(): string
    {
        return <<<'BLADE'
@php
    $hideClasses = [];
    if ($data['desktop_hide']) {
        $hideClasses[] = 'hidden';
        if (!$data['mobile_hide']) {
            $hideClasses[] = 'md:block';
        }
    }
    if ($data['mobile_hide'] && !$data['desktop_hide']) {
        $hideClasses[] = 'hidden md:block';
    }
@endphp

<div
    class="mx-auto {{ implode(' ', $hideClasses) }}"
    style="
        padding-top: {{ $data['mobile_padding_top'] }}px;
        padding-bottom: {{ $data['mobile_padding_bottom'] }}px;
        padding-left: {{ $data['mobile_padding_left'] }}px;
        padding-right: {{ $data['mobile_padding_right'] }}px;
        margin-top: {{ $data['mobile_margin_top'] }}px;
        margin-bottom: {{ $data['mobile_margin_bottom'] }}px;
        background-color: {{ $data['background_color'] }};
        color: {{ $data['text_color'] }};

        @media (min-width: 768px) {
            padding-top: {{ $data['desktop_padding_top'] }}px;
            padding-bottom: {{ $data['desktop_padding_bottom'] }}px;
            padding-left: {{ $data['desktop_padding_left'] }}px;
            padding-right: {{ $data['desktop_padding_right'] }}px;
            margin-top: {{ $data['desktop_margin_top'] }}px;
            margin-bottom: {{ $data['desktop_margin_bottom'] }}px;
        }
    "
>
    {{-- Your block content goes here --}}
    <div class="max-w-7xl mx-auto">
        {{ $data['content'] }}
    </div>
</div>
BLADE;
    }
}

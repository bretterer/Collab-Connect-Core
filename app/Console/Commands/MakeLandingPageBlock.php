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

    /**
     * Define content-specific defaults for this block
     */
    protected static function contentDefaultData(): array
    {
        return [
            'content' => '',
        ];
    }

    /**
     * Define content-specific validation rules
     */
    protected function contentRules(): array
    {
        return [
            'content' => ['nullable', 'string'],
        ];
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
<x-landing-page-block.editor :tabs="$tabs" :property-prefix="$propertyPrefix">
    {{-- Content Tab Fields --}}
    <div class="space-y-6">
        <flux:field>
            <flux:label>Content</flux:label>
            <flux:description>Add your content here</flux:description>
            <flux:input wire:model="{{ $propertyPrefix }}.content" placeholder="Enter content..." />
        </flux:field>
    </div>
</x-landing-page-block.editor>
BLADE;
    }

    /**
     * Get the render view stub
     */
    protected function getRenderViewStub(): string
    {
        return <<<'BLADE'
<x-landing-page-block.wrapper :data="$data">
    {{-- Your block content goes here --}}
    <div class="max-w-7xl mx-auto">
        {{ $data['content'] }}
    </div>
</x-landing-page-block.wrapper>
BLADE;
    }
}

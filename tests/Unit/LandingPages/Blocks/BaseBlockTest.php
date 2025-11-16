<?php

declare(strict_types=1);

namespace Tests\Unit\LandingPages\Blocks;

use App\LandingPages\Blocks\BaseBlock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\View;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View as ViewInstance;
use Tests\TestCase;

// Test fixture: Simple block with only content
class SimpleTestBlock extends BaseBlock
{
    public static function type(): string
    {
        return 'simple-test';
    }

    public static function label(): string
    {
        return 'Simple Test Block';
    }

    public static function description(): string
    {
        return 'A simple test block';
    }

    public static function icon(): string
    {
        return 'test-icon';
    }

    protected static function contentDefaultData(): array
    {
        return [
            'title' => 'Default Title',
            'description' => 'Default Description',
        ];
    }

    protected function contentRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];
    }

    public function renderEditor(array $data, string $propertyPrefix = 'blockData'): ViewInstance
    {
        View::addLocation(__DIR__);

        return view('test-editor', compact('data'));
    }

    public function render(array $data): ViewInstance
    {
        View::addLocation(__DIR__);

        return view('test-render', compact('data'));
    }
}

// Test fixture: Block with custom settings tab
class AdvancedTestBlock extends BaseBlock
{
    public static function type(): string
    {
        return 'advanced-test';
    }

    public static function label(): string
    {
        return 'Advanced Test Block';
    }

    public static function description(): string
    {
        return 'An advanced test block with custom tabs';
    }

    public static function icon(): string
    {
        return 'test-icon';
    }

    protected static function contentDefaultData(): array
    {
        return [
            'content' => 'Test content',
        ];
    }

    protected static function settingsDefaultData(): array
    {
        return [
            'autoplay' => false,
            'volume' => 80,
        ];
    }

    protected function contentRules(): array
    {
        return [
            'content' => ['required', 'string'],
        ];
    }

    protected function settingsRules(): array
    {
        return [
            'autoplay' => ['boolean'],
            'volume' => ['integer', 'min:0', 'max:100'],
        ];
    }

    protected function settingsTab(): array
    {
        return [
            'name' => 'settings',
            'label' => 'Custom Settings',
            'icon' => 'cog-8-tooth',
        ];
    }

    public function renderEditor(array $data, string $propertyPrefix = 'blockData'): ViewInstance
    {
        View::addLocation(__DIR__);

        return view('test-editor', compact('data'));
    }

    public function render(array $data): ViewInstance
    {
        View::addLocation(__DIR__);

        return view('test-render', compact('data'));
    }
}

// Test fixture: Block with custom layout overrides
class CustomLayoutTestBlock extends BaseBlock
{
    public static function type(): string
    {
        return 'custom-layout-test';
    }

    public static function label(): string
    {
        return 'Custom Layout Test Block';
    }

    public static function description(): string
    {
        return 'Block with custom layout defaults';
    }

    public static function icon(): string
    {
        return 'test-icon';
    }

    protected static function contentDefaultData(): array
    {
        return ['text' => 'Test'];
    }

    protected static function layoutDefaultData(): array
    {
        return array_merge(parent::layoutDefaultData(), [
            'desktop_padding_top' => 100,
            'desktop_padding_bottom' => 100,
        ]);
    }

    protected static function styleDefaultData(): array
    {
        return [
            'background_color' => '#ffffff',
            'text_color' => '#000000',
        ];
    }

    protected function contentRules(): array
    {
        return ['text' => ['required', 'string']];
    }

    protected function styleRules(): array
    {
        return [
            'background_color' => ['required', 'string'],
            'text_color' => ['required', 'string'],
        ];
    }

    public function renderEditor(array $data, string $propertyPrefix = 'blockData'): ViewInstance
    {
        View::addLocation(__DIR__);

        return view('test-editor', compact('data'));
    }

    public function render(array $data): ViewInstance
    {
        View::addLocation(__DIR__);

        return view('test-render', compact('data'));
    }
}

class BaseBlockTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create minimal blade views for testing
        $viewsPath = __DIR__;
        if (! file_exists("{$viewsPath}/test-editor.blade.php")) {
            file_put_contents("{$viewsPath}/test-editor.blade.php", '<div>{{ json_encode($data) }}</div>');
        }
        if (! file_exists("{$viewsPath}/test-render.blade.php")) {
            file_put_contents("{$viewsPath}/test-render.blade.php", '<div>{{ json_encode($data) }}</div>');
        }
    }

    protected function tearDown(): void
    {
        // Clean up test views
        $viewsPath = __DIR__;
        @unlink("{$viewsPath}/test-editor.blade.php");
        @unlink("{$viewsPath}/test-render.blade.php");

        parent::tearDown();
    }

    public function test_default_data_auto_discovers_and_merges_all_default_data_methods()
    {
        $data = SimpleTestBlock::defaultData();

        // Should have content data
        $this->assertArrayHasKey('title', $data);
        $this->assertEquals('Default Title', $data['title']);
        $this->assertArrayHasKey('description', $data);
        $this->assertEquals('Default Description', $data['description']);

        // Should have layout data from BaseBlock
        $this->assertArrayHasKey('desktop_padding_top', $data);
        $this->assertEquals(32, $data['desktop_padding_top']);
        $this->assertArrayHasKey('mobile_padding_top', $data);
        $this->assertEquals(24, $data['mobile_padding_top']);

        // Should have style data from BaseBlock
        $this->assertArrayHasKey('background_color', $data);
        $this->assertEquals('transparent', $data['background_color']);
        $this->assertArrayHasKey('border_type', $data);
        $this->assertEquals('none', $data['border_type']);
    }

    public function test_default_data_includes_custom_tab_data()
    {
        $data = AdvancedTestBlock::defaultData();

        // Should have content data
        $this->assertArrayHasKey('content', $data);
        $this->assertEquals('Test content', $data['content']);

        // Should have settings data
        $this->assertArrayHasKey('autoplay', $data);
        $this->assertFalse($data['autoplay']);
        $this->assertArrayHasKey('volume', $data);
        $this->assertEquals(80, $data['volume']);

        // Should have layout and style from BaseBlock
        $this->assertArrayHasKey('desktop_padding_top', $data);
        $this->assertArrayHasKey('background_color', $data);
    }

    public function test_default_data_respects_overridden_values()
    {
        $data = CustomLayoutTestBlock::defaultData();

        // Custom layout values should override parent
        $this->assertEquals(100, $data['desktop_padding_top']);
        $this->assertEquals(100, $data['desktop_padding_bottom']);

        // Non-overridden layout values should use parent defaults
        $this->assertEquals(16, $data['desktop_padding_left']);
        $this->assertEquals(16, $data['desktop_padding_right']);

        // Custom style (completely replaced)
        $this->assertArrayHasKey('background_color', $data);
        $this->assertEquals('#ffffff', $data['background_color']);
        $this->assertArrayHasKey('text_color', $data);
        $this->assertEquals('#000000', $data['text_color']);
        $this->assertArrayNotHasKey('border_type', $data); // Parent style was replaced
    }

    public function test_rules_auto_discovers_and_merges_all_rules_methods()
    {
        $block = new SimpleTestBlock;
        $rules = $this->invokePrivateMethod($block, 'rules');

        // Should have content rules
        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('description', $rules);

        // Should have layout rules from BaseBlock
        $this->assertArrayHasKey('desktop_padding_top', $rules);
        $this->assertArrayHasKey('mobile_hide', $rules);

        // Should have style rules from BaseBlock
        $this->assertArrayHasKey('background_color', $rules);
        $this->assertArrayHasKey('border_type', $rules);
    }

    public function test_rules_includes_custom_tab_rules()
    {
        $block = new AdvancedTestBlock;
        $rules = $this->invokePrivateMethod($block, 'rules');

        // Should have content rules
        $this->assertArrayHasKey('content', $rules);

        // Should have settings rules
        $this->assertArrayHasKey('autoplay', $rules);
        $this->assertArrayHasKey('volume', $rules);
        $this->assertContains('integer', $rules['volume']);
    }

    public function test_rules_respects_overridden_validation()
    {
        $block = new CustomLayoutTestBlock;
        $rules = $this->invokePrivateMethod($block, 'rules');

        // Should have custom style rules
        $this->assertArrayHasKey('background_color', $rules);
        $this->assertContains('required', $rules['background_color']);

        // Should NOT have parent border rules (style was overridden)
        $this->assertArrayNotHasKey('border_type', $rules);

        // Should still have layout rules (not overridden)
        $this->assertArrayHasKey('desktop_padding_top', $rules);
    }

    public function test_editor_tabs_auto_discovers_tabs_from_default_data_methods()
    {
        $block = new SimpleTestBlock;
        $tabs = $block->editorTabs();

        $this->assertCount(3, $tabs);

        // Check tab names
        $tabNames = array_column($tabs, 'name');
        $this->assertContains('content', $tabNames);
        $this->assertContains('layout', $tabNames);
        $this->assertContains('style', $tabNames);
    }

    public function test_editor_tabs_includes_custom_tabs()
    {
        $block = new AdvancedTestBlock;
        $tabs = $block->editorTabs();

        $this->assertCount(4, $tabs);

        $tabNames = array_column($tabs, 'name');
        $this->assertContains('content', $tabNames);
        $this->assertContains('layout', $tabNames);
        $this->assertContains('style', $tabNames);
        $this->assertContains('settings', $tabNames);
    }

    public function test_editor_tabs_maintains_correct_ordering()
    {
        $block = new AdvancedTestBlock;
        $tabs = $block->editorTabs();

        $tabNames = array_column($tabs, 'name');

        // Should be: content, layout, style, settings (alphabetical after core tabs)
        $this->assertEquals('content', $tabNames[0]);
        $this->assertEquals('layout', $tabNames[1]);
        $this->assertEquals('style', $tabNames[2]);
        $this->assertEquals('settings', $tabNames[3]);
    }

    public function test_editor_tabs_uses_custom_tab_configuration_when_provided()
    {
        $block = new AdvancedTestBlock;
        $tabs = $block->editorTabs();

        $settingsTab = collect($tabs)->firstWhere('name', 'settings');

        $this->assertNotNull($settingsTab);
        $this->assertEquals('Custom Settings', $settingsTab['label']);
        $this->assertEquals('cog-8-tooth', $settingsTab['icon']);
    }

    public function test_editor_tabs_generates_default_configuration_when_no_custom_tab_method_exists()
    {
        $block = new SimpleTestBlock;
        $tabs = $block->editorTabs();

        $contentTab = collect($tabs)->firstWhere('name', 'content');
        $layoutTab = collect($tabs)->firstWhere('name', 'layout');
        $styleTab = collect($tabs)->firstWhere('name', 'style');

        $this->assertEquals('Content', $contentTab['label']);
        $this->assertEquals('document-text', $contentTab['icon']);

        $this->assertEquals('Layout', $layoutTab['label']);
        $this->assertEquals('adjustments-horizontal', $layoutTab['icon']);

        $this->assertEquals('Style', $styleTab['label']);
        $this->assertEquals('paint-brush', $styleTab['icon']);
    }

    public function test_validation_works_with_auto_discovered_rules()
    {
        $block = new SimpleTestBlock;

        // Valid data
        $validData = SimpleTestBlock::defaultData();
        $validData['title'] = 'Valid Title';

        $validated = $block->validate($validData);
        $this->assertIsArray($validated);
        $this->assertEquals('Valid Title', $validated['title']);
    }

    public function test_validation_fails_with_invalid_data()
    {
        $block = new SimpleTestBlock;

        $invalidData = SimpleTestBlock::defaultData();
        $invalidData['title'] = ''; // Required field

        $this->expectException(ValidationException::class);
        $block->validate($invalidData);
    }

    public function test_validation_works_with_custom_tab_rules()
    {
        $block = new AdvancedTestBlock;

        $validData = AdvancedTestBlock::defaultData();
        $validData['content'] = 'Test content';
        $validData['volume'] = 50;

        $validated = $block->validate($validData);
        $this->assertEquals(50, $validated['volume']);
    }

    public function test_validation_enforces_custom_tab_constraints()
    {
        $block = new AdvancedTestBlock;

        $invalidData = AdvancedTestBlock::defaultData();
        $invalidData['content'] = 'Test content';
        $invalidData['volume'] = 150; // Exceeds max:100

        $this->expectException(ValidationException::class);
        $block->validate($invalidData);
    }

    public function test_block_can_merge_custom_data_with_defaults()
    {
        $customData = ['title' => 'Custom Title'];
        $merged = array_merge(SimpleTestBlock::defaultData(), $customData);

        $this->assertEquals('Custom Title', $merged['title']);
        $this->assertEquals('Default Description', $merged['description']); // From defaults
        $this->assertEquals(32, $merged['desktop_padding_top']); // From BaseBlock
    }

    public function test_default_data_is_consistent_across_multiple_calls()
    {
        $data1 = SimpleTestBlock::defaultData();
        $data2 = SimpleTestBlock::defaultData();

        $this->assertEquals($data1, $data2);
    }

    public function test_block_instances_have_correct_metadata()
    {
        $this->assertEquals('simple-test', SimpleTestBlock::type());
        $this->assertEquals('Simple Test Block', SimpleTestBlock::label());
        $this->assertEquals('A simple test block', SimpleTestBlock::description());
        $this->assertEquals('test-icon', SimpleTestBlock::icon());
    }

    /**
     * Helper to invoke private/protected methods for testing
     */
    private function invokePrivateMethod($object, string $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}

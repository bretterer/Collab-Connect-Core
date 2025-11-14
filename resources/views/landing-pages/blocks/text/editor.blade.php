<x-landing-page-block.editor :tabs="$tabs" :property-prefix="$propertyPrefix">
    {{-- Content Tab Fields --}}
    <div class="space-y-6">
        {{-- Rich Text Editor --}}
        <flux:editor
            wire:model="{{ $propertyPrefix }}.content"
            label="Content"
            description="Use the editor toolbar to format your text with headings, bold, italic, lists, colors, and more."
            placeholder="Enter your content here..."
            toolbar="heading | bold italic underline strike | color | bullet ordered blockquote | link | align"
        />

        {{-- Text Alignment --}}
        <flux:field>
            <flux:label>Text Alignment</flux:label>
            <flux:select wire:model="{{ $propertyPrefix }}.text_align">
                <option value="left">Left</option>
                <option value="center">Center</option>
                <option value="right">Right</option>
                <option value="justify">Justify</option>
            </flux:select>
        </flux:field>

        {{-- Max Width --}}
        <flux:field>
            <flux:label>Maximum Width</flux:label>
            <flux:select wire:model="{{ $propertyPrefix }}.max_width">
                <option value="sm">Small (640px)</option>
                <option value="prose">Prose (65ch)</option>
                <option value="lg">Large (1024px)</option>
                <option value="xl">Extra Large (1280px)</option>
                <option value="full">Full Width</option>
            </flux:select>
        </flux:field>
    </div>
</x-landing-page-block.editor>

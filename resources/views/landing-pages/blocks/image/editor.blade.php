<x-landing-page-block.editor :tabs="$tabs" :property-prefix="$propertyPrefix">
    {{-- Content Tab Fields --}}
    <div class="space-y-6">
        <flux:field>
            <flux:label>Image URL</flux:label>
            <flux:description>Enter the URL of the image to display</flux:description>
            <flux:input wire:model="{{ $propertyPrefix }}.content" placeholder="https://example.com/image.jpg" />
        </flux:field>
    </div>
</x-landing-page-block.editor>

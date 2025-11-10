<div class="space-y-4">
    <flux:field>
        <flux:label>Image URL</flux:label>
        <flux:input wire:model="blockData.url" placeholder="https://example.com/image.jpg" required />
        <flux:description>Enter the URL of the image you want to display</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>Alt Text</flux:label>
        <flux:input wire:model="blockData.alt" placeholder="Description of the image" />
        <flux:description>Alternative text for accessibility and SEO</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>Caption (Optional)</flux:label>
        <flux:input wire:model="blockData.caption" placeholder="Image caption" />
        <flux:description>Text displayed below the image</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>Image Width</flux:label>
        <flux:select wire:model="blockData.width">
            <option value="full">Full Width</option>
            <option value="large">Large (max 1280px)</option>
            <option value="medium">Medium (max 768px)</option>
            <option value="small">Small (max 512px)</option>
        </flux:select>
        <flux:description>Maximum width of the image</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>Alignment</flux:label>
        <flux:select wire:model="blockData.alignment">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
        </flux:select>
    </flux:field>

    <flux:field>
        <flux:label>Border Radius</flux:label>
        <flux:select wire:model="blockData.rounded">
            <option value="none">None</option>
            <option value="sm">Small</option>
            <option value="md">Medium</option>
            <option value="lg">Large</option>
            <option value="xl">Extra Large</option>
            <option value="full">Full (Circle)</option>
        </flux:select>
    </flux:field>
</div>

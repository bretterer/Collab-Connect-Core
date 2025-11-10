<div class="space-y-4">
    <flux:field>
        <flux:label>Content</flux:label>
        <flux:editor wire:model="blockData.content" placeholder="Enter your content here..." toolbar="heading | bold italic underline strike | color | bullet ordered blockquote | link | align" />
        <flux:description>Use the editor toolbar to format your text with headings, bold, italic, lists, colors, and more.</flux:description>
    </flux:field>

    <flux:field>
        <flux:label>Text Alignment</flux:label>
        <flux:select wire:model="blockData.text_align">
            <option value="left">Left</option>
            <option value="center">Center</option>
            <option value="right">Right</option>
            <option value="justify">Justify</option>
        </flux:select>
    </flux:field>

    <flux:field>
        <flux:label>Max Width</flux:label>
        <flux:select wire:model="blockData.max_width">
            <option value="full">Full Width</option>
            <option value="prose">Prose (65ch)</option>
            <option value="narrow">Narrow (50ch)</option>
            <option value="wide">Wide (80ch)</option>
        </flux:select>
        <flux:description>Controls the maximum width of the text content</flux:description>
    </flux:field>
</div>

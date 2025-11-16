@props(['propertyPrefix' => 'blockData'])

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

        {{-- Border --}}
        <flux:field>
            <flux:label>Border Type</flux:label>
            <flux:select wire:model="{{ $propertyPrefix }}.border_type">
                <option value="none">None</option>
                <option value="solid">Solid</option>
                <option value="dashed">Dashed</option>
                <option value="dotted">Dotted</option>
            </flux:select>
        </flux:field>

        <div class="grid grid-cols-2 gap-4">
            <flux:field>
                <flux:label>Border Width (px)</flux:label>
                <flux:input type="number" wire:model="{{ $propertyPrefix }}.border_width" min="0" max="8" />
            </flux:field>

            <flux:field>
                <flux:label>Border Color</flux:label>
                <flux:input type="color" wire:model="{{ $propertyPrefix }}.border_color" />
            </flux:field>
        </div>

        <flux:field>
            <flux:label>Border Radius (px)</flux:label>
            <flux:input type="number" wire:model="{{ $propertyPrefix }}.border_radius" min="0" max="64" />
        </flux:field>

        {{-- Shadow --}}
        <flux:field>
            <flux:label>Box Shadow</flux:label>
            <flux:select wire:model="{{ $propertyPrefix }}.box_shadow">
                <option value="none">None</option>
                <option value="sm">Small</option>
                <option value="md">Medium</option>
                <option value="lg">Large</option>
                <option value="xl">Extra Large</option>
            </flux:select>
        </flux:field>
    </div>
</flux:tab.panel>

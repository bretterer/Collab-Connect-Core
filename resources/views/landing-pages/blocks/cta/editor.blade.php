<x-landing-page-block.editor :tabs="$tabs" :property-prefix="$propertyPrefix">
    {{-- Content Tab Fields --}}
    <div class="space-y-6">
        {{-- Button Text --}}
        <flux:field>
            <flux:label>Button Text</flux:label>
            <flux:description>The text displayed on the button</flux:description>
            <flux:input wire:model="{{ $propertyPrefix }}.text" placeholder="Click Here" />
        </flux:field>

        {{-- Button Action --}}
        <flux:field>
            <flux:label>Button Action</flux:label>
            <flux:description>What happens when the button is clicked</flux:description>
            <flux:select wire:model.live="{{ $propertyPrefix }}.action">
                <option value="url">Open A URL</option>
                <option value="two_step_optin">Open Two Step Optin Popup</option>
                <option value="section">Go to Section on Page</option>
            </flux:select>
        </flux:field>

        {{-- URL Field (conditional - only for 'url' action) --}}
        @if(data_get($data, 'action') === 'url')
            <flux:field>
                <flux:label>URL</flux:label>
                <flux:description>The destination URL</flux:description>
                <flux:input wire:model="{{ $propertyPrefix }}.url" type="url" placeholder="https://example.com" />
            </flux:field>

            <flux:field>
                <flux:label>
                    <flux:checkbox wire:model="{{ $propertyPrefix }}.open_new_tab" />
                    Open in New Tab
                </flux:label>
                <flux:description>Opens the URL in a new browser tab</flux:description>
            </flux:field>
        @endif

        {{-- Section ID Field (conditional - only for 'section' action) --}}
        @if(data_get($data, 'action') === 'section')
            <flux:field>
                <flux:label>Section ID</flux:label>
                <flux:description>The ID of the section to scroll to (without #)</flux:description>
                <flux:input wire:model="{{ $propertyPrefix }}.section_id" placeholder="section-name" />
            </flux:field>
        @endif

        {{-- Button Background Color --}}
        <flux:field>
            <flux:label>Button Background Color</flux:label>
            <flux:description>The background color of the button</flux:description>
            <flux:input type="color" wire:model="{{ $propertyPrefix }}.button_bg_color" />
        </flux:field>

        {{-- Button Text Color --}}
        <flux:field>
            <flux:label>Button Text Color</flux:label>
            <flux:description>The text color of the button</flux:description>
            <flux:input type="color" wire:model="{{ $propertyPrefix }}.button_text_color" />
        </flux:field>

        {{-- Button Width --}}
        <flux:field>
            <flux:label>Button Width</flux:label>
            <flux:description>How wide the button should be</flux:description>
            <div class="flex gap-4">
                <flux:radio.group wire:model="{{ $propertyPrefix }}.button_width">
                    <flux:radio value="full" label="Full Width" />
                    <flux:radio value="auto" label="Auto Width" />
                </flux:radio.group>
            </div>
        </flux:field>

        {{-- Button Style --}}
        <flux:field>
            <flux:label>Button Style</flux:label>
            <flux:description>The visual style of the button</flux:description>
            <div class="flex gap-4">
                <flux:radio.group wire:model="{{ $propertyPrefix }}.button_style">
                    <flux:radio value="solid" label="Solid" />
                    <flux:radio value="outline" label="Outline" />
                </flux:radio.group>
            </div>
        </flux:field>

        {{-- Button Size --}}
        <flux:field>
            <flux:label>Button Size</flux:label>
            <flux:description>The size of the button</flux:description>
            <div class="flex gap-4">
                <flux:radio.group wire:model="{{ $propertyPrefix }}.button_size">
                    <flux:radio value="small" label="Small" />
                    <flux:radio value="medium" label="Medium" />
                    <flux:radio value="large" label="Large" />
                </flux:radio.group>
            </div>
        </flux:field>

        {{-- Border Radius --}}
        <flux:field>
            <flux:label>Border Radius</flux:label>
            <flux:description>The roundness of the button corners (0-50px)</flux:description>
            <div class="flex gap-4 items-center">
                <input
                    type="range"
                    wire:model.live="{{ $propertyPrefix }}.border_radius"
                    min="0"
                    max="50"
                    class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                />
                <flux:input
                    wire:model="{{ $propertyPrefix }}.border_radius"
                    type="number"
                    min="0"
                    max="50"
                    class="w-20"
                />
            </div>
        </flux:field>
    </div>
</x-landing-page-block.editor>

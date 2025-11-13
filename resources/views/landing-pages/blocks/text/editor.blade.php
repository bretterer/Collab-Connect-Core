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
    </flux:tab.group>
</div>

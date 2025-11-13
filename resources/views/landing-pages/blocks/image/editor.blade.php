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
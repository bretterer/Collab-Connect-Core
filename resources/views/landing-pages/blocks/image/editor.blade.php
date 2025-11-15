<x-landing-page-block.editor :tabs="$tabs" :property-prefix="$propertyPrefix">
    {{-- Content Tab Fields --}}
    <div class="space-y-6">
        @livewire('landing-pages.image-uploader', [
            'imageUrl' => $data['image_url'] ?? '',
            'imageAlt' => $data['image_alt'] ?? '',
            'imageWidth' => $data['image_width'] ?? null,
            'imageHeight' => $data['image_height'] ?? null,
            'brightness' => $data['brightness'] ?? 0,
            'contrast' => $data['contrast'] ?? 0,
            'saturation' => $data['saturation'] ?? 0,
            'blur' => $data['blur'] ?? 0,
            'propertyPrefix' => $propertyPrefix,
        ])
    </div>

    {{-- Settings Tab (custom) --}}
    <x-slot name="additionalTabs">
        <flux:tab.panel name="settings">
            <div class="space-y-6">
                <flux:heading size="sm">Display Settings</flux:heading>

                {{-- Display Width --}}
                <flux:field>
                    <flux:label>Display Width (px)</flux:label>
                    <flux:description>Set a specific width for the image display (leave empty for full width)</flux:description>
                    <flux:input
                        wire:model="{{ $propertyPrefix }}.display_width"
                        type="number"
                        min="1"
                        max="5000"
                        placeholder="Auto"
                    />
                </flux:field>

                {{-- Display Height --}}
                <flux:field>
                    <flux:label>Display Height (px)</flux:label>
                    <flux:description>Set a specific height for the image display (leave empty for auto)</flux:description>
                    <flux:input
                        wire:model="{{ $propertyPrefix }}.display_height"
                        type="number"
                        min="1"
                        max="5000"
                        placeholder="Auto"
                    />
                </flux:field>

                {{-- Maintain Aspect Ratio --}}
                <flux:field>
                    <flux:checkbox wire:model="{{ $propertyPrefix }}.maintain_aspect_ratio">
                        Maintain aspect ratio when resizing
                    </flux:checkbox>
                    <flux:description>When enabled, the image will scale proportionally</flux:description>
                </flux:field>

                {{-- Alignment --}}
                <flux:field>
                    <flux:label>Image Alignment</flux:label>
                    <flux:description>How the image should be aligned within its container</flux:description>
                    <flux:select wire:model="{{ $propertyPrefix }}.alignment">
                        <option value="left">Left</option>
                        <option value="center">Center</option>
                        <option value="right">Right</option>
                    </flux:select>
                </flux:field>
            </div>
        </flux:tab.panel>
    </x-slot>

    @script
    <script>
        // Listen for image upload events from the uploader component
        Livewire.on('imageUploaded', (event) => {
            const data = event[0];
            @this.set('{{ $propertyPrefix }}.image_url', data.imageUrl);
            @this.set('{{ $propertyPrefix }}.image_width', data.imageWidth);
            @this.set('{{ $propertyPrefix }}.image_height', data.imageHeight);
        });

        Livewire.on('imageAdjustmentsUpdated', (event) => {
            const data = event[0];
            @this.set('{{ $propertyPrefix }}.brightness', data.brightness);
            @this.set('{{ $propertyPrefix }}.contrast', data.contrast);
            @this.set('{{ $propertyPrefix }}.saturation', data.saturation);
            @this.set('{{ $propertyPrefix }}.blur', data.blur);
        });

        Livewire.on('imageDeleted', () => {
            @this.set('{{ $propertyPrefix }}.image_url', '');
            @this.set('{{ $propertyPrefix }}.image_width', null);
            @this.set('{{ $propertyPrefix }}.image_height', null);
            @this.set('{{ $propertyPrefix }}.brightness', 0);
            @this.set('{{ $propertyPrefix }}.contrast', 0);
            @this.set('{{ $propertyPrefix }}.saturation', 0);
            @this.set('{{ $propertyPrefix }}.blur', 0);
        });
    </script>
    @endscript
</x-landing-page-block.editor>

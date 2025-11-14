@props(['tabs', 'propertyPrefix' => 'blockData'])

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

        {{-- Content Tab (provided by the block) --}}
        <flux:tab.panel name="content">
            {{ $slot }}
        </flux:tab.panel>

        {{-- Layout Tab (auto-generated from BaseBlock) --}}
        <x-landing-page-block.tabs.layout :property-prefix="$propertyPrefix" />

        {{-- Style Tab (auto-generated from BaseBlock) --}}
        <x-landing-page-block.tabs.style :property-prefix="$propertyPrefix" />

        {{-- Additional custom tabs can be added by the block --}}
        @isset($additionalTabs)
            {{ $additionalTabs }}
        @endisset
    </flux:tab.group>
</div>

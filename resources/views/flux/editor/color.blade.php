@php
$colors = [
    ['label' => 'Default', 'value' => null],
    ['label' => 'Red', 'value' => '#ef4444'],
    ['label' => 'Orange', 'value' => '#f97316'],
    ['label' => 'Yellow', 'value' => '#eab308'],
    ['label' => 'Green', 'value' => '#22c55e'],
    ['label' => 'Blue', 'value' => '#3b82f6'],
    ['label' => 'Purple', 'value' => '#a855f7'],
    ['label' => 'Pink', 'value' => '#ec4899'],
];
@endphp

<flux:dropdown position="bottom start">
    <flux:editor.button icon="paint-brush" tooltip="Text Color">
        <span class="text-xs">A</span>
    </flux:editor.button>

    <flux:menu class="w-40">
        @foreach($colors as $color)
            <flux:menu.item
                x-on:click="$el.closest('[data-flux-editor]').__editor.chain().focus().{{ $color['value'] ? "setColor('{$color['value']}')" : 'unsetColor()' }}.run()"
            >
                <div class="flex items-center gap-2">
                    @if($color['value'])
                        <div class="w-4 h-4 rounded border border-gray-300" style="background-color: {{ $color['value'] }}"></div>
                    @else
                        <div class="w-4 h-4 rounded border border-gray-300 bg-white"></div>
                    @endif
                    <span>{{ $color['label'] }}</span>
                </div>
            </flux:menu.item>
        @endforeach
    </flux:menu>
</flux:dropdown>

@php
    // Support both Livewire component properties and @include with settings array
    $enabled = $enabled ?? ($settings['enabled'] ?? true);
    $items = $items ?? ($settings['items'] ?? []);
    $title = $title ?? ($settings['title'] ?? 'My Rates');
    $size = $size ?? ($settings['size'] ?? 'medium');
    $themeColor = $themeColor ?? ($designSettings['themeColor'] ?? '#dc2626');
@endphp

@if($enabled && count($items) > 0)
    <div class="bg-white rounded-2xl p-6 shadow-lg">
        <h2
            class="font-bold text-center mb-4 {{ $size === 'large' ? 'text-2xl' : ($size === 'small' ? 'text-lg' : 'text-xl') }}"
            style="color: {{ $themeColor }}"
        >
            {{ $title }}
        </h2>
        <div class="space-y-3">
            @foreach($items as $rate)
                @if($rate['enabled'])
                    <div class="flex justify-between items-center p-4 bg-gray-50 rounded-xl">
                        <div>
                            <div class="font-semibold text-gray-900 {{ $size === 'large' ? 'text-lg' : ($size === 'small' ? 'text-sm' : 'text-base') }}">
                                {{ $rate['platform'] }}
                            </div>
                            @if($rate['description'])
                                <div class="text-gray-500 {{ $size === 'large' ? 'text-base' : ($size === 'small' ? 'text-xs' : 'text-sm') }}">
                                    {{ $rate['description'] }}
                                </div>
                            @endif
                        </div>
                        <div class="font-bold text-gray-900 {{ $size === 'large' ? 'text-xl' : ($size === 'small' ? 'text-base' : 'text-lg') }}">
                            {{ $rate['rate'] }}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@endif

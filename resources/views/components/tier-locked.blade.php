@props([
    'locked' => false,
    'requiredTier' => null,
    'currentTier' => null,
    'feature' => null,
    'title' => 'Upgrade Required',
    'description' => null,
    'showOverlay' => true,
    'overlayStyle' => 'blur', // 'blur', 'dim', 'badge-only'
    'badgePosition' => 'top-right', // 'top-right', 'top-left', 'inline'
])

@php
    $overlayClasses = match($overlayStyle) {
        'blur' => 'backdrop-blur-sm bg-white/30 dark:bg-gray-900/30',
        'dim' => 'bg-white/60 dark:bg-gray-900/60',
        'badge-only' => '',
        default => 'backdrop-blur-sm bg-white/30 dark:bg-gray-900/30',
    };

    $badgePositionClasses = match($badgePosition) {
        'top-right' => 'absolute top-2 right-2',
        'top-left' => 'absolute top-2 left-2',
        'inline' => '',
        default => 'absolute top-2 right-2',
    };

    $defaultDescription = $requiredTier
        ? "This feature is available on the " . ucfirst($requiredTier) . " plan."
        : "Upgrade your plan to access this feature.";

    $finalDescription = $description ?? $defaultDescription;
@endphp

<div {{ $attributes->merge(['class' => 'relative']) }}>
    {{-- Original Content --}}
    <div @class([
        'transition-all duration-200',
        'pointer-events-none select-none' => $locked && $overlayStyle !== 'badge-only',
        'opacity-60' => $locked && $overlayStyle === 'dim',
    ])>
        {{ $slot }}
    </div>

    @if($locked)
        {{-- Overlay (for blur/dim styles) --}}
        @if($showOverlay && $overlayStyle !== 'badge-only')
            <div class="absolute inset-0 {{ $overlayClasses }} rounded-lg flex items-center justify-center z-10">
                <div class="text-center p-4 max-w-xs">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-purple-100 dark:bg-purple-900/50 mb-3">
                        <flux:icon name="lock-closed" class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                    </div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-1">{{ $title }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $finalDescription }}</p>
                    @if($requiredTier)
                        <flux:badge color="purple" size="sm">
                            {{ ucfirst($requiredTier) }} Plan
                        </flux:badge>
                    @endif
                </div>
            </div>
        @endif

        {{-- Badge Only (no overlay, just a badge indicator) --}}
        @if($overlayStyle === 'badge-only' || $badgePosition !== 'inline')
            <div class="{{ $badgePositionClasses }} z-20">
                <flux:badge color="purple" size="sm" class="shadow-sm">
                    <flux:icon name="lock-closed" variant="micro" class="mr-1" />
                    {{ ucfirst($requiredTier ?? 'Upgrade') }}
                </flux:badge>
            </div>
        @endif
    @endif
</div>

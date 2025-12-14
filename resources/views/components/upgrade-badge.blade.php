@props([
    'tier' => null,
    'size' => 'sm', // 'sm', 'xs'
    'showIcon' => true,
    'variant' => 'default', // 'default', 'subtle', 'outline'
])

@php
    $tierLabel = $tier ? ucfirst($tier) : 'Upgrade';

    $variantClasses = match($variant) {
        'subtle' => 'bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
        'outline' => 'bg-transparent border border-purple-300 text-purple-600 dark:border-purple-600 dark:text-purple-400',
        default => '', // Uses flux:badge default
    };

    $sizeClasses = match($size) {
        'xs' => 'text-xs px-1.5 py-0.5',
        default => '',
    };
@endphp

@if($variant === 'default')
    <flux:badge color="purple" size="{{ $size }}" {{ $attributes }}>
        @if($showIcon)
            <flux:icon name="lock-closed" variant="micro" class="mr-1" />
        @endif
        {{ $tierLabel }}
    </flux:badge>
@else
    <span {{ $attributes->merge(['class' => "inline-flex items-center rounded-md font-medium {$variantClasses} {$sizeClasses}"]) }}>
        @if($showIcon)
            <flux:icon name="lock-closed" variant="micro" class="mr-1" />
        @endif
        {{ $tierLabel }}
    </span>
@endif

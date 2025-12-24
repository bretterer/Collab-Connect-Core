@php
    $info = $this->limitInfo;
    $isLow = !$info['is_unlimited'] && $info['remaining'] <= 1;
    $isOut = !$info['is_unlimited'] && $info['remaining'] <= 0;
    $isSlotBased = $info['is_slot_based'] ?? false;

    $bgClass = match($variant) {
        'warning' => 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800',
        'info' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800',
        default => $isOut
            ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'
            : ($isLow
                ? 'bg-amber-50 dark:bg-amber-900/20 border-amber-200 dark:border-amber-800'
                : 'bg-zinc-50 dark:bg-zinc-800/50 border-zinc-200 dark:border-zinc-700'),
    };

    $iconClass = match(true) {
        $isOut => 'text-red-600 dark:text-red-400',
        $isLow => 'text-amber-600 dark:text-amber-400',
        default => 'text-blue-600 dark:text-blue-400',
    };
@endphp

<div class="rounded-lg border p-4 {{ $bgClass }}">
    <div class="flex items-start gap-3">
        {{-- Icon --}}
        <div class="flex-shrink-0 mt-0.5">
            @if($isOut)
                <svg class="w-5 h-5 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            @elseif($isLow)
                <svg class="w-5 h-5 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @else
                <svg class="w-5 h-5 {{ $iconClass }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            @endif
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            {{-- Header with remaining count --}}
            <div class="flex items-center justify-between gap-2 mb-1">
                <span class="font-medium text-zinc-900 dark:text-white text-sm">
                    @if($info['is_unlimited'])
                        Unlimited {{ Str::plural($creditName) }}
                    @elseif($isOut)
                        No {{ Str::plural($creditName) }} {{ $isSlotBased ? 'available' : 'remaining' }}
                    @else
                        {{ $info['remaining'] }} {{ Str::plural($creditName, $info['remaining']) }} {{ $isSlotBased ? 'available' : 'remaining' }}
                    @endif
                </span>
                @if(!$info['is_unlimited'] && $info['limit'] > 0)
                    <span class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ $info['remaining'] }}/{{ $info['limit'] }} {{ $isSlotBased ? 'on your plan' : 'this billing cycle' }}
                    </span>
                @endif
            </div>

            {{-- Description text --}}
            <div class="text-sm text-zinc-600 dark:text-zinc-400 space-y-1">
                @if($info['is_unlimited'])
                    <p>Your plan includes unlimited {{ Str::plural($creditName) }}.</p>
                @elseif($isOut)
                    <p class="text-red-600 dark:text-red-400">
                        You have no {{ Str::plural($creditName) }} {{ $isSlotBased ? 'available' : 'remaining' }}.
                        @if(!$isSlotBased && $info['formatted_reset_date'])
                            Your {{ Str::plural($creditName) }} will reset on <strong>{{ $info['formatted_reset_date'] }}</strong>.
                        @elseif($isSlotBased)
                            Remove an existing member to free up a slot, or upgrade your plan.
                        @endif
                    </p>
                @else
                    @if($isSlotBased)
                        <p>
                            {{ $actionText }} will use <strong>1 {{ $creditName }}</strong>.
                            Slots are freed when members are removed.
                        </p>
                    @else
                        <p>
                            {{ $actionText }} will use <strong>1 {{ $creditName }}</strong>.
                            @if($isScheduled)
                                The credit will be used on the scheduled publish date.
                            @endif
                            Once used, this credit cannot be recovered.
                        </p>

                        @if($isScheduled && $info['remaining'] <= 1)
                            <p class="text-amber-600 dark:text-amber-400 font-medium">
                                <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                If no {{ Str::plural($creditName) }} are available on the scheduled date, the campaign will not publish automatically.
                            </p>
                        @endif

                        @if($info['formatted_reset_date'])
                            <p>
                                Your {{ Str::plural($creditName) }} reset on <strong>{{ $info['formatted_reset_date'] }}</strong>.
                            </p>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

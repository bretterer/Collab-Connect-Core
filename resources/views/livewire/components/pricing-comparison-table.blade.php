@props([
    'categories' => [],
    'prices' => [],
    'highlightedPriceId' => null,
    'currentPriceId' => null,
    'isSubscribed' => false,
    'showActions' => true,
])

@php
    // Group prices by product for display
    $priceCollection = collect($prices)->sortBy('unit_amount');

    // Helper function to format feature value
    $formatValue = function($value, $type) {
        if ($value === null || $value === '') {
            return ['display' => '—', 'class' => 'text-zinc-400'];
        }

        if ($type === 'boolean') {
            if ($value === true || $value === '1' || $value === 1) {
                return ['display' => '✓', 'class' => 'text-green-600 dark:text-green-400 font-bold'];
            }
            return ['display' => '—', 'class' => 'text-zinc-400'];
        }

        if ($type === 'number') {
            $numValue = (int) $value;
            if ($numValue === -1) {
                return ['display' => 'Unlimited', 'class' => 'text-zinc-900 dark:text-white font-medium'];
            }
            return ['display' => number_format($numValue), 'class' => 'text-zinc-900 dark:text-white font-medium'];
        }

        // Text type
        return ['display' => $value, 'class' => 'text-zinc-900 dark:text-white'];
    };

    // Get feature value for a price
    $getFeatureValue = function($price, $featureKey) {
        $metadata = $price->metadata ?? [];
        $featureValues = [];

        if (isset($metadata['feature_values'])) {
            $featureValues = is_string($metadata['feature_values'])
                ? json_decode($metadata['feature_values'], true)
                : $metadata['feature_values'];
            $featureValues = $featureValues ?? [];
        }

        return $featureValues[$featureKey] ?? null;
    };
@endphp

@if($priceCollection->count() > 0 && count($categories) > 0)
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <!-- Header with plan names and prices -->
            <thead>
                <tr class="border-b border-zinc-200 dark:border-zinc-700">
                    <th class="py-4 px-4 text-left min-w-[200px]">
                        <span class="sr-only">Feature</span>
                    </th>
                    @foreach($priceCollection as $price)
                        @php
                            $isHighlighted = $highlightedPriceId === $price->stripe_id;
                            $isCurrent = $currentPriceId === $price->stripe_id;
                            $recurring = $price->recurring ?? [];
                            $interval = $recurring['interval'] ?? 'month';
                        @endphp
                        <th class="py-4 px-4 text-center min-w-[160px] {{ $isHighlighted ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                            <div class="space-y-2">
                                @if($isHighlighted)
                                    <flux:badge color="yellow" size="sm" class="mb-2">Most Popular</flux:badge>
                                @endif
                                @if($isCurrent)
                                    <flux:badge color="blue" size="sm" class="mb-2">Current Plan</flux:badge>
                                @endif
                                <div class="text-lg font-semibold text-zinc-900 dark:text-white">
                                    {{ $price->product_name ?? $price->product?->name ?? 'Plan' }}
                                </div>
                                <div class="text-2xl font-bold text-zinc-900 dark:text-white">
                                    ${{ number_format($price->unit_amount / 100, 0) }}
                                    <span class="text-sm font-normal text-zinc-500">/{{ $interval }}</span>
                                </div>
                                @if($showActions)
                                    <div class="pt-2">
                                        @if($isCurrent)
                                            <div class="py-2 text-sm font-medium text-blue-600 dark:text-blue-400">
                                                Current plan
                                            </div>
                                        @elseif($isSubscribed)
                                            <flux:button
                                                wire:click="changePlan('{{ $price->stripe_id }}')"
                                                variant="{{ $isHighlighted ? 'primary' : 'filled' }}"
                                                size="sm"
                                                class="w-full">
                                                Switch plan
                                            </flux:button>
                                        @else
                                            <flux:button
                                                wire:click="subscribe('{{ $price->stripe_id }}')"
                                                variant="{{ $isHighlighted ? 'primary' : 'filled' }}"
                                                size="sm"
                                                class="w-full">
                                                Get started
                                            </flux:button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>

            <!-- Feature rows grouped by category -->
            <tbody>
                @foreach($categories as $category)
                    <!-- Category header row -->
                    <tr class="bg-zinc-50 dark:bg-zinc-800/50">
                        <td colspan="{{ $priceCollection->count() + 1 }}" class="py-3 px-4">
                            <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 uppercase tracking-wide">
                                {{ $category['label'] }}
                            </span>
                        </td>
                    </tr>

                    <!-- Feature rows -->
                    @foreach($category['features'] ?? [] as $feature)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <td class="py-3 px-4 text-sm text-zinc-700 dark:text-zinc-300">
                                <span class="inline-flex items-center gap-1">
                                    {{ $feature['label'] }}
                                    @if(!empty($feature['description']))
                                        <flux:tooltip toggleable>
                                            <flux:button icon="information-circle" size="sm" variant="ghost" class="!p-0 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300" />
                                            <flux:tooltip.content class="max-w-[16rem]">
                                                <p>{{ $feature['description'] }}</p>
                                            </flux:tooltip.content>
                                        </flux:tooltip>
                                    @endif
                                </span>
                            </td>
                            @foreach($priceCollection as $price)
                                @php
                                    $isHighlighted = $highlightedPriceId === $price->stripe_id;
                                    $value = $getFeatureValue($price, $feature['key']);
                                    $formatted = $formatValue($value, $feature['type']);
                                @endphp
                                <td class="py-3 px-4 text-center {{ $isHighlighted ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                                    <span class="{{ $formatted['class'] }}">
                                        {{ $formatted['display'] }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-8 text-zinc-500">
        @if(count($categories) === 0)
            <p>No feature categories defined yet.</p>
        @else
            <p>No plans available to compare.</p>
        @endif
    </div>
@endif

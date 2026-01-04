@props([
    'categories' => [],
    'prices' => collect(),
    'highlightedPriceId' => null,
])

@php
    $priceCollection = $prices instanceof \Illuminate\Support\Collection ? $prices : collect($prices);
    $priceCollection = $priceCollection->sortBy('unit_amount');

    // Helper function to format feature value
    $formatValue = function($value, $type) {
        if ($value === null || $value === '') {
            return ['display' => '—', 'class' => 'text-gray-400 dark:text-gray-500'];
        }

        if ($type === 'boolean') {
            if ($value === true || $value === '1' || $value === 1) {
                return ['display' => '✓', 'class' => 'text-green-600 dark:text-green-400 font-bold text-lg'];
            }
            return ['display' => '—', 'class' => 'text-gray-400 dark:text-gray-500'];
        }

        if ($type === 'number') {
            $numValue = (int) $value;
            if ($numValue === -1) {
                return ['display' => 'Unlimited', 'class' => 'text-gray-900 dark:text-white font-semibold'];
            }
            return ['display' => number_format($numValue), 'class' => 'text-gray-900 dark:text-white font-semibold'];
        }

        // Text type
        return ['display' => $value, 'class' => 'text-gray-900 dark:text-white'];
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

<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-xl">
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <!-- Header with plan names and prices -->
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="py-8 px-6 text-left bg-gray-50 dark:bg-gray-900/50 min-w-[220px]">
                        <span class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Compare Plans</span>
                    </th>
                    @foreach($priceCollection as $price)
                        @php
                            $isHighlighted = $highlightedPriceId === $price->stripe_id;
                            $recurring = $price->recurring ?? [];
                            $interval = $recurring['interval'] ?? 'month';
                        @endphp
                        <th class="py-8 px-6 text-center min-w-[180px] {{ $isHighlighted ? 'bg-blue-50 dark:bg-blue-900/20 relative' : 'bg-gray-50 dark:bg-gray-900/50' }}">
                            @if($isHighlighted)
                                <div class="absolute -top-0 left-1/2 -translate-x-1/2 bg-gradient-to-r from-yellow-400 to-orange-400 text-white text-xs font-bold px-4 py-1 rounded-b-lg shadow-lg">
                                    MOST POPULAR
                                </div>
                            @endif
                            <div class="space-y-3 {{ $isHighlighted ? 'pt-4' : '' }}">
                                <div class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $price->product_name ?? $price->product?->name ?? 'Plan' }}
                                </div>
                                <div class="flex items-baseline justify-center gap-1">
                                    <span class="text-4xl font-bold text-gray-900 dark:text-white">${{ number_format($price->unit_amount / 100, 0) }}</span>
                                    <span class="text-gray-500 dark:text-gray-400">/{{ $interval }}</span>
                                </div>
                                <div class="pt-2">
                                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center w-full px-6 py-3 {{ $isHighlighted ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900' }} font-semibold rounded-lg transition-colors">
                                        Get Started
                                    </a>
                                </div>
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>

            <!-- Feature rows grouped by category -->
            <tbody>
                @foreach($categories as $category)
                    @if(!empty($category['features']))
                        <!-- Category header row -->
                        <tr class="bg-gray-100 dark:bg-gray-900">
                            <td colspan="{{ $priceCollection->count() + 1 }}" class="py-4 px-6">
                                <span class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    {{ $category['label'] }}
                                </span>
                            </td>
                        </tr>

                        <!-- Feature rows -->
                        @foreach($category['features'] as $feature)
                            <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="py-4 px-6 text-gray-700 dark:text-gray-300">
                                    <span class="inline-flex items-center gap-1">
                                        {{ $feature['label'] }}
                                        @if(!empty($feature['description']))
                                            <flux:tooltip toggleable>
                                                <flux:button icon="information-circle" size="sm" variant="ghost" class="!p-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" />
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
                                    <td class="py-4 px-6 text-center {{ $isHighlighted ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                                        <span class="{{ $formatted['class'] }}">
                                            {{ $formatted['display'] }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    @endif
                @endforeach

                <!-- Final CTA row -->
                <tr class="bg-gray-50 dark:bg-gray-900/50">
                    <td class="py-6 px-6"></td>
                    @foreach($priceCollection as $price)
                        @php
                            $isHighlighted = $highlightedPriceId === $price->stripe_id;
                        @endphp
                        <td class="py-6 px-6 text-center {{ $isHighlighted ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-6 py-3 {{ $isHighlighted ? 'bg-blue-600 hover:bg-blue-700 text-white' : 'border-2 border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 text-gray-700 dark:text-gray-300' }} font-semibold rounded-lg transition-colors">
                                Choose Plan
                            </a>
                        </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>

@props([
    'prices' => collect(),
    'highlightedPriceId' => null,
])

@php
    $priceCollection = $prices instanceof \Illuminate\Support\Collection ? $prices : collect($prices);
    $priceCollection = $priceCollection->sortBy('unit_amount');
@endphp

<div class="grid gap-8 lg:grid-cols-{{ min($priceCollection->count(), 3) }} max-w-5xl mx-auto">
    @foreach($priceCollection as $price)
        @php
            $isHighlighted = $highlightedPriceId === $price->stripe_id;
            $recurring = $price->recurring ?? [];
            $interval = $recurring['interval'] ?? 'month';
            $metadata = $price->metadata ?? [];
            $features = [];

            if (isset($metadata['features'])) {
                $features = is_string($metadata['features'])
                    ? json_decode($metadata['features'], true)
                    : $metadata['features'];
                $features = $features ?? [];
            }
        @endphp

        <div class="relative bg-white dark:bg-gray-800 rounded-2xl border-2 {{ $isHighlighted ? 'border-blue-500 shadow-xl shadow-blue-500/10' : 'border-gray-200 dark:border-gray-700' }} overflow-hidden">
            @if($isHighlighted)
                <div class="absolute top-0 left-0 right-0 bg-gradient-to-r from-blue-500 to-blue-600 text-white text-center text-sm font-semibold py-2">
                    MOST POPULAR
                </div>
            @endif

            <div class="p-8 {{ $isHighlighted ? 'pt-14' : '' }}">
                <!-- Plan Name -->
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                    {{ $price->product_name ?? $price->product?->name ?? 'Plan' }}
                </h3>

                <!-- Price -->
                <div class="flex items-baseline gap-1 mb-6">
                    <span class="text-5xl font-bold text-gray-900 dark:text-white">
                        ${{ number_format($price->unit_amount / 100, 0) }}
                    </span>
                    <span class="text-gray-500 dark:text-gray-400 text-lg">
                        /{{ $interval }}
                    </span>
                </div>

                <!-- CTA Button -->
                <a href="{{ route('register') }}"
                   class="block w-full text-center py-3 px-6 rounded-lg font-semibold transition-colors mb-8
                          {{ $isHighlighted
                              ? 'bg-blue-600 hover:bg-blue-700 text-white'
                              : 'bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white' }}">
                    Get Started
                </a>

                <!-- Features List -->
                @if(!empty($features))
                    <ul class="space-y-4">
                        @foreach($features as $feature)
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-gray-600 dark:text-gray-300">{{ $feature }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-500 dark:text-gray-400 text-sm text-center">
                        Features coming soon
                    </p>
                @endif
            </div>
        </div>
    @endforeach
</div>

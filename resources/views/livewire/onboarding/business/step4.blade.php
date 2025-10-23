<!-- Step 4: Subscription Plan -->
<div class="space-y-8">
    <!-- Success Header -->
    <div class="text-center space-y-4">
        <div class="flex justify-center">
            <div class="w-20 h-20 bg-gradient-to-r from-green-400 to-blue-500 rounded-full flex items-center justify-center">
                <flux:icon name="credit-card" class="w-10 h-10 text-white" />
            </div>
        </div>
        <flux:heading size="2xl" class="text-gray-800 dark:text-gray-200">
            Subscription Plans
        </flux:heading>
        <flux:subheading class="text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
            Choose a subscription plan that fits your business needs.
        </flux:subheading>
    </div>

    <!-- Pricing Cards -->
    <div class="max-w-5xl mx-auto">
        @if($subscriptionProducts->isEmpty())
            <div class="text-center py-12">
                <flux:text class="text-gray-500 dark:text-gray-400">
                    No subscription plans are currently available. Please contact support.
                </flux:text>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($subscriptionProducts as $product)
                    @foreach($product->prices as $price)
                        <flux:card class="relative flex flex-col {{ $selectedPriceId === $price->id ? 'ring-2 ring-blue-500 dark:ring-blue-400' : '' }}">
                            <!-- Selected Badge -->
                            @if($selectedPriceId === $price->id)
                                <div class="absolute top-4 right-4">
                                    <flux:badge color="blue" size="sm">Selected</flux:badge>
                                </div>
                            @endif

                            <!-- Product Name -->
                            <div class="mb-4">
                                <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">
                                    {{ $product->name }}
                                </flux:heading>
                                @if($product->description)
                                    <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        {{ $product->description }}
                                    </flux:text>
                                @endif
                            </div>

                            <!-- Price -->
                            <div class="mb-6">
                                <div class="flex items-baseline">
                                    <flux:heading size="2xl" class="text-gray-900 dark:text-gray-100">
                                        ${{ number_format($price->unit_amount / 100, 2) }}
                                    </flux:heading>
                                    <flux:text class="ml-2 text-gray-600 dark:text-gray-400">
                                        / {{ $price->recurring['interval'] ?? 'month' }}
                                    </flux:text>
                                </div>
                            </div>

                            <!-- Billing Details -->
                            <div class="mb-6 flex-grow">
                                <flux:text class="text-xs text-gray-500 dark:text-gray-400">
                                    Billed {{ $price->recurring['interval'] ?? 'monthly' }}
                                </flux:text>
                            </div>

                            <!-- Select Button -->
                            <flux:button
                                wire:click="selectPrice({{ $price->id }})"
                                variant="{{ $selectedPriceId === $price->id ? 'primary' : 'filled' }}"
                                class="w-full"
                            >
                                {{ $selectedPriceId === $price->id ? 'Selected' : 'Select Plan' }}
                            </flux:button>
                        </flux:card>
                    @endforeach
                @endforeach
            </div>

            <div class="mt-8">
                @livewire('components.stripe-payment-form')
            </div>

        @endif
    </div>
</div>
<div>
    <flux:modal wire:model="show" class="max-w-3xl" :closable="!$isProcessing">
        <!-- Processing Overlay - Shows immediately on button click and while processing -->
        <div
            wire:loading.flex
            wire:target="processCheckout"
            class="absolute inset-0 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-sm rounded-xl items-center justify-center z-50"
        >
            <div class="text-center space-y-3">
                <div class="inline-flex items-center justify-center">
                    <svg class="animate-spin h-10 w-10 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <flux:heading>Processing</flux:heading>
                <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Please wait while we process your payment...</flux:text>
            </div>
        </div>

        <!-- Also show when isProcessing is true (for async operations like Stripe callbacks) -->
        @if($isProcessing)
            <div class="absolute inset-0 bg-white/95 dark:bg-zinc-900/95 backdrop-blur-sm rounded-xl flex items-center justify-center z-50">
                <div class="text-center space-y-3">
                    <div class="inline-flex items-center justify-center">
                        <svg class="animate-spin h-10 w-10 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <flux:heading>Processing</flux:heading>
                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Please wait while we process your payment...</flux:text>
                </div>
            </div>
        @endif

        <div class="flex flex-col md:flex-row">
            <!-- Left Column - Order Summary -->
            <div class="md:w-2/5 p-6 bg-zinc-50 dark:bg-zinc-800/50 md:rounded-l-xl border-b md:border-b-0 md:border-r border-zinc-200 dark:border-zinc-700">
                <flux:heading class="text-sm font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-4">
                    {{ $this->summaryTitle }}
                </flux:heading>

                @if($this->price)
                    <div class="mb-6">
                        <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-4">
                            {{ $this->formattedPrice }}
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-start gap-3">
                                <div class="flex-1">
                                    <flux:text class="font-medium text-zinc-900 dark:text-white">
                                        {{ $this->price->product_name ?? $this->price->product?->name ?? 'Subscription' }}
                                    </flux:text>
                                    @if($this->price->product?->name && $this->price->product_name !== $this->price->product->name)
                                        <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">
                                            {{ $this->price->product->name }}
                                        </flux:text>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($this->hasTax)
                        <!-- Show breakdown only when there's tax -->
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-4 space-y-2">
                            <div class="flex justify-between">
                                <flux:text class="text-zinc-500">Subtotal</flux:text>
                                <flux:text class="font-medium">{{ $this->formattedPrice }}</flux:text>
                            </div>
                            <div class="flex justify-between">
                                <flux:text class="text-zinc-500">Tax</flux:text>
                                <flux:text class="font-medium">{{ $this->formattedTax }}</flux:text>
                            </div>
                        </div>
                    @endif

                    <div class="border-t border-zinc-200 dark:border-zinc-700 mt-4 pt-4">
                        <div class="flex justify-between">
                            <flux:text class="font-medium">Due today</flux:text>
                            <flux:text class="font-bold text-lg">{{ $this->formattedTotal }}</flux:text>
                        </div>
                        <flux:text class="text-xs text-zinc-500 mt-1">
                            Then {{ $this->formattedTotal }}{{ $this->intervalLabel }}
                        </flux:text>
                    </div>
                @endif
            </div>

            <!-- Right Column - Payment Details -->
            <div class="md:w-3/5 p-6">
                <flux:heading class="text-sm font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-6">
                    Payment
                </flux:heading>

                <!-- Error Display -->
                @if($errorMessage)
                    <div class="mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <div class="flex items-start gap-2">
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <flux:text class="text-sm font-medium text-red-600 dark:text-red-400">{{ $errorMessage }}</flux:text>
                        </div>
                    </div>
                @endif

                <!-- Existing Payment Methods -->
                @if(count($this->paymentMethods) > 0)
                    <div class="space-y-3 mb-4">
                        <flux:text class="text-sm text-zinc-600 dark:text-zinc-400">Select a payment method</flux:text>

                        @foreach($this->paymentMethods as $method)
                            <button
                                type="button"
                                wire:click="selectPaymentMethod('{{ $method['id'] }}')"
                                class="w-full p-3 rounded-lg border-2 transition-all text-left flex items-center gap-3 {{ $selectedPaymentMethodId === $method['id'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600' }}"
                            >
                                <div class="w-10 h-7 bg-zinc-100 dark:bg-zinc-700 rounded flex items-center justify-center shrink-0">
                                    @if(strtolower($method['brand']) === 'visa')
                                        <span class="text-blue-600 font-bold text-xs">VISA</span>
                                    @elseif(strtolower($method['brand']) === 'mastercard')
                                        <span class="text-red-600 font-bold text-xs">MC</span>
                                    @elseif(strtolower($method['brand']) === 'amex')
                                        <span class="text-blue-500 font-bold text-xs">AMEX</span>
                                    @else
                                        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <flux:text class="font-medium text-sm">{{ $method['brand'] }} ending in {{ $method['last4'] }}</flux:text>
                                    <flux:text class="text-xs text-zinc-500">Expires {{ $method['exp_month'] }}/{{ $method['exp_year'] }}</flux:text>
                                </div>
                                @if($selectedPaymentMethodId === $method['id'])
                                    <svg class="w-5 h-5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                @endif
                            </button>
                        @endforeach

                        <!-- Add New Card Option -->
                        <button
                            type="button"
                            wire:click="showAddNewCard"
                            class="w-full p-3 rounded-lg border-2 transition-all text-left flex items-center gap-3 {{ $showNewPaymentForm ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600' }}"
                        >
                            <div class="w-10 h-7 bg-zinc-100 dark:bg-zinc-700 rounded flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <flux:text class="font-medium text-sm">Add a new card</flux:text>
                            @if($showNewPaymentForm)
                                <svg class="w-5 h-5 text-blue-600 shrink-0 ml-auto" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </button>
                    </div>
                @endif

                <!-- New Payment Form -->
                @if($showNewPaymentForm)
                    <div class="mb-6">
                        @if(count($this->paymentMethods) === 0)
                            <flux:text class="text-sm text-zinc-600 dark:text-zinc-400 mb-4">
                                Enter your card details to complete your {{ $checkoutType === 'subscription' ? 'subscription' : 'payment' }}
                            </flux:text>
                        @endif

                        <!-- Inline Stripe Card Fields -->
                        <div class="space-y-4" wire:ignore>
                            <!-- Card Number Field -->
                            <flux:field>
                                <flux:label for="checkout-cardNumber">Card Number</flux:label>
                                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 transition-colors focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20"
                                    id="checkout-cardNumber">
                                </div>
                            </flux:field>

                            <!-- Expiry and CVC Fields -->
                            <div class="grid grid-cols-2 gap-4">
                                <flux:field>
                                    <flux:label for="checkout-cardExpiry">Expiration</flux:label>
                                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 transition-colors focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20"
                                        id="checkout-cardExpiry">
                                    </div>
                                </flux:field>

                                <flux:field>
                                    <flux:label for="checkout-cardCvc">CVC</flux:label>
                                    <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 transition-colors focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20"
                                        id="checkout-cardCvc">
                                    </div>
                                </flux:field>
                            </div>

                            <!-- Stripe Error Display -->
                            <div id="checkout-stripe-error-container" class="hidden p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                <div class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <flux:text class="text-sm font-medium text-red-600 dark:text-red-400" id="checkout-stripe-errors"></flux:text>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Continue Button -->
                <flux:button
                    type="button"
                    wire:click="processCheckout"
                    variant="primary"
                    class="w-full"
                    :disabled="$isProcessing"
                >
                    @if($isProcessing)
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    @else
                        {{ $this->checkoutTypeLabel }}
                    @endif
                </flux:button>

                <!-- Secure Payment Notice -->
                <div class="flex items-center justify-center gap-2 mt-4">
                    <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <flux:text class="text-xs text-zinc-400">
                        Payments are secure and encrypted
                    </flux:text>
                </div>
            </div>
        </div>
    </flux:modal>

    @push('scripts')
    <script>
        document.addEventListener('livewire:init', function() {
            (function() {
                let stripeElementsInstance = null;
                let isInitialized = false;

                const initializeStripeElements = () => {
                    if (isInitialized) return;

                    const cardNumberEl = document.getElementById('checkout-cardNumber');
                    if (!cardNumberEl || !window.Stripe) return;

                    isInitialized = true;
                    console.log('Initializing Stripe Elements in CheckoutModal');

                    const stripe = window.Stripe;
                    const elements = stripe.elements();

                    const isDarkMode = document.documentElement.classList.contains('dark');

                    const style = {
                        base: {
                            color: isDarkMode ? '#f4f4f5' : '#18181b',
                            fontFamily: 'system-ui, -apple-system, sans-serif',
                            fontSmoothing: 'antialiased',
                            fontSize: '16px',
                            '::placeholder': {
                                color: isDarkMode ? '#71717a' : '#a1a1aa'
                            }
                        },
                        invalid: {
                            color: '#ef4444',
                            iconColor: '#ef4444'
                        }
                    };

                    const cardNumber = elements.create('cardNumber', { style });
                    cardNumber.mount('#checkout-cardNumber');

                    const cardExpiry = elements.create('cardExpiry', { style });
                    cardExpiry.mount('#checkout-cardExpiry');

                    const cardCvc = elements.create('cardCvc', { style });
                    cardCvc.mount('#checkout-cardCvc');

                    function displayError(event) {
                        const displayError = document.getElementById('checkout-stripe-errors');
                        const errorContainer = document.getElementById('checkout-stripe-error-container');
                        if (event.error) {
                            displayError.textContent = event.error.message;
                            errorContainer.classList.remove('hidden');
                        } else {
                            displayError.textContent = '';
                            errorContainer.classList.add('hidden');
                        }
                    }

                    cardNumber.on('change', displayError);
                    cardExpiry.on('change', displayError);
                    cardCvc.on('change', displayError);

                    window.checkoutStripeElements = {
                        stripe: stripe,
                        cardNumber: cardNumber,
                        cardExpiry: cardExpiry,
                        cardCvc: cardCvc
                    };

                    console.log('Checkout Stripe Elements initialized');
                };

                const cleanupStripeElements = () => {
                    if (window.checkoutStripeElements) {
                        try {
                            window.checkoutStripeElements.cardNumber?.unmount();
                            window.checkoutStripeElements.cardExpiry?.unmount();
                            window.checkoutStripeElements.cardCvc?.unmount();
                        } catch (e) {
                            console.log('Stripe elements already unmounted');
                        }
                        window.checkoutStripeElements = null;
                    }
                    isInitialized = false;
                };

                Livewire.on('reloadStripeFromLivewire', () => {
                    console.log('Reloading Stripe Elements from Livewire');
                    cleanupStripeElements();
                    setTimeout(() => initializeStripeElements(), 150);
                });

                Livewire.on('createCheckoutPaymentMethod', async () => {
                    const currentStripe = window.checkoutStripeElements?.stripe;
                    const currentCardNumber = window.checkoutStripeElements?.cardNumber;

                    if (!currentStripe || !currentCardNumber) {
                        console.error('Stripe elements not available');
                        Livewire.dispatch('stripePaymentMethodError', { message: 'Payment form is not ready. Please try again.' });
                        return;
                    }

                    try {
                        const { paymentMethod, error } = await currentStripe.createPaymentMethod({
                            type: 'card',
                            card: currentCardNumber,
                        });

                        if (error) {
                            Livewire.dispatch('stripePaymentMethodError', { message: error.message });
                        } else {
                            Livewire.dispatch('stripePaymentMethodCreated', { paymentMethodId: paymentMethod.id });
                        }
                    } catch (e) {
                        console.error('Error creating payment method:', e);
                        Livewire.dispatch('stripePaymentMethodError', { message: 'An error occurred processing your payment.' });
                    }
                });

                // Initialize when Stripe is ready
                if (window.Stripe) {
                    setTimeout(() => initializeStripeElements(), 100);
                } else {
                    document.addEventListener('stripe:loaded', () => {
                        setTimeout(() => initializeStripeElements(), 100);
                    }, { once: true });
                }
            })();
        });
    </script>
    @endpush
</div>

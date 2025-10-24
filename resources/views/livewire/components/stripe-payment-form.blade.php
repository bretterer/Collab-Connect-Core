<div>
    <flux:card class="relative">
    <!-- Loading Overlay -->
    <div id="stripe-loading-overlay" class="absolute inset-0 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm rounded-lg flex items-center justify-center z-10 transition-opacity duration-300">
        <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center">
                <svg class="animate-spin h-8 w-8 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <flux:text class="text-sm text-gray-600 dark:text-gray-400">Loading payment form...</flux:text>
        </div>
    </div>

    <!-- Payment Processing Overlay -->
    <div id="stripe-processing-overlay" class="hidden absolute inset-0 bg-white/90 dark:bg-zinc-900/90 backdrop-blur-sm rounded-lg flex items-center justify-center z-10 transition-opacity duration-300">
        <div class="text-center space-y-3">
            <div class="inline-flex items-center justify-center">
                <svg class="animate-spin h-8 w-8 text-blue-600 dark:text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <flux:text class="text-sm text-gray-600 dark:text-gray-400">Processing...</flux:text>
        </div>
    </div>

    <!-- Timeout Error (hidden by default) -->
    <div id="stripe-timeout-error" class="hidden absolute inset-0 bg-white dark:bg-zinc-900 rounded-lg flex items-center justify-center z-10">
        <div class="text-center space-y-4 p-6">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30">
                <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="space-y-2">
                <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">Payment Form Failed to Load</flux:heading>
                <flux:text class="text-sm text-gray-600 dark:text-gray-400">
                    The payment system could not be initialized. Please refresh the page and try again.
                </flux:text>
            </div>
            <flux:button onclick="window.location.reload()" variant="primary">
                Refresh Page
            </flux:button>
        </div>
    </div>

    <!-- Card Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-3 mb-2">
            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
            </div>
            <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">
                Payment Information
            </flux:heading>
        </div>
        <flux:text class="text-sm text-gray-600 dark:text-gray-400">
            Enter your card details to complete your subscription
        </flux:text>
    </div>

    <!-- Error Display -->
    <div id="stripe-error-container" class="hidden mb-4 p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
        <div class="flex items-start space-x-2">
            <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <flux:text class="text-sm font-medium text-red-600 dark:text-red-400" id="stripe-errors"></flux:text>
        </div>
    </div>

    <div class="space-y-5">
        <!-- Card Number Field -->
        <flux:field>
            <flux:label for="cardNumber">Card Number</flux:label>
            <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 transition-colors focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20"
                id="cardNumber"
                wire:ignore>
            </div>
        </flux:field>

        <!-- Expiry and CVC Fields -->
        <div class="grid grid-cols-2 gap-4">
            <flux:field>
                <flux:label for="cardExpiry">Expiration Date</flux:label>
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 transition-colors focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20"
                    id="cardExpiry"
                    wire:ignore>
                </div>
            </flux:field>

            <flux:field>
                <flux:label for="cardCvc">CVC</flux:label>
                <div class="rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2.5 transition-colors focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-500/20"
                    id="cardCvc"
                    wire:ignore>
                </div>
            </flux:field>
        </div>

        <!-- Secure Payment Notice -->
        <div class="flex items-center space-x-2 pt-2">
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
            </svg>
            <flux:text class="text-xs text-gray-500 dark:text-gray-400">
                Your payment information is secure and encrypted
            </flux:text>
        </div>
    </div>
</flux:card>

    @push('scripts')
    <script>
        let paymentformInitialized = false;

        (function() {
            let timeoutId;
            let stripeElementsInstance = null;
            let isInitialized = false;

            const hideLoadingOverlay = () => {
                const overlay = document.getElementById('stripe-loading-overlay');
                if (overlay) {
                    overlay.style.opacity = '0';
                    setTimeout(() => overlay.remove(), 300);
                }
            };

            const showTimeoutError = () => {
                const overlay = document.getElementById('stripe-loading-overlay');
                const timeoutError = document.getElementById('stripe-timeout-error');

                if (overlay) overlay.remove();
                if (timeoutError) timeoutError.classList.remove('hidden');
            };

            const paymentProcessing = () => {
                const overlay = document.getElementById('stripe-processing-overlay');
                if (overlay) overlay.classList.remove('hidden');
            };

            const cleanupStripeElements = () => {
                console.log('Cleaning up Stripe Elements');

                if (window.stripeElements) {
                    try {
                        window.stripeElements.cardNumber?.unmount();
                        window.stripeElements.cardExpiry?.unmount();
                        window.stripeElements.cardCvc?.unmount();
                    } catch (e) {
                        console.log('Stripe elements already unmounted');
                    }
                    window.stripeElements = null;
                }
                stripeElementsInstance = null;
                isInitialized = false;
            };

            const initializeStripeElements = () => {
                if (paymentformInitialized) {
                    hideLoadingOverlay();
                }
                if (isInitialized) return;
                isInitialized = true;

                // Clear the timeout since we successfully loaded
                if (timeoutId) {
                    clearTimeout(timeoutId);
                }

                console.log('Initializing Stripe Payment Form');

                const stripe = window.Stripe;
                const elements = stripe.elements();

                // Check if dark mode is enabled
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
                cardNumber.mount('#cardNumber');

                const cardExpiry = elements.create('cardExpiry', { style });
                cardExpiry.mount('#cardExpiry');

                const cardCvc = elements.create('cardCvc', { style });
                cardCvc.mount('#cardCvc');

                function displayError(event) {
                    const displayError = document.getElementById('stripe-errors');
                    const errorContainer = document.getElementById('stripe-error-container');
                    const processingOverlay = document.getElementById('stripe-processing-overlay');
                    if (event.error) {
                        displayError.textContent = event.error.message;
                        errorContainer.classList.remove('hidden');
                    } else {
                        displayError.textContent = '';
                        errorContainer.classList.add('hidden');
                        if (processingOverlay) {
                            processingOverlay.classList.add('hidden');
                        }
                    }
                }

                cardNumber.on('change', displayError);
                cardExpiry.on('change', displayError);
                cardCvc.on('change', displayError);

                // Store references globally
                window.stripeElements = {
                    stripe: stripe,
                    cardNumber: cardNumber,
                    cardExpiry: cardExpiry,
                    cardCvc: cardCvc
                };

                console.log('Stripe Elements initialized successfully');

                // Hide loading overlay
                hideLoadingOverlay();


                paymentformInitialized = true;
            };

            // Set timeout for 10 seconds
            timeoutId = setTimeout(() => {
                if (!isInitialized) {
                    console.error('Stripe failed to load within 10 seconds');
                    showTimeoutError();
                }
            }, 10000);

            // Initialize immediately if Stripe is already loaded, otherwise wait for event
            if (window.Stripe) {
                initializeStripeElements();
            } else {
                document.addEventListener('stripe:loaded', initializeStripeElements, { once: true });
            }

            Livewire.on('reloadStripeFromLivewire', async () => {
                cleanupStripeElements();
                console.log('Re-initializing Stripe Elements from Livewire event');
                setTimeout(() => initializeStripeElements(), 100);
            });

            // Register a single persistent listener that uses current elements
            Livewire.on('createStripePaymentMethod', async () => {
                paymentProcessing();

                // Use the current elements from the global reference
                const currentStripe = window.stripeElements?.stripe;
                const currentCardNumber = window.stripeElements?.cardNumber;

                if (!currentStripe || !currentCardNumber) {
                    console.error('Stripe elements not available');
                    Livewire.dispatch('stripePaymentMethodError', { message: 'Payment form is not ready. Please refresh the page.' });
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
                    Livewire.dispatch('stripePaymentMethodError', { message: 'An error occurred processing your payment. Please try again.' });
                }
            });

            // Cleanup when Livewire navigates away or component is removed
            document.addEventListener('livewire:navigate', cleanupStripeElements);

            Livewire.on('stripePaymentMethodError', ({ message }) => {
                const displayError = document.getElementById('stripe-errors');
                const errorContainer = document.getElementById('stripe-error-container');

                if (displayError) displayError.textContent = message;
                if (errorContainer) errorContainer.classList.remove('hidden');

                // Hide processing overlay
                const processingOverlay = document.getElementById('stripe-processing-overlay');
                if (processingOverlay) {
                    processingOverlay.style.opacity = '0';
                    setTimeout(() => processingOverlay.classList.add('hidden'), 300);
                }
            });
        })();
    </script>
    @endpush
</div>

@php
    $modalId = 'stripe-checkout-' . uniqid();
    $fields = is_string($data['fields'] ?? null) ? json_decode($data['fields'], true) : ($data['fields'] ?? []);
@endphp

<section class="py-16 bg-white" x-data="{ showModal: false, formData: {} }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <button
            @click="showModal = true"
            class="inline-block bg-blue-600 text-white px-12 py-6 rounded-lg text-2xl font-bold hover:bg-blue-700 transition-colors shadow-xl"
        >
            {{ $data['button_text'] ?? 'Buy Now' }}
        </button>
    </div>

    <!-- Modal -->
    <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="showModal = false"></div>

            <div class="relative bg-white rounded-lg max-w-md w-full p-8 shadow-2xl">
                <!-- Close button -->
                <button
                    @click="showModal = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <h3 class="text-2xl font-bold text-gray-900 mb-2">
                    {{ $data['modal_headline'] ?? 'Complete Your Purchase' }}
                </h3>

                @if(!empty($data['modal_description']))
                    <p class="text-gray-600 mb-6">
                        {{ $data['modal_description'] }}
                    </p>
                @endif

                <form
                    action="{{ route('landing.stripe-checkout') }}"
                    method="POST"
                    class="space-y-4"
                >
                    @csrf
                    <input type="hidden" name="price_id" value="{{ $data['stripe_price_id'] ?? '' }}">
                    <input type="hidden" name="success_url" value="{{ request()->url() }}">
                    <input type="hidden" name="cancel_url" value="{{ $data['cancel_url'] ?? '' }}">

                    @if(is_array($fields) && count($fields) > 0)
                        @foreach($fields as $field)
                            <div>
                                <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ $field['label'] }}
                                    @if($field['required'] ?? false)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                <input
                                    type="{{ $field['type'] ?? 'text' }}"
                                    id="{{ $field['name'] }}"
                                    name="{{ $field['name'] }}"
                                    @if($field['required'] ?? false) required @endif
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                >
                            </div>
                        @endforeach
                    @endif

                    <button
                        type="submit"
                        class="w-full bg-blue-600 text-white px-6 py-4 rounded-lg font-semibold hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        Continue to Checkout
                    </button>
                </form>

                <p class="text-xs text-gray-500 text-center mt-4">
                    Secure payment powered by Stripe
                </p>
            </div>
        </div>
    </div>
</section>

@if(request()->has('success') || request()->has('session_id'))
    <section class="min-h-screen flex items-center justify-center px-4 bg-gray-50">
        <div class="max-w-md w-full text-center">
            <div class="bg-white rounded-lg shadow-xl p-8">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <h1 class="text-3xl font-bold text-gray-900 mb-4">
                    {{ $data['headline'] ?? 'Thank You!' }}
                </h1>

                <p class="text-lg text-gray-600 mb-6">
                    {{ $data['message'] ?? 'Your payment was successful. You\'ll receive a confirmation email shortly.' }}
                </p>

                @if(($data['show_order_id'] ?? true) && request()->has('session_id'))
                    <p class="text-sm text-gray-500 mb-6">
                        Order ID: {{ substr(request('session_id'), -12) }}
                    </p>
                @endif

                @if(!empty($data['button_text']))
                    <a
                        href="{{ $data['button_url'] ?? '/' }}"
                        class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                    >
                        {{ $data['button_text'] ?? 'Return to Home' }}
                    </a>
                @endif
            </div>
        </div>
    </section>
@endif

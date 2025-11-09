<section class="py-16 bg-white" x-data="{ showModal: false }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <button
            @click="showModal = true"
            class="inline-block bg-blue-600 text-white px-12 py-6 rounded-lg text-2xl font-bold hover:bg-blue-700 transition-colors shadow-xl"
        >
            {{ $data['button_text'] ?? 'Yes! I Want This' }}
        </button>
    </div>

    <!-- Modal -->
    <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="showModal = false"></div>

            <div class="relative bg-white rounded-lg max-w-md w-full p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">
                    {{ $data['modal_headline'] ?? 'Enter your email to continue' }}
                </h3>

                <form action="{{ route('landing.signup') }}" method="POST">
                    @csrf
                    <input
                        type="email"
                        name="email"
                        placeholder="Enter your email"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg mb-4"
                        required
                    >
                    <button
                        type="submit"
                        class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors"
                    >
                        {{ $data['form_button_text'] ?? 'Get Instant Access' }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

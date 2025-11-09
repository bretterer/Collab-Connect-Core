<!-- Exit popup will be triggered via JavaScript -->
<div x-data="exitPopup()" x-cloak>
    <div x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" @click="show = false"></div>

            <div class="relative bg-white rounded-lg max-w-md w-full p-8 z-10"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95">
                <button @click="show = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <h3 class="text-2xl font-bold text-gray-900 mb-4">
                    {{ $data['headline'] ?? "Wait! Don't Leave Yet" }}
                </h3>
                <p class="text-gray-600 mb-6">
                    {{ $data['content'] ?? 'Get a special offer before you go' }}
                </p>
                <a
                    href="{{ $data['cta_url'] ?? '#' }}"
                    class="block w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center"
                >
                    {{ $data['cta_text'] ?? 'Claim Offer' }}
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function exitPopup() {
    return {
        show: false,
        shown: false,
        init() {
            // Detect when user's mouse leaves the viewport (exit intent)
            document.addEventListener('mouseleave', (e) => {
                if (e.clientY <= 0 && !this.shown) {
                    this.show = true;
                    this.shown = true; // Only show once per session
                }
            });
        }
    }
}
</script>

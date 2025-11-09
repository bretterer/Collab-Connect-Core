<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl sm:text-4xl font-bold text-center text-gray-900 mb-12">
            {{ $data['title'] ?? 'Features' }}
        </h2>
        @if(!empty($data['items']) && is_array($data['items']))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($data['items'] as $item)
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            {{ $item['title'] ?? '' }}
                        </h3>
                        <p class="text-gray-600">
                            {{ $item['description'] ?? '' }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

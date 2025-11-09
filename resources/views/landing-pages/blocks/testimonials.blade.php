<section class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl sm:text-4xl font-bold text-center text-gray-900 mb-12">
            {{ $data['title'] ?? 'What Our Customers Say' }}
        </h2>
        @if(!empty($data['items']) && is_array($data['items']))
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($data['items'] as $item)
                    <div class="bg-white p-6 rounded-lg shadow-lg">
                        <p class="text-gray-600 mb-4">{{ $item['content'] ?? '' }}</p>
                        <div class="flex items-center">
                            @if(!empty($item['image']))
                                <img src="{{ $item['image'] }}" alt="{{ $item['name'] ?? '' }}" class="w-12 h-12 rounded-full mr-4">
                            @endif
                            <div>
                                <p class="font-semibold text-gray-900">{{ $item['name'] ?? '' }}</p>
                                <p class="text-sm text-gray-500">{{ $item['role'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

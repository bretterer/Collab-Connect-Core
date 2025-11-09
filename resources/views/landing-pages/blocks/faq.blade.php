<section class="py-20 bg-gray-50">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl sm:text-4xl font-bold text-center text-gray-900 mb-12">
            {{ $data['title'] ?? 'Frequently Asked Questions' }}
        </h2>
        @if(!empty($data['items']) && is_array($data['items']))
            <div class="space-y-6">
                @foreach($data['items'] as $item)
                    <div class="bg-white p-6 rounded-lg shadow">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            {{ $item['question'] ?? '' }}
                        </h3>
                        <p class="text-gray-600">
                            {{ $item['answer'] ?? '' }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

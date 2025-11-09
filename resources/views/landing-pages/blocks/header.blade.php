<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <div class="flex justify-between items-center">
            @if(!empty($data['logo']))
                <img src="{{ $data['logo'] }}" alt="Logo" class="h-10">
            @else
                <div class="text-2xl font-bold text-gray-900">{{ config('app.name') }}</div>
            @endif

            @if(!empty($data['navigation']) && is_array($data['navigation']))
                <nav class="hidden md:flex space-x-8">
                    @foreach($data['navigation'] as $item)
                        <a href="{{ $item['url'] ?? '#' }}" class="text-gray-600 hover:text-gray-900">
                            {{ $item['label'] ?? '' }}
                        </a>
                    @endforeach
                </nav>
            @endif
        </div>
    </div>
</header>

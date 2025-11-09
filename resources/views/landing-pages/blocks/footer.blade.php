<footer class="bg-gray-900 text-white py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <p class="text-gray-400">{{ $data['copyright'] ?? '© 2025 Your Company' }}</p>
            @if(!empty($data['links']) && is_array($data['links']))
                <nav class="mt-4 flex justify-center space-x-6">
                    @foreach($data['links'] as $link)
                        <a href="{{ $link['url'] ?? '#' }}" class="text-gray-400 hover:text-white">
                            {{ $link['label'] ?? '' }}
                        </a>
                    @endforeach
                </nav>
            @endif
        </div>
    </div>
</footer>

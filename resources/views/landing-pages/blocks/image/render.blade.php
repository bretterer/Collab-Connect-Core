<x-landing-page-block.wrapper :data="$data">
    <div class="max-w-7xl mx-auto">
        @if($data['content'])
            <img src="{{ $data['content'] }}" alt="Block image" class="w-full h-auto" />
        @else
            <div class="text-center text-gray-400 py-12">
                No image selected
            </div>
        @endif
    </div>
</x-landing-page-block.wrapper>

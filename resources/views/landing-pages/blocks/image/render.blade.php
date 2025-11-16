<x-landing-page-block.wrapper :data="$data">
    @php
        $alignment = $data['alignment'] ?? 'center';
        $displayWidth = $data['display_width'] ?? null;
        $displayHeight = $data['display_height'] ?? null;
        $maintainAspect = $data['maintain_aspect_ratio'] ?? true;

        $alignmentClasses = match($alignment) {
            'left' => 'mx-0',
            'right' => 'ml-auto mr-0',
            default => 'mx-auto',
        };

        $styles = [];
        if ($displayWidth) {
            $styles[] = "max-width: {$displayWidth}px";
        }
        if ($displayHeight && !$maintainAspect) {
            $styles[] = "height: {$displayHeight}px";
        }

        $filterStyles = sprintf(
            'filter: brightness(%s) contrast(%s) blur(%spx);',
            1 + (($data['brightness'] ?? 0) / 100),
            1 + (($data['contrast'] ?? 0) / 100),
            ($data['blur'] ?? 0)
        );

        $styleAttr = implode(' ', array_merge($styles, [$filterStyles]));
    @endphp

    <div class="max-w-7xl {{ $alignmentClasses }}">
        @if($data['image_url'])
            <img
                src="{{ $data['image_url'] }}"
                alt="{{ $data['image_alt'] ?: 'Block image' }}"
                @if($data['image_width'] || $data['image_height'])
                    width="{{ $data['image_width'] }}"
                    height="{{ $data['image_height'] }}"
                @endif
                class="@if($displayWidth) w-full @else w-full @endif h-auto @if($maintainAspect) object-contain @else object-cover @endif"
                style="{{ $styleAttr }}"
                loading="lazy"
            />
        @else
            <div class="text-center text-gray-400 py-12 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-lg">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="mt-2">No image selected</p>
            </div>
        @endif
    </div>
</x-landing-page-block.wrapper>

@php
    $width = $data['width'] ?? 'full';
    $alignment = $data['alignment'] ?? 'center';
    $rounded = $data['rounded'] ?? 'none';

    $widthClass = match($width) {
        'small' => 'max-w-md',
        'medium' => 'max-w-3xl',
        'large' => 'max-w-7xl',
        default => 'max-w-full',
    };

    $alignmentClass = match($alignment) {
        'left' => 'mr-auto',
        'right' => 'ml-auto',
        default => 'mx-auto',
    };

    $roundedClass = match($rounded) {
        'sm' => 'rounded-sm',
        'md' => 'rounded-md',
        'lg' => 'rounded-lg',
        'xl' => 'rounded-xl',
        'full' => 'rounded-full',
        default => '',
    };
@endphp

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <figure class="{{ $widthClass }} {{ $alignmentClass }}">
            <img
                src="{{ $data['url'] ?? '' }}"
                alt="{{ $data['alt'] ?? '' }}"
                class="w-full h-auto {{ $roundedClass }} shadow-lg"
            />
            @if(!empty($data['caption']))
                <figcaption class="mt-3 text-sm text-gray-600 dark:text-gray-400 text-center">
                    {{ $data['caption'] }}
                </figcaption>
            @endif
        </figure>
    </div>
</div>

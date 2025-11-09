@php
    $textAlign = $data['text_align'] ?? 'left';
    $maxWidth = $data['max_width'] ?? 'prose';

    $alignClass = match($textAlign) {
        'center' => 'text-center',
        'right' => 'text-right',
        'justify' => 'text-justify',
        default => 'text-left',
    };

    $widthClass = match($maxWidth) {
        'full' => 'max-w-full',
        'narrow' => 'max-w-2xl',
        'wide' => 'max-w-5xl',
        default => 'max-w-prose', // prose (65ch)
    };
@endphp

<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="{{ $widthClass }} mx-auto {{ $alignClass }}">
            <div class="prose prose-lg dark:prose-invert max-w-none">
                {!! $data['content'] ?? '<p>Add your content here...</p>' !!}
            </div>
        </div>
    </div>
</div>

@php
    $maxWidthClass = match($data['max_width']) {
        'sm' => 'max-w-screen-sm',
        'prose' => 'max-w-prose',
        'lg' => 'max-w-screen-lg',
        'xl' => 'max-w-screen-xl',
        default => 'max-w-full',
    };

    $textAlignClass = match($data['text_align']) {
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right',
        'justify' => 'text-justify',
        default => 'text-left',
    };
@endphp

<x-landing-page-block.wrapper :data="$data" :class="$maxWidthClass . ' ' . $textAlignClass . ' mx-auto'">
    <div class="prose prose-gray max-w-none">
        {!! $data['content'] !!}
    </div>
</x-landing-page-block.wrapper>

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

    $borderClass = $data['border_type'] !== 'none'
        ? "border-{$data['border_type']}"
        : '';

    $shadowClass = $data['box_shadow'] !== 'none'
        ? "shadow-{$data['box_shadow']}"
        : '';

    $hideClasses = [];
    if ($data['desktop_hide']) {
        $hideClasses[] = 'hidden';
        if (!$data['mobile_hide']) {
            $hideClasses[] = 'md:block';
        }
    }
    if ($data['mobile_hide'] && !$data['desktop_hide']) {
        $hideClasses[] = 'hidden md:block';
    }
@endphp

<div
    class="{{ $maxWidthClass }} {{ $textAlignClass }} {{ $borderClass }} {{ $shadowClass }} mx-auto {{ implode(' ', $hideClasses) }}"
    style="
        padding-top: {{ $data['mobile_padding_top'] }}px;
        padding-bottom: {{ $data['mobile_padding_bottom'] }}px;
        padding-left: {{ $data['mobile_padding_left'] }}px;
        padding-right: {{ $data['mobile_padding_right'] }}px;
        margin-top: {{ $data['mobile_margin_top'] }}px;
        margin-bottom: {{ $data['mobile_margin_bottom'] }}px;
        background-color: {{ $data['background_color'] }};
        color: {{ $data['text_color'] }};
        @if($data['border_type'] !== 'none')
        border-width: {{ $data['border_width'] }}px;
        border-color: {{ $data['border_color'] }};
        @endif
        border-radius: {{ $data['border_radius'] }}px;

        @media (min-width: 768px) {
            padding-top: {{ $data['desktop_padding_top'] }}px;
            padding-bottom: {{ $data['desktop_padding_bottom'] }}px;
            padding-left: {{ $data['desktop_padding_left'] }}px;
            padding-right: {{ $data['desktop_padding_right'] }}px;
            margin-top: {{ $data['desktop_margin_top'] }}px;
            margin-bottom: {{ $data['desktop_margin_bottom'] }}px;
        }
    "
>
    <div class="prose prose-gray dark:prose-invert max-w-none">
        {!! $data['content'] !!}
    </div>
</div>

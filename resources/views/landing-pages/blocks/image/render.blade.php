@php
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
    class="mx-auto {{ implode(' ', $hideClasses) }}"
    style="
        padding-top: {{ $data['mobile_padding_top'] }}px;
        padding-bottom: {{ $data['mobile_padding_bottom'] }}px;
        padding-left: {{ $data['mobile_padding_left'] }}px;
        padding-right: {{ $data['mobile_padding_right'] }}px;
        margin-top: {{ $data['mobile_margin_top'] }}px;
        margin-bottom: {{ $data['mobile_margin_bottom'] }}px;
        background-color: {{ $data['background_color'] }};
        color: {{ $data['text_color'] }};

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
    {{-- Your block content goes here --}}
    <div class="max-w-7xl mx-auto">
        {{ $data['content'] }}
    </div>
</div>
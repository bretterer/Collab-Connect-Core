@props(['data', 'class' => ''])

@php
    $hideClasses = [];
    if ($data['desktop_hide'] ?? false) {
        $hideClasses[] = 'hidden';
        if (!($data['mobile_hide'] ?? false)) {
            $hideClasses[] = 'md:block';
        }
    }
    if (($data['mobile_hide'] ?? false) && !($data['desktop_hide'] ?? false)) {
        $hideClasses[] = 'hidden md:block';
    }

    // Build border style
    $borderStyle = '';
    if (($data['border_type'] ?? 'none') !== 'none') {
        $borderStyle = "border-style: {$data['border_type']}; border-width: {$data['border_width']}px; border-color: {$data['border_color']};";
    }

    // Build box shadow
    $shadowClass = match($data['box_shadow'] ?? 'none') {
        'sm' => 'shadow-sm',
        'md' => 'shadow-md',
        'lg' => 'shadow-lg',
        'xl' => 'shadow-xl',
        default => '',
    };
@endphp

<div
    {{ $attributes->merge(['class' => trim($class . ' ' . implode(' ', $hideClasses) . ' ' . $shadowClass)]) }}
    style="
        padding-top: {{ $data['mobile_padding_top'] ?? 24 }}px;
        padding-bottom: {{ $data['mobile_padding_bottom'] ?? 24 }}px;
        padding-left: {{ $data['mobile_padding_left'] ?? 16 }}px;
        padding-right: {{ $data['mobile_padding_right'] ?? 16 }}px;
        margin-top: {{ $data['mobile_margin_top'] ?? 0 }}px;
        margin-bottom: {{ $data['mobile_margin_bottom'] ?? 0 }}px;
        background-color: {{ $data['background_color'] ?? 'transparent' }};
        color: {{ $data['text_color'] ?? 'inherit' }};
        border-radius: {{ $data['border_radius'] ?? 0 }}px;
        {{ $borderStyle }}

        @media (min-width: 768px) {
            padding-top: {{ $data['desktop_padding_top'] ?? 32 }}px;
            padding-bottom: {{ $data['desktop_padding_bottom'] ?? 32 }}px;
            padding-left: {{ $data['desktop_padding_left'] ?? 16 }}px;
            padding-right: {{ $data['desktop_padding_right'] ?? 16 }}px;
            margin-top: {{ $data['desktop_margin_top'] ?? 0 }}px;
            margin-bottom: {{ $data['desktop_margin_bottom'] ?? 0 }}px;
        }
    "
>
    {{ $slot }}
</div>

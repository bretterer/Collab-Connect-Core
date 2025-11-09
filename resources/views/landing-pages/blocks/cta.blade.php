@php
    // Extract all styling options with defaults
    $bgColor = $data['background_color'] ?? '#f9fafb';
    $borderType = $data['border_type'] ?? 'none';
    $borderWidth = $data['border_width'] ?? 1;
    $borderColor = $data['border_color'] ?? '#e5e7eb';
    $borderRadius = $data['border_radius'] ?? 0;
    $boxShadow = $data['box_shadow'] ?? 'none';

    // Desktop layout
    $desktopHide = $data['desktop_hide'] ?? false;
    $desktopTextAlign = $data['desktop_text_align'] ?? 'center';
    $desktopPaddingTop = $data['desktop_padding_top'] ?? 64;
    $desktopPaddingBottom = $data['desktop_padding_bottom'] ?? 64;
    $desktopPaddingLeft = $data['desktop_padding_left'] ?? 16;
    $desktopPaddingRight = $data['desktop_padding_right'] ?? 16;
    $desktopMarginTop = $data['desktop_margin_top'] ?? 0;
    $desktopMarginBottom = $data['desktop_margin_bottom'] ?? 0;
    $desktopMarginLeft = $data['desktop_margin_left'] ?? 0;
    $desktopMarginRight = $data['desktop_margin_right'] ?? 0;

    // Mobile layout
    $mobileHide = $data['mobile_hide'] ?? false;
    $mobileTextAlign = $data['mobile_text_align'] ?? 'center';
    $mobilePaddingTop = $data['mobile_padding_top'] ?? 48;
    $mobilePaddingBottom = $data['mobile_padding_bottom'] ?? 48;
    $mobilePaddingLeft = $data['mobile_padding_left'] ?? 16;
    $mobilePaddingRight = $data['mobile_padding_right'] ?? 16;
    $mobileMarginTop = $data['mobile_margin_top'] ?? 0;
    $mobileMarginBottom = $data['mobile_margin_bottom'] ?? 0;
    $mobileMarginLeft = $data['mobile_margin_left'] ?? 0;
    $mobileMarginRight = $data['mobile_margin_right'] ?? 0;

    // Button styling
    $buttonBgColor = $data['button_bg_color'] ?? '#3b82f6';
    $buttonTextColor = $data['button_text_color'] ?? '#ffffff';
    $buttonWidth = $data['button_width'] ?? 'auto';
    $buttonStyle = $data['button_style'] ?? 'solid';
    $buttonSize = $data['button_size'] ?? 'large';
    $buttonBorderRadius = $data['button_border_radius'] ?? 8;
    $buttonNewTab = $data['button_new_tab'] ?? false;

    // Shadow mapping
    $shadowClass = match($boxShadow) {
        'small' => 'shadow-sm',
        'medium' => 'shadow-md',
        'large' => 'shadow-lg',
        default => ''
    };

    // Button size mapping
    $buttonSizeClasses = match($buttonSize) {
        'small' => 'px-6 py-2 text-sm',
        'medium' => 'px-8 py-3 text-base',
        'large' => 'px-12 py-4 text-lg',
        default => 'px-8 py-3 text-base'
    };

    // Build inline styles
    $sectionStyle = "background-color: {$bgColor};";
    if ($borderType !== 'none') {
        $sectionStyle .= " border: {$borderWidth}px {$borderType} {$borderColor};";
    }
    if ($borderRadius > 0) {
        $sectionStyle .= " border-radius: {$borderRadius}px;";
    }

    // Button styles
    $buttonStyle = "background-color: {$buttonBgColor}; color: {$buttonTextColor}; border-radius: {$buttonBorderRadius}px;";
    if ($buttonStyle === 'outline') {
        $buttonStyle = "background-color: transparent; color: {$buttonBgColor}; border: 2px solid {$buttonBgColor}; border-radius: {$buttonBorderRadius}px;";
    }
@endphp

<section
    class="{{ $shadowClass }} {{ $desktopHide ? 'hidden md:block' : '' }} {{ $mobileHide ? 'md:hidden' : '' }}"
    style="{{ $sectionStyle }}"
>
    <!-- Desktop -->
    <div
        class="hidden md:block"
        style="padding: {{ $desktopPaddingTop }}px {{ $desktopPaddingRight }}px {{ $desktopPaddingBottom }}px {{ $desktopPaddingLeft }}px; margin: {{ $desktopMarginTop }}px {{ $desktopMarginRight }}px {{ $desktopMarginBottom }}px {{ $desktopMarginLeft }}px; text-align: {{ $desktopTextAlign }};"
    >
        <div class="max-w-4xl mx-auto">
            @if(!empty($data['headline']))
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">
                    {{ $data['headline'] }}
                </h2>
            @endif

            @if(!empty($data['subheadline']))
                <p class="text-xl mb-4">
                    {{ $data['subheadline'] }}
                </p>
            @endif

            @if(!empty($data['text']))
                <div class="prose prose-lg mx-auto mb-8">
                    {!! nl2br(e($data['text'])) !!}
                </div>
            @endif

            @if(!empty($data['button_text']))
                <a
                    href="{{ $data['button_url'] ?? '#' }}"
                    {{ $buttonNewTab ? 'target="_blank" rel="noopener noreferrer"' : '' }}
                    class="inline-block {{ $buttonSizeClasses }} font-semibold transition-all hover:opacity-90 {{ $buttonWidth === 'full' ? 'w-full' : '' }}"
                    style="{{ $buttonStyle }}"
                >
                    {{ $data['button_text'] }}
                </a>
            @endif
        </div>
    </div>

    <!-- Mobile -->
    <div
        class="md:hidden"
        style="padding: {{ $mobilePaddingTop }}px {{ $mobilePaddingRight }}px {{ $mobilePaddingBottom }}px {{ $mobilePaddingLeft }}px; margin: {{ $mobileMarginTop }}px {{ $mobileMarginRight }}px {{ $mobileMarginBottom }}px {{ $mobileMarginLeft }}px; text-align: {{ $mobileTextAlign }};"
    >
        <div class="max-w-4xl mx-auto">
            @if(!empty($data['headline']))
                <h2 class="text-2xl sm:text-3xl font-bold mb-3">
                    {{ $data['headline'] }}
                </h2>
            @endif

            @if(!empty($data['subheadline']))
                <p class="text-lg mb-3">
                    {{ $data['subheadline'] }}
                </p>
            @endif

            @if(!empty($data['text']))
                <div class="prose mx-auto mb-6">
                    {!! nl2br(e($data['text'])) !!}
                </div>
            @endif

            @if(!empty($data['button_text']))
                <a
                    href="{{ $data['button_url'] ?? '#' }}"
                    {{ $buttonNewTab ? 'target="_blank" rel="noopener noreferrer"' : '' }}
                    class="inline-block {{ $buttonSizeClasses }} font-semibold transition-all hover:opacity-90 {{ $buttonWidth === 'full' ? 'w-full' : '' }}"
                    style="{{ $buttonStyle }}"
                >
                    {{ $data['button_text'] }}
                </a>
            @endif
        </div>
    </div>
</section>

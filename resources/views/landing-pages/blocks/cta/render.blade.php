@php
    // Width classes
    $widthClass = match($data['button_width']) {
        'full' => 'w-full',
        'auto' => 'w-auto inline-block',
        default => 'w-auto inline-block',
    };

    // Size classes
    $sizeClasses = match($data['button_size']) {
        'small' => 'px-4 py-2 text-sm',
        'medium' => 'px-6 py-3 text-base',
        'large' => 'px-8 py-4 text-lg',
        default => 'px-6 py-3 text-base',
    };

    // Style-specific classes and inline styles
    $styleClasses = match($data['button_style']) {
        'solid' => 'border-2 border-transparent',
        'outline' => 'border-2 bg-transparent',
        default => 'border-2 border-transparent',
    };

    // Build inline styles
    $inlineStyles = [];

    if ($data['button_style'] === 'solid') {
        $inlineStyles[] = 'background-color: ' . $data['button_bg_color'];
        $inlineStyles[] = 'color: ' . $data['button_text_color'];
    } else {
        $inlineStyles[] = 'border-color: ' . $data['button_bg_color'];
        $inlineStyles[] = 'color: ' . $data['button_bg_color'];
    }

    $inlineStyles[] = 'border-radius: ' . $data['border_radius'] . 'px';

    $styleAttribute = implode('; ', $inlineStyles);

    // Determine the action
    $action = $data['action'] ?? 'url';
    $href = '#';
    $target = '';
    $onClick = '';
    $alpineClick = '';

    if ($action === 'url') {
        $href = $data['url'] ?? '#';
        $target = ($data['open_new_tab'] ?? false) ? '_blank' : '';
    } elseif ($action === 'section') {
        $href = '#' . ($data['section_id'] ?? '');
        $onClick = 'event.preventDefault(); document.getElementById(\'' . ($data['section_id'] ?? '') . '\')?.scrollIntoView({ behavior: \'smooth\' });';
    } elseif ($action === 'two_step_optin') {
        $href = '#';
        // Only dispatch event if two-step optin is enabled
        if ($twoStepOptinEnabled ?? false) {
            $alpineClick = '$event.preventDefault(); $dispatch(\'open-two-step-optin\')';
        } else {
            $onClick = 'event.preventDefault();';
        }
    }

    // Combined classes
    $buttonClasses = trim("font-medium text-center transition-all duration-200 hover:opacity-90 {$widthClass} {$sizeClasses} {$styleClasses}");
@endphp

<x-landing-page-block.wrapper :data="$data">
    <div class="max-w-7xl mx-auto flex justify-center">
        <a
            href="{{ $href }}"
            @if($target) target="{{ $target }}" rel="noopener noreferrer" @endif
            @if($onClick) onclick="{{ $onClick }}" @endif
            @if($alpineClick) @click="{{ $alpineClick }}" @endif
            class="{{ $buttonClasses }}"
            style="{{ $styleAttribute }}"
        >
            {{ $data['text'] }}
        </a>
    </div>
</x-landing-page-block.wrapper>

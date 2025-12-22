@php
    // Support both Livewire component properties and @include with settings array
    $enabled = $enabled ?? ($settings['enabled'] ?? false);
    $text = $text ?? ($settings['text'] ?? 'Join CollabConnect');
    $style = $style ?? ($settings['style'] ?? 'secondary');
    $buttonColor = $buttonColor ?? ($settings['buttonColor'] ?? '#000000');
    $referralUrl = $referralUrl ?? ($settings['referralUrl'] ?? '');
    $containerStyle = $containerStyle ?? ($designSettings['containerStyle'] ?? 'round');
    $shadow = $shadow ?? ($settings['shadow'] ?? true);
    $outline = $outline ?? ($settings['outline'] ?? false);

    // Calculate if buttonColor is light or dark for text contrast
    $hex = ltrim($buttonColor, '#');
    $r = hexdec(substr($hex, 0, 2)) / 255;
    $g = hexdec(substr($hex, 2, 2)) / 255;
    $b = hexdec(substr($hex, 4, 2)) / 255;
    $luminance = 0.2126 * $r + 0.7152 * $g + 0.0722 * $b;
    $buttonTextColor = $luminance > 0.5 ? '#111827' : '#ffffff';
@endphp

@if($enabled && $referralUrl)
    <div class="mb-6">
        <a
            href="{{ $referralUrl }}"
            target="_blank"
            rel="noopener noreferrer"
            class="flex items-center justify-center gap-3 w-full py-4 px-6 font-medium transition-all hover:scale-[1.02] hover:opacity-90 {{ $containerStyle === 'round' ? 'rounded-full' : ($containerStyle === 'square' ? 'rounded-xl' : 'rounded-none') }}"
            @if($style === 'primary')
                style="background-color: {{ $buttonColor }}; color: {{ $buttonTextColor }}; {{ $shadow ? 'box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);' : '' }}"
            @else
                :style="
                    @if($style === 'secondary')
                        'background-color: ' + linkBgColor + '; {{ $shadow ? 'box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);' : '' }}'
                    @else
                        'background-color: transparent; border: 1px solid ' + borderColor + ';'
                    @endif
                "
                :class="textColor"
            @endif
        >
            {{-- CollabConnect Logo in Circle --}}
            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 @if($style === 'primary') bg-white/20 @endif" @if($style !== 'primary') :class="iconBgColor" @endif>
                <svg class="h-5 w-auto" viewBox="0 0 245.25 203.25" fill="currentColor">
                    <g>
                        <circle cx="72.88" cy="31.9" r="31.9" transform="translate(15.52 87.03) rotate(-67.5)"/>
                        <path d="M151.82,106.25h-7.71c-6.49,0-12.49,2.15-17.33,5.76,6.19,6.86,9.97,15.93,9.97,25.88v2.02c0,8.96-3.08,17.22-8.22,23.78-1.45,1.85-3.06,3.56-4.81,5.12-6.39,5.67-14.67,9.26-23.76,9.7-.61.03-1.23.05-1.86.05H23.7v-44.27c0-19.32,15.72-35.03,35.03-35.03h29.27c5.92-9.2,14.03-16.85,23.61-22.22-4.35-.96-8.86-1.48-13.5-1.48h-39.38C26.3,75.55,0,101.85,0,134.28v60.98c0,3.86,3.13,6.98,6.98,6.98h91.13c5.88,0,11.56-.83,16.95-2.35,3.1-.87,6.1-1.98,8.99-3.3,9.09-4.16,17-10.45,23.1-18.22,1.7-2.17,3.27-4.44,4.67-6.83,5.48-9.28,8.63-20.09,8.63-31.64v-2.02c0-11.55-3.15-22.36-8.63-31.64Z"/>
                    </g>
                    <g>
                        <circle cx="170.36" cy="31.9" r="31.9" transform="translate(-2.93 28.76) rotate(-9.57)"/>
                        <path d="M185.48,75.55h-41.37c-4.44,0-8.75.5-12.91,1.42-3.4.75-6.68,1.78-9.83,3.08-9.91,4.09-18.48,10.77-24.87,19.2-1.68,2.22-3.2,4.56-4.56,7-4.78,8.59-7.51,18.47-7.51,28.99v.57c0,13.16,3.84,25.42,10.45,35.74h3.25c7.86,0,15.05-2.88,20.59-7.64-6.58-7.52-10.58-17.35-10.58-28.1v-.57c0-8.77,3.16-16.82,8.39-23.07,1.52-1.81,3.21-3.48,5.06-4.96,6.17-4.98,14.01-7.96,22.54-7.96h41.37c19.68,0,35.68,16.01,35.68,35.68v43.62h-65.51c-5.85,8.68-13.61,15.95-22.67,21.25,5.69,1.58,11.67,2.45,17.87,2.45h87.03c3.86,0,6.98-3.13,6.98-6.98v-60.33c0-32.8-26.59-59.38-59.38-59.38Z"/>
                    </g>
                </svg>
            </div>
            <span class="text-lg font-semibold" @if($style !== 'primary') :class="textColor" @endif>{{ $text }}</span>
        </a>
    </div>
@endif

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title }} - {{ config('app.name') }}</title>

    @if($page->settings && isset($page->settings['meta_description']))
        <meta name="description" content="{{ $page->settings['meta_description'] }}">
    @elseif($page->description)
        <meta name="description" content="{{ $page->description }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    @if($page->settings && isset($page->settings['custom_css']))
        <style>{!! $page->settings['custom_css'] !!}</style>
    @endif

    @if($page->settings && isset($page->settings['head_scripts']))
        {!! $page->settings['head_scripts'] !!}
    @endif
</head>
<body class="antialiased">
    @if($page->blocks && count($page->blocks) > 0)
        @foreach($page->blocks as $section)
            @php
                $settings = $section['settings'] ?? [];
                $bgType = $settings['background_type'] ?? 'color';
                $bgColor = $settings['background_color'] ?? '#ffffff';
                $bgImage = $settings['background_image'] ?? '';
                $bgVideo = $settings['background_video'] ?? '';
                $bgPosition = $settings['background_position'] ?? 'center';
                $bgSize = $settings['background_size'] ?? 'cover';
                $bgFixed = $settings['background_fixed'] ?? false;
                $paddingTop = $settings['padding_top'] ?? 0;
                $paddingBottom = $settings['padding_bottom'] ?? 0;
                $paddingLeft = $settings['padding_left'] ?? 0;
                $paddingRight = $settings['padding_right'] ?? 0;
                $maxWidth = $settings['max_width'] ?? 'full';
                $textColor = $settings['text_color'] ?? '#000000';

                $sectionStyle = "background-color: {$bgColor};";
                if ($bgType === 'image' && !empty($bgImage)) {
                    $sectionStyle .= " background-image: url('{$bgImage}'); background-position: {$bgPosition}; background-size: {$bgSize};";
                    if ($bgFixed) {
                        $sectionStyle .= " background-attachment: fixed;";
                    }
                }
                $sectionStyle .= " padding: {$paddingTop}px {$paddingRight}px {$paddingBottom}px {$paddingLeft}px;";
                $sectionStyle .= " color: {$textColor};";
            @endphp

            <section style="{{ $sectionStyle }}">
                @if($bgType === 'video' && !empty($bgVideo))
                    <div class="absolute inset-0 overflow-hidden -z-10">
                        <video autoplay loop muted playsinline class="w-full h-full object-cover">
                            <source src="{{ $bgVideo }}" type="video/mp4">
                        </video>
                    </div>
                @endif

                <div class="{{ $maxWidth === 'container' ? 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8' : '' }}">
                    @if(isset($section['blocks']) && is_array($section['blocks']))
                        @foreach($section['blocks'] as $block)
                            @include('landing-pages.blocks.' . $block['type'], ['data' => $block['data']])
                        @endforeach
                    @endif
                </div>
            </section>
        @endforeach
    @else
        <div class="min-h-screen flex items-center justify-center">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-gray-900">No content yet</h1>
                <p class="text-gray-600 mt-4">This landing page is still being built.</p>
            </div>
        </div>
    @endif

    @if($page->settings && isset($page->settings['body_scripts']))
        {!! $page->settings['body_scripts'] !!}
    @endif

    @fluxScripts
    @livewireScripts
</body>
</html>

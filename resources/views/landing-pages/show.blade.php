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

                // Background settings
                $bgColor = $settings['background_color'] ?? '#ffffff';
                $bgImage = $settings['background_image'] ?? '';
                $bgPosition = $settings['background_position'] ?? 'center';
                $bgFixed = $settings['background_fixed'] ?? false;

                // Desktop layout
                $desktopHide = $settings['desktop_hide'] ?? false;
                $desktopPaddingTop = $settings['desktop_padding_top'] ?? 0;
                $desktopPaddingBottom = $settings['desktop_padding_bottom'] ?? 0;
                $desktopPaddingLeft = $settings['desktop_padding_left'] ?? 0;
                $desktopPaddingRight = $settings['desktop_padding_right'] ?? 0;
                $desktopVerticalAlign = $settings['desktop_vertical_align'] ?? 'top';
                $desktopHorizontalAlign = $settings['desktop_horizontal_align'] ?? 'left';

                // Mobile layout
                $mobileHide = $settings['mobile_hide'] ?? false;
                $mobilePaddingTop = $settings['mobile_padding_top'] ?? 0;
                $mobilePaddingBottom = $settings['mobile_padding_bottom'] ?? 0;
                $mobilePaddingLeft = $settings['mobile_padding_left'] ?? 0;
                $mobilePaddingRight = $settings['mobile_padding_right'] ?? 0;

                // Build background styles
                $backgroundStyles = "background-color: {$bgColor};";
                if ($bgImage) {
                    $bgAttachment = $bgFixed ? 'fixed' : 'scroll';
                    $bgPositionMap = [
                        'top' => 'center top',
                        'center' => 'center center',
                        'bottom' => 'center bottom',
                    ];
                    $bgPositionValue = $bgPositionMap[$bgPosition] ?? 'center center';
                    $backgroundStyles .= " background-image: url('{$bgImage}'); background-size: cover; background-position: {$bgPositionValue}; background-attachment: {$bgAttachment};";
                }

                // Build responsive classes
                $visibilityClasses = '';
                if ($desktopHide && $mobileHide) {
                    $visibilityClasses = 'hidden';
                } elseif ($desktopHide) {
                    $visibilityClasses = 'md:hidden';
                } elseif ($mobileHide) {
                    $visibilityClasses = 'hidden md:block';
                }

                // Build flex alignment classes
                $verticalAlignMap = [
                    'top' => 'items-start',
                    'center' => 'items-center',
                    'bottom' => 'items-end',
                ];
                $horizontalAlignMap = [
                    'left' => 'justify-start',
                    'center' => 'justify-center',
                    'right' => 'justify-end',
                    'space-around' => 'justify-around',
                    'space-between' => 'justify-between',
                ];
                $flexClasses = ($verticalAlignMap[$desktopVerticalAlign] ?? 'items-start') . ' ' . ($horizontalAlignMap[$desktopHorizontalAlign] ?? 'justify-start');
            @endphp

            <section
                style="{{ $backgroundStyles }} padding: {{ $mobilePaddingTop }}px {{ $mobilePaddingRight }}px {{ $mobilePaddingBottom }}px {{ $mobilePaddingLeft }}px;"
                class="relative {{ $visibilityClasses }}"
            >
                <style>
                    @media (min-width: 768px) {
                        section:nth-of-type({{ $loop->iteration }}) {
                            padding: {{ $desktopPaddingTop }}px {{ $desktopPaddingRight }}px {{ $desktopPaddingBottom }}px {{ $desktopPaddingLeft }}px !important;
                        }
                    }
                </style>

                <div class="flex {{ $flexClasses }} min-h-full w-full">
                    <div class="w-full">
                        @if(isset($section['blocks']) && is_array($section['blocks']))
                            @foreach($section['blocks'] as $block)
                                @php
                                    $blockInstance = \App\LandingPages\BlockRegistry::get($block['type']);
                                @endphp
                                @if($blockInstance)
                                    {!! $blockInstance->render($block['data']) !!}
                                @else
                                    @include('landing-pages.blocks.' . $block['type'] . '.render', ['data' => $block['data']])
                                @endif
                            @endforeach
                        @endif
                    </div>
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

    <!-- Two Step Optin Modal -->
    @if($page->two_step_optin && ($page->two_step_optin['enabled'] ?? false) && !empty($page->two_step_optin['blocks']))
        <div x-data="{ showTwoStepOptin: false }" @open-two-step-optin.window="showTwoStepOptin = true">
            <div x-show="showTwoStepOptin"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 style="display: none;">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-black bg-opacity-50" @click="showTwoStepOptin = false"></div>

                <!-- Modal -->
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6">
                        <!-- Close Button -->
                        <button @click="showTwoStepOptin = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <!-- Two Step Optin Content -->
                        @foreach($page->two_step_optin['blocks'] as $block)
                            @php
                                $blockInstance = \App\LandingPages\BlockRegistry::get($block['type']);
                            @endphp
                            @if($blockInstance)
                                {!! $blockInstance->render($block['data']) !!}
                            @else
                                @include('landing-pages.blocks.' . $block['type'] . '.render', ['data' => $block['data']])
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Exit Popup -->
    @if($page->exit_popup && ($page->exit_popup['enabled'] ?? false) && !empty($page->exit_popup['blocks']))
        <div x-data="exitIntentPopup()" x-cloak>
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-y-auto"
                 style="display: none;">
                <!-- Overlay -->
                <div class="fixed inset-0 bg-black bg-opacity-50" @click="show = false"></div>

                <!-- Modal -->
                <div class="flex min-h-full items-center justify-center p-4">
                    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full p-6">
                        <!-- Close Button -->
                        <button @click="show = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>

                        <!-- Exit Popup Content -->
                        @foreach($page->exit_popup['blocks'] as $block)
                            @php
                                $blockInstance = \App\LandingPages\BlockRegistry::get($block['type']);
                            @endphp
                            @if($blockInstance)
                                {!! $blockInstance->render($block['data']) !!}
                            @else
                                @include('landing-pages.blocks.' . $block['type'] . '.render', ['data' => $block['data']])
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <script>
        function exitIntentPopup() {
            return {
                show: false,
                shown: false,
                init() {
                    // Detect when user's mouse leaves the viewport (exit intent)
                    document.addEventListener('mouseleave', (e) => {
                        if (e.clientY <= 0 && !this.shown) {
                            this.show = true;
                            this.shown = true; // Only show once per session
                        }
                    });
                }
            }
        }
        </script>
    @endif

    @fluxScripts
    @livewireScripts
</body>
</html>

@php
    $videoUrl = $data['video_url'] ?? '';
    $videoTitle = $data['video_title'] ?? 'Video';
    $posterUrl = $data['poster_url'] ?? '';
    $thumbnailUrl = $data['thumbnail_url'] ?? '';
    $autoplay = $data['autoplay'] ?? false;
    $loop = $data['loop'] ?? false;
    $muted = $data['muted'] ?? false;
    $controls = $data['controls'] ?? true;
    $width = $data['width'] ?? 'full';
    $alignment = $data['alignment'] ?? 'center';

    // Build Plyr config
    $plyrConfig = [
        'autoplay' => $autoplay,
        'loop' => ['active' => $loop],
        'muted' => $muted,
        'clickToPlay' => true,
        'resetOnEnd' => !$loop,
        'keyboard' => ['focused' => true, 'global' => false],
    ];

    // Build controls array based on settings
    if ($controls) {
        $controlsArray = [];

        // Use array_key_exists to differentiate between "not set" (default true) vs "set to false"
        if (!array_key_exists('show_play_button', $data) || $data['show_play_button']) {
            $controlsArray[] = 'play-large';
            $controlsArray[] = 'play';
        }
        if (!array_key_exists('show_progress', $data) || $data['show_progress']) {
            $controlsArray[] = 'progress';
        }
        if (!array_key_exists('show_current_time', $data) || $data['show_current_time']) {
            $controlsArray[] = 'current-time';
        }
        if (!array_key_exists('show_duration', $data) || $data['show_duration']) {
            $controlsArray[] = 'duration';
        }
        if (!array_key_exists('show_mute', $data) || $data['show_mute']) {
            $controlsArray[] = 'mute';
        }
        if (!array_key_exists('show_volume', $data) || $data['show_volume']) {
            $controlsArray[] = 'volume';
        }
        if (!array_key_exists('show_fullscreen', $data) || $data['show_fullscreen']) {
            $controlsArray[] = 'fullscreen';
        }

        if (!empty($controlsArray)) {
            $plyrConfig['controls'] = $controlsArray;
        }
    } else {
        $plyrConfig['controls'] = false;
    }

    $plyrConfigJson = json_encode($plyrConfig);

    // Width classes
    $widthClass = match($width) {
        'full' => 'w-full',
        'large' => 'w-full md:w-4/5',
        'medium' => 'w-full md:w-3/5',
        'small' => 'w-full md:w-2/5',
        default => 'w-full',
    };

    // Alignment classes
    $alignmentClass = match($alignment) {
        'left' => 'mx-0',
        'center' => 'mx-auto',
        'right' => 'ml-auto mr-0',
        default => 'mx-auto',
    };
@endphp

<x-landing-page-block.wrapper :data="$data">
    @if($videoUrl)
        <div class="max-w-7xl mx-auto">
            <div class="{{ $widthClass }} {{ $alignmentClass }}">
                <div
                    x-data="{
                        player: null,
                        showThumbnail: {{ !empty($thumbnailUrl) ? 'true' : 'false' }},
                        playVideo() {
                            this.showThumbnail = false;
                            if (this.player) {
                                this.player.play();
                            }
                        }
                    }"
                    x-init="
                        const video = $refs.videoElement;
                        if (video && window.Plyr) {
                            const config = {{ Js::from($plyrConfig) }};
                            player = new Plyr(video, config);
                            if (config.autoplay) {
                                showThumbnail = false;
                            }
                        }
                    "
                    class="relative"
                >
                    <video
                        x-ref="videoElement"
                        class="w-full h-auto rounded-lg"
                        playsinline
                        @if($posterUrl && !$thumbnailUrl) poster="{{ $posterUrl }}" @endif
                    >
                        <source src="{{ $videoUrl }}" type="video/mp4">
                        <source src="{{ $videoUrl }}" type="video/webm">
                        Your browser does not support the video tag.
                    </video>

                    {{-- Custom Thumbnail Overlay with Play Button --}}
                    @if($thumbnailUrl)
                        <div
                            x-show="showThumbnail"
                            x-cloak
                            @click="playVideo()"
                            class="absolute inset-0 cursor-pointer group"
                        >
                            {{-- Thumbnail Image --}}
                            <img
                                src="{{ $thumbnailUrl }}"
                                alt="{{ $videoTitle }}"
                                class="w-full h-full object-cover rounded-lg"
                            />

                            {{-- Play Button Overlay --}}
                            <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/30 transition-colors rounded-lg">
                                <div class="w-20 h-20 md:w-24 md:h-24 bg-white/90 rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform">
                                    <svg class="w-10 h-10 md:w-12 md:h-12 text-gray-900 ml-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Screen reader text --}}
                    @if($videoTitle)
                        <span class="sr-only">{{ $videoTitle }}</span>
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- Placeholder when no video is uploaded --}}
        <div class="max-w-7xl mx-auto text-center py-12">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
            </div>
            <p class="text-gray-500 dark:text-gray-400 text-sm">No video uploaded</p>
        </div>
    @endif
</x-landing-page-block.wrapper>

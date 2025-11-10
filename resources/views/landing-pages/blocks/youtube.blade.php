@php
    // Extract video ID from various YouTube URL formats
    $videoId = $data['video_id'] ?? '';

    // If it's a full URL, extract the video ID
    if (str_contains($videoId, 'youtube.com') || str_contains($videoId, 'youtu.be')) {
        // Handle youtube.com/watch?v=VIDEO_ID
        if (preg_match('/[?&]v=([^&]+)/', $videoId, $matches)) {
            $videoId = $matches[1];
        }
        // Handle youtu.be/VIDEO_ID
        elseif (preg_match('/youtu\.be\/([^?]+)/', $videoId, $matches)) {
            $videoId = $matches[1];
        }
        // Handle youtube.com/embed/VIDEO_ID
        elseif (preg_match('/youtube\.com\/embed\/([^?]+)/', $videoId, $matches)) {
            $videoId = $matches[1];
        }
    }

    $maxWidth = $data['max_width'] ?? 'large';
    $aspectRatio = $data['aspect_ratio'] ?? '16/9';

    $widthClass = match($maxWidth) {
        'small' => 'max-w-2xl',
        'medium' => 'max-w-3xl',
        'large' => 'max-w-5xl',
        default => 'max-w-full',
    };

    // Build YouTube embed parameters
    $params = [];

    if ($data['autoplay'] ?? false) {
        $params[] = 'autoplay=1';
        $params[] = 'mute=1'; // Autoplay requires mute in most browsers
    }

    if (!($data['controls'] ?? true)) {
        $params[] = 'controls=0';
    }

    if ($data['loop'] ?? false) {
        $params[] = 'loop=1';
        $params[] = 'playlist=' . $videoId; // Loop requires playlist parameter
    }

    if ($data['mute'] ?? false) {
        $params[] = 'mute=1';
    }

    if ($data['modest_branding'] ?? false) {
        $params[] = 'modestbranding=1';
    }

    $color = $data['color'] ?? 'red';
    if ($color === 'white') {
        $params[] = 'color=white';
    }

    $paramString = !empty($params) ? '?' . implode('&', $params) : '';
    $embedUrl = "https://www.youtube.com/embed/{$videoId}{$paramString}";
@endphp

<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="{{ $widthClass }} mx-auto">
            <div class="relative w-full" style="aspect-ratio: {{ $aspectRatio }};">
                <iframe
                    src="{{ $embedUrl }}"
                    class="absolute inset-0 w-full h-full rounded-lg shadow-lg"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen
                ></iframe>
            </div>
        </div>
    </div>
</div>

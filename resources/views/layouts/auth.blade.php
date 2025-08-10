<!DOCTYPE html>
<html class="h-full"
      lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0" />

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
        <link rel="manifest" href="/site.webmanifest" />

    <link href="https://fonts.bunny.net"
          rel="preconnect">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600"
          rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @fluxAppearance()

    @turnstileScripts()

</head>

<body class="h-full bg-white dark:bg-gray-900">
    @php
        $bgImage = collect([
            [
                'image' =>
                    'https://images.unsplash.com/photo-1678616473860-5c6978031719?q=80&w=838&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'alt' => 'a-man-sitting-on-a-stool-with-a-cell-phone-in-his-hand',
            ],
            [
                'image' =>
                    'https://images.unsplash.com/photo-1658207951097-96f86cc0a1c8?q=80&w=930&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'alt' => 'a-woman-holding-a-picture-of-a-man',
            ],
            [
                'image' =>
                    'https://plus.unsplash.com/premium_photo-1684017834245-f714094ca936?q=80&w=774&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'alt' => 'a-womans-hand-touching-a-cell-phone-with-a-camera-attached-to-it',
            ],
            [
                'image' =>
                    'https://plus.unsplash.com/premium_photo-1684017834521-ac0d1edbd23d?q=80&w=774&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'alt' => 'two-women-sitting-on-a-couch-with-a-camera-in-front-of-them',
            ],
            [
                'image' =>
                    'https://plus.unsplash.com/premium_photo-1684953432025-2933206ca61b?q=80&w=774&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D',
                'alt' => 'a-man-taking-a-picture-of-a-skateboarder',
            ],
        ])->random();

    @endphp
    <div class="flex min-h-full">
        <div class="relative hidden w-0 flex-1 lg:block">
            <img class="absolute inset-0 size-full object-cover"
                 src="{{ $bgImage['image'] }}"
                 alt="{{ $bgImage['alt'] }}" />
            <!-- Dark overlay for better contrast in dark mode -->
            <div class="absolute inset-0 bg-black/20 dark:bg-black/40"></div>
        </div>
        <div class="flex flex-1 flex-col justify-center bg-white px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24 dark:bg-gray-900">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                {{ $slot }}
            </div>
        </div>

    </div>

    @fluxScripts()

</body>

</html>

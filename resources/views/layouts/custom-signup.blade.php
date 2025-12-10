<!DOCTYPE html>
<html class="h-full"
      lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true' || (localStorage.getItem('darkMode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
    x-init="$watch('darkMode', value => localStorage.setItem('darkMode', value))"
    x-bind:class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>{{ $title ?? config('app.name') }}</title>

    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
    <link rel="manifest" href="/site.webmanifest" />

    <link href="https://fonts.bunny.net" rel="preconnect">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Dark mode flash prevention -->
    <script>
        (function() {
            const isDark = localStorage.getItem('darkMode') === 'true' ||
                (localStorage.getItem('darkMode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches);
            if (isDark) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if(!app()->environment('local'))
    <script
        defer
        data-website-id="68953b233e0aad41246ad8b4"
        data-domain="collabconnect.app"
        src="https://datafa.st/js/script.js">
    </script>
    @endif
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 font-sans antialiased">
    <div class="min-h-full">
        {{ $slot }}
    </div>

    @fluxScripts()
    <x-toaster-hub />
    @stack('scripts')
</body>

</html>

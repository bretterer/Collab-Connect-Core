<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('description', '')">

    <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if(!app()->environment('local'))
        <script
            defer
            data-website-id="68953b233e0aad41246ad8b4"
            data-domain="collabconnect.app"
            src="https://datafa.st/js/script.js">
        </script>
    @endif

    @stack('styles')

    <x-metapixel-head :userIdAsString="true"/>
</head>
<body class="antialiased">
    <x-metapixel-body/>
    {{ $slot }}

    @stack('scripts')
</body>
</html>

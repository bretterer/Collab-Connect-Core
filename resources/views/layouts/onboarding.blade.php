<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    x-data="{
        darkMode: localStorage.getItem('darkMode') === 'true' || (localStorage.getItem('darkMode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches)
      }"
    x-init="$watch('darkMode', value => localStorage.setItem('darkMode', value))"
    x-bind:class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <x-head />
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <x-body />

    <!-- Header with Logout -->
    <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Brand -->
                <div class="flex items-center">
                    <flux:heading size="lg" class="text-gray-900 dark:text-gray-100">
                        {{ config('app.name', 'CollabConnect') }}
                    </flux:heading>
                </div>

                <!-- Logout Button -->
                <div class="flex items-center space-x-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <flux:button type="submit" variant="ghost" size="sm" icon="arrow-right-start-on-rectangle">
                            Logout
                        </flux:button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main content area -->
    <main>
        {{ $slot }}
    </main>

    <!-- Developer Tools Drawer (local only) -->
    @if(app()->environment('local'))
        <livewire:developer-tools-drawer />
    @endif

    @fluxScripts
    @livewireScripts
    <x-toaster-hub />
    @stack('scripts')
</body>

</html>
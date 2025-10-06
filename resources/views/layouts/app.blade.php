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

    @if(!app()->environment('local'))
            <script
                defer
                data-website-id="68953b233e0aad41246ad8b4"
                data-domain="collabconnect.app"
                src="https://datafa.st/js/script.js">
            </script>
        @endif
</head>

<body class="font-sans antialiased bg-gray-50 dark:bg-gray-900 transition-colors duration-300"
    x-cloak
    x-data="{ sidebarOpen: false, sidebarExpanded: window.innerWidth >= 1400 ? (localStorage.getItem('sidebarExpanded') !== 'false') : false }"
    x-init="$watch('sidebarExpanded', value => localStorage.setItem('sidebarExpanded', value))">

    <!-- Off-canvas menu for mobile -->
    <div x-show="sidebarOpen"
        x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 lg:hidden"
        style="display: none;">
        <div class="fixed inset-0 bg-gray-900/80" x-on:click="sidebarOpen = false"></div>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen"
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="relative flex w-full max-w-xs flex-1 flex-col bg-white dark:bg-gray-900">

            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button x-on:click="sidebarOpen = false" class="ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            @include('layouts.partials.app-sidebar')
        </div>
    </div>

    <!-- Desktop sidebar -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:flex-col"
        x-bind:class="sidebarExpanded ? 'lg:w-72' : 'lg:w-20'">
        @include('layouts.partials.app-sidebar')
    </div>

    <!-- Main content -->
    <div class="lg:pl-20" x-bind:class="{ 'lg:pl-72': sidebarExpanded, 'lg:pl-20': !sidebarExpanded }">
        <!-- Top bar -->
        <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-6 border-b border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm px-4 shadow-sm sm:px-6 lg:px-8">
            <!-- Mobile menu button -->
            <button x-on:click="sidebarOpen = true" class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-300 lg:hidden">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <!-- Desktop sidebar toggle -->
            <button x-on:click="sidebarExpanded = !sidebarExpanded" class="hidden lg:flex -m-2.5 p-2.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            <!-- Breadcrumb -->
            <nav class="flex flex-1" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <div class="flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                Dashboard
                            </a>
                        </div>
                    </li>
                </ol>
            </nav>

            <!-- Right side -->
            <div class="flex items-center gap-x-4 lg:gap-x-6">

                <!-- Notifications -->
                <livewire:notification-button />

                <!-- Dark mode toggle -->
                <button x-on:click="darkMode = !darkMode"
                    class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors">
                    <svg x-show="!darkMode"
                        x-transition:enter="transition ease-in duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-out duration-200"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                    <svg x-show="darkMode"
                        x-transition:enter="transition ease-in duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-out duration-200"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                </button>

                <!-- Profile dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button x-on:click="open = !open" class="flex items-center gap-x-2 -m-1.5 p-1.5">
                        <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="hidden lg:flex lg:items-center">
                            <span class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>
                            <svg class="ml-2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </div>
                    </button>

                    <div x-show="open"
                        x-on:click.away="open = false"
                        x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute right-0 z-10 mt-2.5 w-48 origin-top-right rounded-md bg-white dark:bg-gray-800 py-2 shadow-lg ring-1 ring-gray-900/5 dark:ring-gray-700 focus:outline-none"
                        style="display: none;">
                        <a href="{{ route('profile.edit') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">Your profile</a>

                        @if(auth()->user()->isBusinessAccount() && auth()->user()->businesses()->count() > 1)
                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                            <div class="px-3 py-2">
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Switch Business</p>
                                @foreach(auth()->user()->businesses as $business)
                                    <form method="POST" action="{{ route('switch-business', $business->id) }}" class="mb-1">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-2 py-1 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded {{ auth()->user()->currentBusiness?->id == $business->id ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' : '' }}">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 rounded-full {{ auth()->user()->currentBusiness?->id == $business->id ? 'bg-blue-500' : 'bg-gray-300' }} mr-2"></div>
                                                <div>
                                                    <div class="font-medium">{{ $business->name }}</div>
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ ucfirst($business->pivot->role) }}</div>
                                                </div>
                                            </div>
                                        </button>
                                    </form>
                                @endforeach
                            </div>
                            <div class="border-t border-gray-100 dark:border-gray-700"></div>
                        @endif

                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">Admin Dashboard</a>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="block w-full text-left px-3 py-1 text-sm leading-6 text-gray-900 dark:text-gray-100 hover:bg-gray-50 dark:hover:bg-gray-700">Sign out</button>
                        </form>

                        @if (app()->environment('local'))
                        <div class="border-t border-gray-100 dark:border-gray-700"></div>
                        <!-- Local Development Helpers -->
                        <div
                            class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700">
                            <x-login-link class="text-sm/6 font-semibold"
                                :email="config('collabconnect.init_user_email')"
                                label="Login as Admin"
                                redirect-url="{{ route('dashboard') }}" />
                        </div>

                        <div
                            class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700">
                            <x-login-link class="text-sm/6 font-semibold"
                                :email="config('collabconnect.init_business_email')"
                                label="Login as Business"
                                redirect-url="{{ route('dashboard') }}" />
                        </div>

                        <div
                            class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700">
                            <x-login-link class="text-sm/6 font-semibold"
                                :email="config('collabconnect.init_influencer_email')"
                                label="Login as Influencer"
                                redirect-url="{{ route('dashboard') }}" />
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        <!-- Main content area -->
        <main class="py-6">
            <div class="px-4 sm:px-6 lg:px-8">
                <livewire:banner />
                {{ $slot }}
            </div>
        </main>
    </div>

    @fluxScripts
    @livewireScripts
    <x-toaster-hub />

    <!-- Feedback Widget -->
    <livewire:feedback-widget />
</body>

</html>
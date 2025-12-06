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

        // Fix dark mode on Livewire navigation
        document.addEventListener('livewire:navigated', () => {
            const isDark = localStorage.getItem('darkMode') === 'true' ||
                (localStorage.getItem('darkMode') === null && window.matchMedia('(prefers-color-scheme: dark)').matches);

            if (isDark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
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
    x-data="{ sidebarOpen: false }">

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
            class="fixed inset-y-0 left-0 flex w-full max-w-xs flex-col bg-white dark:bg-gray-900">

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
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-40 lg:flex lg:w-58 lg:flex-col">
        @include('layouts.partials.app-sidebar')
    </div>

    <!-- Main content -->
    <div class="lg:pl-58">
        <!-- Top bar -->
        <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-6 border-b border-gray-200 dark:border-gray-800 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm px-4 shadow-sm sm:px-6 lg:px-8">
            <!-- Mobile menu button -->
            <button x-on:click="sidebarOpen = true" class="-m-2.5 p-2.5 text-gray-700 dark:text-gray-300 lg:hidden">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>

            @if(false===true) <!-- do not show toggle, but don't lose the code -->
            <!-- Desktop sidebar toggle -->
            <button x-on:click="sidebarExpanded = !sidebarExpanded" class="hidden lg:flex -m-2.5 p-2.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            @endif

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
                <flux:dropdown align="end">
                    <flux:profile circle :name="auth()->user()->name" avatar:color="purple" />

                    <flux:navmenu class="max-w-[14rem]">
                        <div class="px-2 py-1.5">
                            <flux:text size="sm">Signed in as</flux:text>
                            <flux:heading class="mt-1! truncate">{{ auth()->user()->email }}</flux:heading>
                        </div>

                        <flux:navmenu.separator />

                        <flux:navmenu.item href="{{ route('profile.edit') }}" icon="user" class="text-zinc-800 dark:text-white">
                            Your Profile
                        </flux:navmenu.item>

                        @if(auth()->user()->isAdmin())
                            <flux:navmenu.item href="{{ route('admin.dashboard') }}" icon="shield-check" class="text-zinc-800 dark:text-white">
                                Admin Dashboard
                            </flux:navmenu.item>
                        @endif

                        @if(auth()->user()->isBusinessAccount() && auth()->user()->businesses()->count() >= 1)
                            <flux:navmenu.separator />

                            <div class="px-2 py-1.5">
                                <flux:text size="sm" class="pl-7">Businesses</flux:text>
                            </div>

                            @foreach(auth()->user()->businesses as $business)
                                <form method="POST" action="{{ route('switch-business', $business) }}" class="contents" wire:key="switch-business-{{ $business->id }}">
                                    @method('PUT')
                                    @csrf
                                    @if(auth()->user()->isCurrentBusiness($business))
                                        <flux:navmenu.item type="submit" icon="check" class="text-zinc-800 dark:text-white truncate">{{ $business->name }}</flux:navmenu.item>
                                    @else
                                        <flux:navmenu.item type="submit" indent class="text-zinc-800 dark:text-white truncate">{{ $business->name }}</flux:navmenu.item>
                                    @endif
                                </form>
                            @endforeach

                            <flux:navmenu.separator />

                            <flux:navmenu.item href="{{ route('business.settings') }}" icon="cog-6-tooth" class="text-zinc-800 dark:text-white">
                                Business Settings
                            </flux:navmenu.item>
                        @endif

                        <flux:navmenu.separator />

                        <form method="POST" action="{{ route('logout') }}" class="contents">
                            @csrf
                            <flux:navmenu.item type="submit" icon="arrow-right-start-on-rectangle" class="text-zinc-800 dark:text-white">
                                Sign out
                            </flux:navmenu.item>
                        </form>

                        @if(app()->environment('local'))
                            <flux:navmenu.separator />

                            <div class="px-2 py-1.5">
                                <flux:text size="sm" class="pl-7">Dev Shortcuts</flux:text>
                            </div>

                            <div class="pl-7">
                                <x-login-link
                                    class="flex w-full items-center gap-2 px-2 py-1.5 text-left text-sm text-zinc-800 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-md"
                                    :email="config('collabconnect.init_user_email')"
                                    label="Login as Admin"
                                    redirect-url="{{ route('dashboard') }}"
                                />
                            </div>

                            <div class="pl-7">
                                <x-login-link
                                    class="flex w-full items-center gap-2 px-2 py-1.5 text-left text-sm text-zinc-800 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-md"
                                    :email="config('collabconnect.init_business_email')"
                                    label="Login as Business"
                                    redirect-url="{{ route('dashboard') }}"
                                />
                            </div>

                            <div class="pl-7">
                                <x-login-link
                                    class="flex w-full items-center gap-2 px-2 py-1.5 text-left text-sm text-zinc-800 dark:text-white hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-md"
                                    :email="config('collabconnect.init_influencer_email')"
                                    label="Login as Influencer"
                                    redirect-url="{{ route('dashboard') }}"
                                />
                            </div>
                        @endif
                    </flux:navmenu>
                </flux:dropdown>
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

    @stack('scripts')
    <!-- Feedback Widget -->
    <livewire:feedback-widget />
</body>

</html>
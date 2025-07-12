<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard - {{ config('app.name') }}</title>

    <link href="/apple-touch-icon.png" rel="apple-touch-icon" sizes="180x180">
    <link type="image/png" href="/favicon-32x32.png" rel="icon" sizes="32x32">
    <link type="image/png" href="/favicon-16x16.png" rel="icon" sizes="16x16">
    <link href="/site.webmanifest" rel="manifest">

    <link href="https://fonts.bunny.net" rel="preconnect">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance()
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900">
    <div class="h-full flex flex-col">
        <!-- Top Navigation Bar -->
        <nav class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700" x-data="{ mobileMenuOpen: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Left Section: Logo + Navigation -->
                    <div class="flex items-center space-x-8">
                        <!-- Logo -->
                        <div class="flex-shrink-0 flex items-center">
                            <img class="block h-8 w-auto dark:hidden"
                                 src="{{ Vite::asset('resources/images/CollabConnectLogo.png') }}"
                                 alt="CollabConnect Logo" />
                            <img class="hidden h-8 w-auto dark:block"
                                 src="{{ Vite::asset('resources/images/CollabConnectLogoDark.png') }}"
                                 alt="CollabConnect Logo" />
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden md:flex items-center space-x-6">
                            <a href="{{ route('dashboard') }}"
                               class="text-gray-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 text-sm font-medium border-b-2 border-blue-500">
                                Dashboard
                            </a>
                            @if(auth()->user()->account_type === App\Enums\AccountType::INFLUENCER)
                                <a href="#"
                                   class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium">
                                    Campaigns
                                </a>
                                <a href="#"
                                   class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium">
                                    Applications
                                </a>
                                <a href="#"
                                   class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium">
                                    Media Kit
                                </a>
                            @elseif(auth()->user()->account_type === App\Enums\AccountType::BUSINESS)
                                <a href="#"
                                   class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium">
                                    Campaigns
                                </a>
                                <a href="#"
                                   class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium">
                                    Influencers
                                </a>
                                <a href="#"
                                   class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium">
                                    Analytics
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Right Section: Search + Notifications + Profile -->
                    <div class="flex items-center space-x-4">


                        <!-- Theme Switcher -->
                        <div class="relative" x-data="{
                            open: false,
                            theme: localStorage.getItem('theme') || 'system',
                            init() {
                                this.applyTheme();
                                this.$watch('theme', () => {
                                    localStorage.setItem('theme', this.theme);
                                    this.applyTheme();
                                });
                            },
                            applyTheme() {
                                if (this.theme === 'dark' || (this.theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                                    document.documentElement.classList.add('dark');
                                } else {
                                    document.documentElement.classList.remove('dark');
                                }
                            },
                            get currentIcon() {
                                if (this.theme === 'light') return 'sun';
                                if (this.theme === 'dark') return 'moon';
                                return 'computer';
                            }
                        }">
                            <button @click="open = !open"
                                    class="relative p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-full">
                                <span class="sr-only">Change theme</span>
                                <!-- Sun icon -->
                                <svg x-show="currentIcon === 'sun'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <!-- Moon icon -->
                                <svg x-show="currentIcon === 'moon'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                </svg>
                                <!-- Computer icon -->
                                <svg x-show="currentIcon === 'computer'" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </button>

                            <!-- Theme Dropdown -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <div class="py-1">
                                    <button @click="theme = 'light'; open = false"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center"
                                            :class="{ 'bg-gray-50 dark:bg-gray-700': theme === 'light' }">
                                        <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        Light
                                        <svg x-show="theme === 'light'" class="ml-auto h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    <button @click="theme = 'dark'; open = false"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center"
                                            :class="{ 'bg-gray-50 dark:bg-gray-700': theme === 'dark' }">
                                        <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                        </svg>
                                        Dark
                                        <svg x-show="theme === 'dark'" class="ml-auto h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    <button @click="theme = 'system'; open = false"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center"
                                            :class="{ 'bg-gray-50 dark:bg-gray-700': theme === 'system' }">
                                        <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        System
                                        <svg x-show="theme === 'system'" class="ml-auto h-4 w-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="relative p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-full">
                                <span class="sr-only">View notifications</span>
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                                <!-- Notification badge -->
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white dark:ring-gray-800"></span>
                            </button>

                            <!-- Notifications Dropdown -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <div class="py-1">
                                    <div class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 border-b border-gray-200 dark:border-gray-700">
                                        <div class="flex items-center justify-between">
                                            <span class="font-medium">Notifications</span>
                                            <span class="text-xs text-gray-500">3 new</span>
                                        </div>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto">
                                        <!-- Sample notifications -->
                                        <a href="#" class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <div class="h-2 w-2 bg-blue-500 rounded-full mt-2"></div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium">New campaign application</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">2 minutes ago</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <div class="h-2 w-2 bg-green-500 rounded-full mt-2"></div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium">Campaign approved</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">1 hour ago</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#" class="block px-4 py-3 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    <div class="h-2 w-2 bg-yellow-500 rounded-full mt-2"></div>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm font-medium">Profile update required</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400">2 hours ago</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="px-4 py-2 border-t border-gray-200 dark:border-gray-700">
                                        <a href="#" class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-500">View all notifications</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                    class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-white text-sm font-medium">
                                    {{ auth()->user()->initials() }}
                                </div>

                            </button>

                            <!-- Profile Dropdown Menu -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 @click.away="open = false"
                                 class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                                <div class="py-1">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="flex items-center">
                                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            Profile
                                        </div>
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="flex items-center">
                                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Settings
                                        </div>
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <div class="flex items-center">
                                            <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Help & Support
                                        </div>
                                    </a>
                                    <div class="border-t border-gray-100 dark:border-gray-700"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <div class="flex items-center">
                                                <svg class="mr-3 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                Sign out
                                            </div>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                                                <!-- Mobile menu button -->
                        <div class="md:hidden">
                            <button type="button"
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500"
                                    @click="mobileMenuOpen = !mobileMenuOpen">
                                <span class="sr-only">Open main menu</span>
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div class="md:hidden" x-show="mobileMenuOpen"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95">
                <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('dashboard') }}" class="text-gray-900 dark:text-white block px-3 py-2 text-base font-medium border-l-4 border-blue-500 bg-blue-50 dark:bg-blue-900/20">Dashboard</a>
                    @if(auth()->user()->account_type->value === 1)
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white block px-3 py-2 text-base font-medium">Campaigns</a>
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white block px-3 py-2 text-base font-medium">Applications</a>
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white block px-3 py-2 text-base font-medium">Media Kit</a>
                    @elseif(auth()->user()->account_type->value === 2)
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white block px-3 py-2 text-base font-medium">Campaigns</a>
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white block px-3 py-2 text-base font-medium">Influencers</a>
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white block px-3 py-2 text-base font-medium">Analytics</a>
                    @endif
                    <!-- Mobile Search -->
                    <div class="px-3 py-2">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text"
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                   placeholder="Search...">
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content Area -->
        <main class="flex-1 overflow-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                @if(session('message'))
                    <div class="mb-6 rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                    {{ session('message') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Welcome Section -->
                <div class="mb-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                Welcome to CollabConnect, {{ auth()->user()->name }}! 🎉
                            </h1>
                            <p class="text-gray-600 dark:text-gray-400">
                                @if(auth()->user()->account_type->value === 1)
                                    Your influencer profile is now active. You can start applying for campaigns and connecting with businesses.
                                @elseif(auth()->user()->account_type->value === 2)
                                    Your business profile is ready! You can now start searching for local influencers and creating campaigns.
                                @else
                                    Great! You're all set up and ready to start collaborating.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-8">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Profile</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">Complete</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Account Type</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ auth()->user()->account_type->label() }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Status</dt>
                                        <dd class="text-lg font-medium text-gray-900 dark:text-white">Active</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">What's Next?</h3>
                        <div class="space-y-3">
                            @if(auth()->user()->account_type->value === 1)
                                <!-- Influencer next steps -->
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400">1</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium text-gray-900 dark:text-white">Browse available campaigns</span> - Find businesses looking for collaborators in your area
                                    </p>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400">2</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium text-gray-900 dark:text-white">Complete your media kit</span> - Showcase your best content and analytics
                                    </p>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-blue-600 dark:text-blue-400">3</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium text-gray-900 dark:text-white">Start applying</span> - Apply to campaigns that match your niche and interests
                                    </p>
                                </div>
                            @elseif(auth()->user()->account_type->value === 2)
                                <!-- Business next steps -->
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-green-600 dark:text-green-400">1</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium text-gray-900 dark:text-white">Search for influencers</span> - Find local creators that align with your brand
                                    </p>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-green-600 dark:text-green-400">2</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium text-gray-900 dark:text-white">Create your first campaign</span> - Define your collaboration goals and requirements
                                    </p>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-medium text-green-600 dark:text-green-400">3</span>
                                        </div>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        <span class="font-medium text-gray-900 dark:text-white">Start collaborating</span> - Connect with influencers and launch your campaigns
                                    </p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-6">
                            <flux:button variant="primary">
                                @if(auth()->user()->account_type->value === 1)
                                    Browse Campaigns
                                @elseif(auth()->user()->account_type->value === 2)
                                    Find Influencers
                                @else
                                    Get Started
                                @endif
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @fluxScripts()
</body>
</html>
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
                                <a href="{{ route('search') }}"
                                   class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium">
                                    Find Businesses
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
                                <a href="{{ route('search') }}"
                                   class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white px-3 py-2 text-sm font-medium">
                                    Find Influencers
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
                        <a href="{{ route('search') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white block px-3 py-2 text-base font-medium">Find Businesses</a>
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white block px-3 py-2 text-base font-medium">Media Kit</a>
                    @elseif(auth()->user()->account_type->value === 2)
                        <a href="#" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white block px-3 py-2 text-base font-medium">Campaigns</a>
                        <a href="{{ route('search') }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white block px-3 py-2 text-base font-medium">Find Influencers</a>
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

                @if(auth()->user()->account_type === App\Enums\AccountType::BUSINESS)
                    <!-- BUSINESS DASHBOARD -->
                    <!-- Welcome Section -->
                    <div class="mb-8">
                        <div class="bg-gradient-to-r from-blue-600 to-purple-600 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6 text-white">
                                <h1 class="text-3xl font-bold mb-2">
                                    Welcome back, {{ auth()->user()->name }}! 🚀
                                </h1>
                                <p class="text-blue-100 text-lg">
                                    Your business profile is active. Ready to connect with local influencers and grow your brand?
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Business Stats Grid -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                        <!-- Active Campaigns -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Campaigns</dt>
                                            <dd class="text-lg font-medium text-gray-900 dark:text-white">3</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Applications Received -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">New Applications</dt>
                                            <dd class="text-lg font-medium text-gray-900 dark:text-white">12</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Connected Influencers -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Connected Influencers</dt>
                                            <dd class="text-lg font-medium text-gray-900 dark:text-white">8</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Reach -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Reach</dt>
                                            <dd class="text-lg font-medium text-gray-900 dark:text-white">45.2K</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <flux:button variant="primary" class="flex items-center justify-center space-x-2 h-12">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Create Campaign</span>
                            </flux:button>
                            <a href="{{ route('search') }}">
                                <flux:button variant="outline" class="flex items-center justify-center space-x-2 h-12 w-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span>Find Influencers</span>
                                </flux:button>
                            </a>
                            <flux:button variant="outline" class="flex items-center justify-center space-x-2 h-12">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>View Analytics</span>
                            </flux:button>
                            <flux:button variant="outline" class="flex items-center justify-center space-x-2 h-12">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>Messages</span>
                            </flux:button>
                        </div>
                    </div>

                    <!-- Recent Activity & Pending Applications -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <!-- Recent Activity -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                                <div class="space-y-4">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                <span class="font-medium">@sarah_lifestyle</span> completed your "Summer Collection" campaign
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">2 hours ago</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                New application from <span class="font-medium">@mike_fitness</span> for "Protein Shake Review"
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">4 hours ago</p>
                                        </div>
                                    </div>
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-900 dark:text-white">
                                                <span class="font-medium">@jenny_food</span> sent you a message about collaboration terms
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">1 day ago</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <flux:button variant="subtle" size="sm">View All Activity</flux:button>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Applications -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pending Applications</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-pink-400 to-red-400 rounded-full flex items-center justify-center text-white font-medium">
                                                AL
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">@alex_local</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">12.5K followers • Local Food</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <flux:button variant="primary" size="sm">Accept</flux:button>
                                            <flux:button variant="outline" size="sm">Decline</flux:button>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white font-medium">
                                                MR
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">@maria_reviews</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">8.2K followers • Product Reviews</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <flux:button variant="primary" size="sm">Accept</flux:button>
                                            <flux:button variant="outline" size="sm">Decline</flux:button>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-teal-400 rounded-full flex items-center justify-center text-white font-medium">
                                                TF
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">@tom_fitness</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">25.1K followers • Fitness</p>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2">
                                            <flux:button variant="primary" size="sm">Accept</flux:button>
                                            <flux:button variant="outline" size="sm">Decline</flux:button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <flux:button variant="subtle" size="sm">View All Applications</flux:button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Performance -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Active Campaigns</h3>
                            <div class="space-y-4">
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Summer Collection Launch</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            Active
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Influencers</p>
                                            <p class="font-medium text-gray-900 dark:text-white">3 / 5</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Total Reach</p>
                                            <p class="font-medium text-gray-900 dark:text-white">28.5K</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Engagement</p>
                                            <p class="font-medium text-gray-900 dark:text-white">4.2%</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Budget</p>
                                            <p class="font-medium text-gray-900 dark:text-white">$750</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">Local Coffee Shop Promo</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                            Recruiting
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Influencers</p>
                                            <p class="font-medium text-gray-900 dark:text-white">1 / 3</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Applications</p>
                                            <p class="font-medium text-gray-900 dark:text-white">8</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Location</p>
                                            <p class="font-medium text-gray-900 dark:text-white">Downtown</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 dark:text-gray-400">Budget</p>
                                            <p class="font-medium text-gray-900 dark:text-white">$300</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif(auth()->user()->account_type === App\Enums\AccountType::INFLUENCER)
                    <!-- INFLUENCER DASHBOARD -->
                    <!-- Welcome Section -->
                    <div class="mb-8">
                        <div class="bg-gradient-to-r from-pink-500 to-purple-600 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6 text-white">
                                <h1 class="text-3xl font-bold mb-2">
                                    Hey {{ auth()->user()->name }}! ✨
                                </h1>
                                <p class="text-pink-100 text-lg">
                                    Your influencer profile is live. Time to discover amazing collaboration opportunities!
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Influencer Stats Grid -->
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                        <!-- Active Campaigns -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-pink-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Campaigns</dt>
                                            <dd class="text-lg font-medium text-gray-900 dark:text-white">2</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Applications -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pending Applications</dt>
                                            <dd class="text-lg font-medium text-gray-900 dark:text-white">5</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Earnings -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">This Month</dt>
                                            <dd class="text-lg font-medium text-gray-900 dark:text-white">$1,250</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Views -->
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="p-5">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Profile Views</dt>
                                            <dd class="text-lg font-medium text-gray-900 dark:text-white">89</dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                            <a href="{{ route('search') }}">
                                <flux:button variant="primary" class="flex items-center justify-center space-x-2 h-12 w-full">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                    <span>Find Businesses</span>
                                </flux:button>
                            </a>
                            <flux:button variant="outline" class="flex items-center justify-center space-x-2 h-12">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span>Update Profile</span>
                            </flux:button>
                            <flux:button variant="outline" class="flex items-center justify-center space-x-2 h-12">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span>Media Kit</span>
                            </flux:button>
                            <flux:button variant="outline" class="flex items-center justify-center space-x-2 h-12">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <span>Messages</span>
                            </flux:button>
                        </div>
                    </div>

                    <!-- Available Campaigns & My Applications -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <!-- Available Campaigns -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">New Campaigns for You</h3>
                                <div class="space-y-4">
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Local Coffee Shop Review</h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Brew & Bean Co. • Food & Beverage</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-2">Looking for local food influencers to review our new seasonal menu...</p>
                                                <div class="flex items-center space-x-4 mt-3 text-xs text-gray-500 dark:text-gray-400">
                                                    <span>📍 Downtown</span>
                                                    <span>💰 $200</span>
                                                    <span>📅 Due: Nov 15</span>
                                                </div>
                                            </div>
                                            <flux:button variant="primary" size="sm" class="ml-4">Apply</flux:button>
                                        </div>
                                    </div>
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Fitness Apparel Showcase</h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">ActiveWear Plus • Fashion & Fitness</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-2">Seeking fitness influencers to showcase our new workout collection...</p>
                                                <div class="flex items-center space-x-4 mt-3 text-xs text-gray-500 dark:text-gray-400">
                                                    <span>📍 Remote</span>
                                                    <span>💰 $350 + Product</span>
                                                    <span>📅 Due: Nov 20</span>
                                                </div>
                                            </div>
                                            <flux:button variant="primary" size="sm" class="ml-4">Apply</flux:button>
                                        </div>
                                    </div>
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">Home Decor Collaboration</h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Cozy Home Studio • Home & Lifestyle</p>
                                                <p class="text-xs text-gray-600 dark:text-gray-300 mt-2">Partner with us to create content featuring our handmade decor pieces...</p>
                                                <div class="flex items-center space-x-4 mt-3 text-xs text-gray-500 dark:text-gray-400">
                                                    <span>📍 Local Area</span>
                                                    <span>💰 Product Trade</span>
                                                    <span>📅 Due: Nov 25</span>
                                                </div>
                                            </div>
                                            <flux:button variant="primary" size="sm" class="ml-4">Apply</flux:button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <flux:button variant="subtle" size="sm">View All Campaigns</flux:button>
                                </div>
                            </div>
                        </div>

                        <!-- My Applications -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">My Applications</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Summer Beauty Launch</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Glow Cosmetics • Applied 2 days ago</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                            Under Review
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Tech Gadget Review</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">TechForward • Applied 5 days ago</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            Accepted
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Local Restaurant Feature</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Bistro Downtown • Applied 1 week ago</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">
                                            Declined
                                        </span>
                                    </div>
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">Sustainable Fashion</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">EcoStyle • Applied 1 week ago</p>
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                            In Progress
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <flux:button variant="subtle" size="sm">View All Applications</flux:button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Performance & Profile Stats -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <!-- Performance Overview -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Performance This Month</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Campaigns Completed</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">4</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Average Engagement Rate</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">5.8%</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Reach</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">18.2K</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Client Satisfaction</span>
                                        <div class="flex items-center space-x-1">
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">4.9</span>
                                            <div class="flex space-x-1">
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Completion -->
                        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Profile Completion</h3>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Basic Information</span>
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-green-600">Complete</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Social Media Accounts</span>
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-green-600">Complete</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Media Kit</span>
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-yellow-600">In Progress</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">Portfolio</span>
                                        <div class="flex items-center space-x-2">
                                            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm font-medium text-gray-500">Not Started</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: 75%"></div>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">75% Complete</p>
                                </div>
                            </div>
                        </div>
                    </div>

                @else
                    <!-- DEFAULT DASHBOARD (for undefined or other account types) -->
                    <div class="mb-8">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                            <div class="px-4 py-5 sm:p-6">
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                    Welcome to CollabConnect, {{ auth()->user()->name }}! 🎉
                                </h1>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Great! You're all set up and ready to start collaborating.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Basic Stats -->
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

                    <!-- Get Started -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Get Started</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">
                                Complete your profile setup to unlock all CollabConnect features.
                            </p>
                            <flux:button variant="primary">Complete Setup</flux:button>
                        </div>
                    </div>
                @endif
            </div>
        </main>

    @fluxScripts()
</body>
</html>
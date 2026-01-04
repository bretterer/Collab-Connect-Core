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

        <title>@yield('title', 'CollabConnect')</title>
        <meta name="description" content="@yield('description', 'Connecting local businesses with authentic micro-influencers in Cincinnati and Dayton.')">

        <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
        <link rel="manifest" href="/site.webmanifest" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

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

        {{-- Analytics is loaded dynamically based on cookie consent --}}

        <style>
            /* Modern SaaS Design System */
            :root {
                --primary-50: #eff6ff;
                --primary-100: #dbeafe;
                --primary-500: #3b82f6;
                --primary-600: #2563eb;
                --primary-700: #1d4ed8;
                --primary-900: #1e3a8a;

                --gray-50: #f9fafb;
                --gray-100: #f3f4f6;
                --gray-200: #e5e7eb;
                --gray-300: #d1d5db;
                --gray-400: #9ca3af;
                --gray-500: #6b7280;
                --gray-600: #4b5563;
                --gray-700: #374151;
                --gray-800: #1f2937;
                --gray-900: #111827;

                --success-500: #10b981;
                --warning-500: #f59e0b;
                --error-500: #ef4444;

                --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
                --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
                --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
                --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
                --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);

                --border-radius: 0.5rem;
                --border-radius-lg: 0.75rem;
                --border-radius-xl: 1rem;
            }

            .gradient-primary {
                background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
            }

            .gradient-text {
                background: linear-gradient(135deg, var(--primary-600), var(--primary-700));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .glass-effect {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(12px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            .glass-effect-dark {
                background: rgba(31, 41, 55, 0.8);
                backdrop-filter: blur(12px);
                border: 1px solid rgba(75, 85, 99, 0.2);
            }

            .card-hover {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .card-hover:hover {
                transform: translateY(-4px);
                box-shadow: var(--shadow-xl);
            }

            .btn-primary {
                background: var(--primary-600);
                color: white;
                padding: 0.75rem 1.5rem;
                border-radius: var(--border-radius);
                font-weight: 600;
                box-shadow: var(--shadow-sm);
                transition: all 0.2s ease;
            }

            .btn-primary:hover {
                background: var(--primary-700);
                box-shadow: var(--shadow-md);
            }

            .btn-secondary {
                background: white;
                color: var(--gray-700);
                padding: 0.75rem 1.5rem;
                border-radius: var(--border-radius);
                font-weight: 600;
                border: 1px solid var(--gray-300);
                box-shadow: var(--shadow-sm);
                transition: all 0.2s ease;
            }

            .btn-secondary:hover {
                background: var(--gray-50);
                border-color: var(--gray-400);
            }

            .dark .btn-secondary {
                background: var(--gray-800);
                color: var(--gray-200);
                border-color: var(--gray-600);
            }

            .dark .btn-secondary:hover {
                background: var(--gray-700);
                border-color: var(--gray-500);
            }

            .feature-card {
                background: white;
                border-radius: var(--border-radius-xl);
                padding: 2rem;
                box-shadow: var(--shadow);
                border: 1px solid var(--gray-200);
            }

            .dark .feature-card {
                background: var(--gray-800);
                border-color: var(--gray-700);
            }

            .stat-card {
                text-align: center;
                padding: 1.5rem;
                background: white;
                border-radius: var(--border-radius-lg);
                box-shadow: var(--shadow);
                border: 1px solid var(--gray-200);
            }

            .dark .stat-card {
                background: var(--gray-800);
                border-color: var(--gray-700);
            }

            @keyframes slideUp {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .animate-slide-up {
                animation: slideUp 0.6s ease-out;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            .animate-fade-in {
                animation: fadeIn 0.8s ease-out;
            }

            /* Responsive grid system */
            .container-custom {
                max-width: 1280px;
                margin: 0 auto;
                padding: 0 1rem;
            }

            @media (min-width: 768px) {
                .container-custom {
                    padding: 0 2rem;
                }
            }

            /* Professional navigation */
            .nav-link {
                color: var(--gray-600);
                font-weight: 500;
                transition: color 0.2s ease;
            }

            .nav-link:hover {
                color: var(--primary-600);
            }

            .nav-link.active {
                color: var(--primary-600);
            }
        </style>

        <x-head />

    </head>
    <body class="font-inter bg-white dark:bg-gray-900 min-h-screen">
        <x-body />

        <!-- Modern Professional Header -->
        <header class="bg-white/95 dark:bg-gray-900/95 backdrop-blur-lg border-b border-gray-200 dark:border-gray-800 sticky top-0 z-50" x-data="{ mobileMenuOpen: false }">
            <div class="container-custom">
                <nav class="flex items-center justify-between py-4">
                    <!-- Logo -->
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 gradient-primary rounded-lg flex items-center justify-center">
                            <img src="{{ Vite::asset('resources/images/CollabConnectMark.png') }}" alt="CollabConnect" class="w-6 h-6">
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">Collab Connect</span>
                    </div>

                    <!-- Navigation Links -->
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}">Home</a>
                        @hasSection('header-nav')
                            @yield('header-nav')
                        @else
                            <a href="/about" class="nav-link {{ request()->is('about') ? 'active' : '' }}">About</a>
                            <a href="/contact" class="nav-link {{ request()->is('contact') ? 'active' : '' }}">Contact</a>
                            <a href="/privacy" class="nav-link {{ request()->is('privacy') ? 'active' : '' }}">Privacy</a>
                            <a href="/terms" class="nav-link {{ request()->is('terms') ? 'active' : '' }}">Terms</a>
                        @endif
                    </div>

                    <!-- CTA Buttons - Hidden on mobile -->
                    <div class="hidden md:flex items-center space-x-4">
                        @yield('nav-cta', '<a href="/" class="btn-primary">Join the Beta Crew</a>')
                    </div>

                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors" aria-label="Toggle mobile menu">
                        <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </nav>
            </div>

            <!-- Mobile Navigation Menu -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 transform translate-y-0" x-transition:leave-end="opacity-0 transform -translate-y-2" class="md:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800" @click.away="mobileMenuOpen = false">
                <div class="container-custom">
                    <div class="py-4 space-y-3">
                        <a href="/" @click="mobileMenuOpen = false" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors {{ request()->is('/') ? 'text-blue-600 bg-blue-50 dark:bg-blue-900/20' : '' }}">
                            Home
                        </a>
                        @hasSection('mobile-header-nav')
                            @yield('mobile-header-nav')
                        @elseif($__env->hasSection('header-nav'))
                            <div class="space-y-3">
                                @yield('header-nav')
                            </div>
                        @else
                            <a href="/about" @click="mobileMenuOpen = false" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors {{ request()->is('about') ? 'text-blue-600 bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                About
                            </a>
                            <a href="/contact" @click="mobileMenuOpen = false" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors {{ request()->is('contact') ? 'text-blue-600 bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                Contact
                            </a>
                            <a href="/privacy" @click="mobileMenuOpen = false" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors {{ request()->is('privacy') ? 'text-blue-600 bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                Privacy
                            </a>
                            <a href="/terms" @click="mobileMenuOpen = false" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors {{ request()->is('terms') ? 'text-blue-600 bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                Terms
                            </a>
                        @endif

                        <!-- Divider -->
                        <div class="border-t border-gray-200 dark:border-gray-700 mx-4 my-4"></div>

                        <!-- Mobile CTA Buttons -->
                        <div class="px-4 space-y-3">
                            @if(true === false)
                            <a href="/#beta-signup" @click="mobileMenuOpen = false" class="block w-full text-center bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors font-semibold">
                                Join the Beta Crew
                            </a>
                            @endif
                            @hasSection('mobile-nav-cta')
                                @yield('mobile-nav-cta')
                            @else
                                <a href="{{ route('register') }}" @click="mobileMenuOpen = false" class="button-primary block w-full text-center border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-3 px-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors font-semibold">Sign up</a>
                                <a href="/login" @click="mobileMenuOpen = false" class="block w-full text-center border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 py-3 px-4 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors font-semibold">Sign In</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            @isset($slot)
                {{ $slot }}
            @endisset
            @yield('content')
        </main>

        <!-- Modern Footer -->
        <footer class="bg-gray-900 text-gray-300">
            <div class="container-custom">
                <div class="py-16">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                        <!-- Logo Section -->
                        <div class="col-span-1">
                            <div class="flex items-center space-x-3 mb-6">
                                <div class="w-10 h-10 gradient-primary rounded-lg flex items-center justify-center">
                                    <img src="{{ Vite::asset('resources/images/CollabConnectMark.png') }}" alt="CollabConnect" class="w-6 h-6">
                                </div>
                                <span class="text-xl font-bold text-white">CollabConnect</span>
                            </div>
                            <p class="text-gray-400 leading-relaxed mb-6">
                                Connecting local businesses with micro-influencers.
                            </p>
                            <div class="flex space-x-3">
                                <!-- Facebook -->
                                <a href="https://www.facebook.com/CollabConnect" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors" aria-label="Facebook">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                                <!-- Instagram -->
                                <a href="https://www.instagram.com/collabconnectus" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors" aria-label="Facebook">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                </a>
                                @if(false === true)
                                <!-- X (Twitter) -->
                                <a href="#" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors" aria-label="Facebook">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                    </svg>
                                </a>
                                @endif
                                <!-- BlueSky -->
                                <a href="https://bsky.app/profile/collabconnect.app" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors" aria-label="BlueSky">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 568 501">
                                        <path d="M123.121 33.664C188.241 82.553 258.281 181.68 284 234.873c25.719-53.193 95.759-152.32 160.879-201.209C491.866-1.611 568-28.906 568 57.947c0 17.346-9.945 145.713-15.778 166.063-20.275 70.98-90.515 90.556-125.376 70.225-29.699-17.332-44.823-4.618-44.823 26.808 0 31.426 27.342 42.831 63.725 31.426 36.383-11.405 126.234-23.758 126.234 58.851 0 82.608-89.85 70.98-126.234 58.851-36.383-12.151-63.725-.755-63.725 31.426 0 31.426 15.124 44.14 44.823 26.808 34.861-20.331 105.101-.755 125.376 70.225C557.055 355.34 568 483.707 568 501.053c0 86.853-76.134 59.558-123.121 24.283C388.759 475.445 318.719 376.318 284 323.125c-34.719 53.193-104.759 152.32-160.879 201.209C76.134 560.611 0 587.906 0 501.053c0-17.346 9.945-145.713 15.778-166.063 20.275-70.98 90.515-90.556 125.376-70.225 29.699 17.332 44.823 4.618 44.823-26.808 0-31.426-27.342-42.831-63.725-31.426C85.869 218.936-4.234 231.289-4.234 149.68c0-82.608 90.103-70.256 126.487-58.851 36.383 11.405 63.725.755 63.725-31.426 0-31.426-15.124-44.14-44.823-26.808C106.294 53.926 36.054 34.35 15.778-36.63 9.945-56.98 0-185.347 0-202.693c0-86.853 76.134-59.558 123.121-24.283z"/>
                                    </svg>
                                </a>
                                <!-- LinkedIn -->
                                <a href="https://www.linkedin.com/company/collab-connect-us" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors" aria-label="Facebook">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                                    </svg>
                                </a>
                                <!-- TikTok -->
                                <a href="https://www.tiktok.com/@collab_connect_us" target="_blank" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-blue-600 transition-colors" aria-label="Facebook">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- Product -->
                        <div>
                            <h3 class="text-white font-semibold mb-6">Product</h3>
                            <ul class="space-y-4">
                                <li><a href="/#features" class="hover:text-blue-400 transition-colors">Features</a></li>
                                <li><a href="/#how-it-works" class="hover:text-blue-400 transition-colors">How it Works</a></li>
                                <li><a href="{{ route('register') }}" class="hover:text-blue-400 transition-colors">Register</a></li>
                            </ul>
                        </div>

                        <!-- Company -->
                        <div>
                            <h3 class="text-white font-semibold mb-6">Company</h3>
                            <ul class="space-y-4">
                                <li><a href="/about" class="hover:text-blue-400 transition-colors">About</a></li>
                                <li><a href="/contact" class="hover:text-blue-400 transition-colors">Contact</a></li>
                                <li><a href="/careers" class="hover:text-blue-400 transition-colors">Careers</a></li>
                            </ul>
                        </div>

                        <!-- Legal -->
                        <div>
                            <h3 class="text-white font-semibold mb-6">Legal</h3>
                            <ul class="space-y-4">
                                <li><a href="/privacy" class="hover:text-blue-400 transition-colors">Privacy Policy</a></li>
                                <li><a href="/terms" class="hover:text-blue-400 transition-colors">Terms of Service</a></li>
                                <li><button onclick="window.openCookiePreferences()" class="hover:text-blue-400 transition-colors">Cookie Preferences</button></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="border-t border-gray-800 py-6">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <p class="text-gray-400 text-sm">
                            &copy; {{ date('Y') }} CollabConnect. All rights reserved.
                        </p>
                        <div class="flex items-center space-x-6 mt-4 md:mt-0">
                            <span class="text-sm text-gray-400">Made with ❤️ in Springboro, Ohio</span>
                        </div>
                    </div>
                </div>
            </div>
        </footer>

        <script>
            function smoothScrollTo(elementId) {
                document.getElementById(elementId).scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        </script>

        <!-- Developer Tools Drawer (local only) -->
        @if(app()->environment('local'))
            <livewire:developer-tools-drawer />
        @endif

        @fluxScripts
        @livewireScripts
    </body>
</html>
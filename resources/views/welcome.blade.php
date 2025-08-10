<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CollabConnect - Coming Soon</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])


        <style>
            .gradient-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            .gradient-text {
                background: linear-gradient(135deg, #1E88E5, #42A5F5, #64B5F6);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            .glow-effect {
                box-shadow: 0 0 30px rgba(30, 136, 229, 0.3);
            }
            .floating {
                animation: float 6s ease-in-out infinite;
            }
            @keyframes float {
                0%, 100% { transform: translateY(0px); }
                50% { transform: translateY(-20px); }
            }
            .pulse-slow {
                animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
            }
            @keyframes shimmer {
                0% {
                    transform: translateX(-100%) skewX(-12deg);
                }
                100% {
                    transform: translateX(200%) skewX(-12deg);
                }
            }
            .animate-shimmer {
                animation: shimmer 1.5s ease-in-out;
            }
            
            /* Enhanced visual hierarchy styles */
            .section-divider {
                background: linear-gradient(90deg, transparent, #e0e7ff 20%, #e0e7ff 80%, transparent);
                height: 1px;
            }
            
            .visual-depth {
                background: linear-gradient(135deg, 
                    rgba(255,255,255,0.1) 0%, 
                    rgba(255,255,255,0.05) 50%, 
                    rgba(0,0,0,0.05) 100%
                );
                backdrop-filter: blur(10px);
            }
            
            .text-shadow-sm {
                text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            }
        </style>

        @fluxAppearance()

        @if (!app()->environment('local'))
            <script
                defer
                data-website-id="68953b233e0aad41246ad8b4"
                data-domain="collabconnect.app"
                src="https://datafa.st/js/script.js">
            </script>
        @endif
    </head>
    <body class="font-inter bg-gradient-to-br from-blue-50 via-sky-50 to-cyan-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen">
        <!-- Header -->
        <header class="w-full p-6">
            @if (Route::has('login') && app()->environment('local'))
                <nav class="flex items-center justify-end gap-4 max-w-7xl mx-auto">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-flex items-center px-4 py-2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-blue-200 dark:border-gray-600 rounded-lg text-blue-700 dark:text-blue-400 hover:bg-white dark:hover:bg-gray-700 hover:border-blue-300 dark:hover:border-gray-500 transition-all duration-200 font-medium"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 hover:text-blue-700 dark:hover:text-blue-400 transition-colors duration-200 font-medium"
                        >
                            Sign In
                        </a>

                        @if (Route::has('register') && app()->environment('local'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex items-center px-4 py-2 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-blue-200 dark:border-gray-600 rounded-lg text-blue-700 dark:text-blue-400 hover:bg-white dark:hover:bg-gray-700 hover:border-blue-300 dark:hover:border-gray-500 transition-all duration-200 font-medium"
                            >
                                Get Started
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <!-- Main Content -->
        <main class="flex-1 flex items-center justify-center px-6 py-12">
            <div class="max-w-4xl mx-auto text-center">
                <!-- Hero Section with Visual Elements -->
                <div class="mb-8 relative">
                    <!-- Background decoration with Cincinnati/Dayton elements -->
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="absolute -top-40 -left-40 w-80 h-80 bg-gradient-to-r from-blue-400/20 to-purple-400/20 rounded-full blur-3xl floating"></div>
                        <div class="absolute -top-20 -right-40 w-60 h-60 bg-gradient-to-r from-pink-400/20 to-cyan-400/20 rounded-full blur-3xl floating" style="animation-delay: -3s;"></div>
                        
                        <!-- Ohio state outline -->
                        <div class="absolute top-10 right-10 opacity-5 dark:opacity-10">
                            <svg width="120" height="100" viewBox="0 0 120 100" fill="currentColor" class="text-blue-500">
                                <!-- Simplified Ohio outline -->
                                <path d="M20 30 Q25 15 40 20 Q60 15 80 25 Q95 30 100 45 Q95 65 85 75 Q70 85 50 80 Q30 85 15 70 Q10 50 20 30 Z"/>
                                <!-- Cincinnati marker -->
                                <circle cx="25" cy="70" r="2" fill="currentColor" opacity="0.8"/>
                                <!-- Dayton marker -->
                                <circle cx="35" cy="55" r="2" fill="currentColor" opacity="0.8"/>
                            </svg>
                        </div>
                        
                        <!-- Local landmark icons scattered -->
                        <div class="absolute top-20 left-20 opacity-10 text-blue-400 text-2xl floating">🏛️</div>
                        <div class="absolute bottom-20 right-20 opacity-10 text-purple-400 text-2xl floating" style="animation-delay: -4s;">🎭</div>
                        <div class="absolute top-40 right-40 opacity-10 text-cyan-400 text-xl floating" style="animation-delay: -6s;">🏈</div>
                    </div>
                    
                    <div class="relative z-10">
                        <div class="floating">
                            <div class="w-20 h-20 mx-auto mb-6 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center glow-effect p-3">
                                <img src="{{ Vite::asset('resources/images/CollabConnectMark.png') }}" alt="CollabConnect Logo" class="w-14 h-14">
                            </div>
                        </div>
                        <h1 class="text-5xl md:text-7xl font-bold gradient-text mb-4 text-shadow-sm">
                            CollabConnect
                        </h1>
                        <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 font-medium mb-8 text-shadow-sm">
                            Where Influence Meets Opportunity
                        </p>
                        
                        <!-- Hero Visual Design -->
                        <div class="max-w-5xl mx-auto mb-8">
                            <div class="relative bg-gradient-to-br from-white via-blue-50 to-purple-50 dark:from-gray-800 dark:via-blue-900/20 dark:to-purple-900/20 rounded-3xl p-8 shadow-2xl border border-blue-200/30 dark:border-blue-700/30 overflow-hidden">
                                <!-- Background pattern -->
                                <div class="absolute inset-0 opacity-10">
                                    <svg width="100%" height="100%" viewBox="0 0 100 100" fill="none">
                                        <defs>
                                            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                                                <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
                                            </pattern>
                                        </defs>
                                        <rect width="100%" height="100%" fill="url(#grid)" class="text-blue-400 dark:text-blue-600"/>
                                    </svg>
                                </div>
                                
                                <!-- Split Hero Design -->
                                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                                    <!-- Left side - Business Owner -->
                                    <div class="text-center lg:text-left">
                                        <div class="relative inline-block mb-6">
                                            <!-- Business Owner Avatar -->
                                            <div class="w-32 h-32 mx-auto lg:mx-0 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-2xl flex items-center justify-center shadow-2xl floating">
                                                <div class="w-28 h-28 bg-white dark:bg-gray-700 rounded-xl flex items-center justify-center">
                                                    <svg class="w-16 h-16 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <!-- Business badge -->
                                            <div class="absolute -bottom-2 left-1/2 lg:left-8 transform -translate-x-1/2 lg:translate-x-0 bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                                LOCAL BUSINESS
                                            </div>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">Sarah's Coffee Corner</h3>
                                        <p class="text-gray-600 dark:text-gray-400">Downtown Cincinnati</p>
                                    </div>
                                    
                                    <!-- Connection Arrow -->
                                    <div class="hidden lg:block absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20">
                                        <div class="relative">
                                            <svg class="w-16 h-16 text-purple-500 animate-pulse" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            <div class="absolute -top-6 left-1/2 transform -translate-x-1/2 bg-purple-500 text-white px-2 py-1 rounded text-xs font-bold whitespace-nowrap">
                                                PERFECT MATCH
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Right side - Influencer -->
                                    <div class="text-center lg:text-right">
                                        <div class="relative inline-block mb-6">
                                            <!-- Influencer Avatar -->
                                            <div class="w-32 h-32 mx-auto lg:mx-0 bg-gradient-to-br from-purple-400 to-pink-500 rounded-2xl flex items-center justify-center shadow-2xl floating" style="animation-delay: -2s;">
                                                <div class="w-28 h-28 bg-white dark:bg-gray-700 rounded-xl flex items-center justify-center">
                                                    <svg class="w-16 h-16 text-purple-500" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                                        <circle cx="16" cy="6" r="2" fill="currentColor"/>
                                                        <path d="M20 8c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2z" opacity="0.6"/>
                                                    </svg>
                                                </div>
                                            </div>
                                            <!-- Influencer badge -->
                                            <div class="absolute -bottom-2 right-1/2 lg:right-8 transform translate-x-1/2 lg:translate-x-0 bg-purple-500 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
                                                MICRO-INFLUENCER
                                            </div>
                                        </div>
                                        <h3 class="text-xl font-bold text-gray-800 dark:text-gray-100 mb-2">@CincyFoodie</h3>
                                        <p class="text-gray-600 dark:text-gray-400">15K Local Followers</p>
                                    </div>
                                </div>
                                
                                <!-- Mobile connection indicator -->
                                <div class="lg:hidden flex justify-center my-6">
                                    <svg class="w-8 h-8 text-purple-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                                
                                <!-- Bottom collaboration preview -->
                                <div class="mt-8 pt-6 border-t border-blue-200/30 dark:border-blue-700/30">
                                    <div class="grid grid-cols-3 gap-4">
                                        <div class="bg-green-100 dark:bg-green-900/30 rounded-lg p-3 text-center">
                                            <div class="text-2xl mb-1">📸</div>
                                            <div class="text-xs text-green-700 dark:text-green-400 font-medium">Instagram Post</div>
                                        </div>
                                        <div class="bg-purple-100 dark:bg-purple-900/30 rounded-lg p-3 text-center">
                                            <div class="text-2xl mb-1">🎥</div>
                                            <div class="text-xs text-purple-700 dark:text-purple-400 font-medium">Stories</div>
                                        </div>
                                        <div class="bg-blue-100 dark:bg-blue-900/30 rounded-lg p-3 text-center">
                                            <div class="text-2xl mb-1">⭐</div>
                                            <div class="text-xs text-blue-700 dark:text-blue-400 font-medium">Reviews</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Beta Launch Message with Visual Stats -->
                <div class="mb-16">
                    <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-100 to-sky-100 dark:from-gray-700 dark:to-gray-600 rounded-full mb-6">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 pulse-slow"></span>
                        <span class="text-blue-700 dark:text-blue-300 font-medium">Beta Launch - Cincinnati & Dayton</span>
                    </div>
                    <h2 class="text-3xl md:text-5xl font-bold text-gray-800 dark:text-gray-100 mb-8">
                        Your Bridge to Local Influence
                    </h2>
                    <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed mb-8">
                        We're launching something <span class="font-semibold text-blue-600 dark:text-blue-400">BIG</span> — and it all starts right here in your backyard.
                        Connect with authentic micro-influencers who have real connections in your community.
                    </p>
                    
                    <!-- Quick Stats Visual -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-2xl mx-auto mb-12">
                        <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl p-6 border border-blue-200/50 dark:border-blue-700/30">
                            <div class="text-3xl font-bold text-blue-600 dark:text-blue-400 mb-2">8x</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Higher engagement with micro-influencers</div>
                        </div>
                        <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl p-6 border border-purple-200/50 dark:border-purple-700/30">
                            <div class="text-3xl font-bold text-purple-600 dark:text-purple-400 mb-2">70%</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">More likely to trust local recommendations</div>
                        </div>
                        <div class="bg-white/60 dark:bg-gray-800/60 backdrop-blur-sm rounded-2xl p-6 border border-pink-200/50 dark:border-pink-700/30">
                            <div class="text-3xl font-bold text-pink-600 dark:text-pink-400 mb-2">50%</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Less cost than traditional advertising</div>
                        </div>
                    </div>
                </div>

                <!-- Dual Audience Sections -->
                <div class="mb-20 grid grid-cols-1 lg:grid-cols-2 gap-12 max-w-7xl mx-auto">
                    <!-- For Influencers -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-3xl p-8 border border-purple-100 dark:border-purple-800/20 relative overflow-hidden">
                        <!-- Influencer visual scene -->
                        <div class="absolute top-4 right-4 w-24 h-24">
                            <!-- Phone mockup -->
                            <div class="relative bg-gray-800 rounded-lg p-1 shadow-lg">
                                <div class="bg-white rounded-md p-1">
                                    <!-- Screen with Instagram-like interface -->
                                    <div class="w-20 h-16 bg-gradient-to-b from-purple-400 to-pink-500 rounded-sm relative overflow-hidden">
                                        <!-- Profile pic -->
                                        <div class="absolute top-1 left-1 w-3 h-3 bg-white rounded-full flex items-center justify-center">
                                            <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                                        </div>
                                        <!-- Hearts floating -->
                                        <div class="absolute top-1 right-1 text-red-400 text-xs">❤️</div>
                                        <div class="absolute bottom-1 left-2 text-white text-xs">📍</div>
                                        <!-- Content preview -->
                                        <div class="absolute bottom-0 left-0 right-0 h-4 bg-gradient-to-t from-black/50 to-transparent rounded-b-sm"></div>
                                    </div>
                                </div>
                                <!-- Phone button -->
                                <div class="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-6 h-1 bg-gray-600 rounded-full"></div>
                            </div>
                            <!-- Floating elements -->
                            <div class="absolute -top-2 -left-2 text-xs">💫</div>
                            <div class="absolute -bottom-1 -right-1 text-xs">✨</div>
                        </div>
                        
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">
                                For Influencers
                            </h3>
                            <h4 class="text-lg font-semibold text-purple-600 dark:text-purple-400 mb-4">
                                Be First in Line for Exclusive, Local Business Collabs!
                            </h4>
                        </div>

                        <div class="space-y-4 text-gray-600 dark:text-gray-300 leading-relaxed">
                            <p class="font-medium text-gray-700 dark:text-gray-200">
                                Calling all micro-influencers in Cincinnati & Dayton — this one's for <span class="text-purple-600 dark:text-purple-400 font-bold">YOU</span>.
                            </p>

                            <p>
                                Micro-influencers aren't just trendsetters — you're local powerhouses. Your voice can spark buzz,
                                shift buying habits, and spotlight the hidden gems of your city.
                            </p>

                            <div class="bg-white/60 dark:bg-gray-800/40 rounded-lg p-4 border border-purple-200 dark:border-purple-700/30">
                                <h5 class="font-semibold text-purple-700 dark:text-purple-300 mb-2">Beta Partner Benefits:</h5>
                                <ul class="text-sm space-y-1">
                                    <li>• <strong>FREE</strong> early access during beta testing</li>
                                    <li>• Special discounted rates after launch</li>
                                    <li>• VIP invites and exclusive collaborations</li>
                                    <li>• Shape the platform built specifically for YOU</li>
                                </ul>
                            </div>

                            <p>
                                Maybe you're tired of sending awkward DMs or don't know where to start approaching businesses.
                                Maybe you already promote your favorite local spots and want to make it official.
                                Either way — we'll match you with brands that want your style, voice, and reach.
                            </p>

                            <div class="bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-800/20 dark:to-pink-800/20 rounded-lg p-4">
                                <p class="text-sm font-medium text-purple-800 dark:text-purple-200">
                                    If you've got 1,000–100,000+ followers, love supporting small businesses,
                                    and are ready to turn content into connection — this is your moment.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- For Business -->
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-3xl p-8 border border-blue-100 dark:border-blue-800/20 relative overflow-hidden">
                        <!-- Business visual scene -->
                        <div class="absolute top-4 right-4 w-24 h-24">
                            <!-- Storefront mockup -->
                            <div class="relative">
                                <!-- Building -->
                                <div class="w-24 h-20 bg-gradient-to-b from-blue-400 to-blue-600 rounded-t-lg relative">
                                    <!-- Windows -->
                                    <div class="absolute top-2 left-2 w-4 h-3 bg-yellow-200 rounded-sm"></div>
                                    <div class="absolute top-2 right-2 w-4 h-3 bg-yellow-200 rounded-sm"></div>
                                    <!-- Door -->
                                    <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-6 h-8 bg-white rounded-t-lg">
                                        <div class="absolute right-1 top-2 w-1 h-1 bg-blue-400 rounded-full"></div>
                                    </div>
                                    <!-- Sign -->
                                    <div class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-2 bg-blue-500 text-white px-2 py-1 rounded text-xs font-bold">
                                        OPEN
                                    </div>
                                </div>
                                <!-- Awning -->
                                <div class="absolute bottom-6 left-0 right-0 h-3 bg-gradient-to-r from-cyan-400 to-blue-400 rounded-b-lg"></div>
                                <!-- Growth arrow -->
                                <div class="absolute -top-2 -right-2 text-green-500 text-sm">📈</div>
                                <!-- Customer indicator -->
                                <div class="absolute -bottom-2 -left-2 text-sm">👥</div>
                            </div>
                        </div>
                        
                        <div class="text-center mb-6">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100 mb-2">
                                For Business
                            </h3>
                            <h4 class="text-lg font-semibold text-blue-600 dark:text-blue-400 mb-4">
                                Your brand deserves more than ads — it deserves advocates!
                            </h4>
                        </div>

                        <div class="space-y-4 text-gray-600 dark:text-gray-300 leading-relaxed">
                            <p class="font-medium text-gray-700 dark:text-gray-200">
                                CollabConnect is redefining how small businesses connect with local influence —
                                and we're inviting a select few to be part of the movement.
                            </p>

                            <p>
                                Micro-influencers are one of the most valuable untapped resources for small businesses —
                                and CollabConnect puts that power at your fingertips. These creators have loyal, local followings
                                and real influence in your community.
                            </p>

                            <div class="bg-white/60 dark:bg-gray-800/40 rounded-lg p-4 border border-blue-200 dark:border-blue-700/30">
                                <h5 class="font-semibold text-blue-700 dark:text-blue-300 mb-2">Beta Partner Benefits:</h5>
                                <ul class="text-sm space-y-1">
                                    <li>• <strong>FREE</strong> early access during beta testing</li>
                                    <li>• Special discounted rates after launch</li>
                                    <li>• Front-row seat in shaping the platform</li>
                                    <li>• Vetted, hyper-local micro-influencer matches</li>
                                </ul>
                            </div>

                            <p>
                                We are creating a platform for <span class="font-semibold text-blue-600 dark:text-blue-400">LOCAL</span> influence with real impact —
                                leverage authentic exposure, scroll-stopping content, and genuine engagement that drives results.
                            </p>

                            <div class="bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-800/20 dark:to-cyan-800/20 rounded-lg p-4">
                                <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                    If you're a business owner in Cincinnati or Dayton, join our beta group today
                                    and lead the next wave of local marketing.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Email Signup Form -->
                <div class="max-w-md mx-auto">
                    <div class="relative bg-gradient-to-br from-white via-blue-50/30 to-purple-50/30 dark:from-gray-800 dark:via-blue-900/20 dark:to-purple-900/20 backdrop-blur-sm rounded-3xl p-8 shadow-2xl border-2 border-gradient-to-br from-blue-200/50 via-purple-200/30 to-pink-200/50 dark:from-blue-700/30 dark:via-purple-700/20 dark:to-pink-700/30 ring-4 ring-blue-100/20 dark:ring-blue-400/10">
                        <!-- Decorative elements -->
                        <div class="absolute -top-4 -right-4 w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full opacity-60 floating"></div>
                        <div class="absolute -bottom-3 -left-3 w-6 h-6 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full opacity-40 floating" style="animation-delay: -2s;"></div>
                        <div class="text-center mb-8">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 via-purple-500 to-pink-500 rounded-2xl mb-4 shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 bg-clip-text text-transparent dark:from-blue-400 dark:via-purple-400 dark:to-pink-400 mb-2">Join the Beta Crew</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Be among the first to revolutionize local marketing</p>
                        </div>

                        <!-- Success Message -->
                        <div id="success-message" class="hidden mb-6 p-4 bg-green-100 dark:bg-green-900/30 border border-green-300 dark:border-green-700 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-green-700 dark:text-green-300 font-medium" id="success-text"></span>
                            </div>
                        </div>

                        <form class="space-y-4" method="POST" action="{{ route('waitlist.store') }}" id="waitlist-form">
                            @csrf
                            <div>
                                <input
                                    type="text"
                                    name="name"
                                    placeholder="Your Name"
                                    required
                                    class="w-full px-4 py-3 bg-white/70 dark:bg-gray-700/70 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-gray-100"
                                >
                            </div>
                            <div>
                                <input
                                    type="email"
                                    name="email"
                                    placeholder="Your Email"
                                    required
                                    class="w-full px-4 py-3 bg-white/70 dark:bg-gray-700/70 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-gray-100"
                                >
                            </div>
                            <div>
                                <select
                                    name="user_type"
                                    required
                                    class="w-full px-4 py-3 bg-white/70 dark:bg-gray-700/70 border border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 text-gray-700 dark:text-gray-100"
                                >
                                    <option value="">I'm interested as a...</option>
                                    <option value="business">Business Owner</option>
                                    <option value="influencer">Influencer/Creator</option>
                                </select>
                            </div>
                            <!-- Conditional Fields -->
                            <div id="influencer-fields" class="hidden transition-all duration-300 ease-in-out">
                                <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/10 dark:to-pink-900/10 rounded-lg p-4 border border-purple-200 dark:border-purple-700/30">
                                    <label class="block text-sm font-medium text-purple-700 dark:text-purple-300 mb-2">
                                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Follower Count
                                    </label>
                                    <select
                                        name="follower_count"
                                        class="w-full px-4 py-3 bg-white/90 dark:bg-gray-700/90 border border-purple-300 dark:border-purple-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-all duration-200 text-gray-900 dark:text-gray-100"
                                    >
                                        <option value="">Select your follower range</option>
                                        <option value="1K-5K">1,000 - 5,000 followers</option>
                                        <option value="5K-15K">5,000 - 15,000 followers</option>
                                        <option value="15K-50K">15,000 - 50,000 followers</option>
                                        <option value="50K-100K">50,000 - 100,000 followers</option>
                                        <option value="100K+">100,000+ followers</option>
                                    </select>
                                </div>
                            </div>

                            <div id="business-fields" class="hidden transition-all duration-300 ease-in-out">
                                <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/10 dark:to-cyan-900/10 rounded-lg p-4 border border-blue-200 dark:border-blue-700/30">
                                    <label class="block text-sm font-medium text-blue-700 dark:text-blue-300 mb-2">
                                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Business Name
                                    </label>
                                    <input
                                        type="text"
                                        name="business_name"
                                        placeholder="Your business name"
                                        class="w-full px-4 py-3 bg-white/90 dark:bg-gray-700/90 border border-blue-300 dark:border-blue-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-gray-100"
                                    >
                                </div>
                            </div>
                            <button
                                type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 dark:from-blue-500 dark:via-purple-500 dark:to-pink-500 dark:hover:from-blue-600 dark:hover:via-purple-600 dark:hover:to-pink-600 text-white font-bold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-2xl hover:shadow-3xl ring-2 ring-blue-200/50 hover:ring-purple-300/50 dark:ring-blue-400/20 dark:hover:ring-purple-400/30 relative overflow-hidden group"
                            >
                                <!-- Shimmer effect -->
                                <div class="absolute inset-0 -inset-full h-full w-1/2 z-5 block transform -skew-x-12 bg-gradient-to-r from-transparent to-white/20 to-transparent opacity-0 group-hover:animate-shimmer group-hover:opacity-100 transition-opacity duration-500"></div>
                                <span class="relative flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    🚀 Join the Beta Crew - FREE Access!
                                </span>
                            </button>
                        </form>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                            Get FREE early access during beta + special discounted rates after launch. No spam, ever.
                        </p>
                        
                        <!-- Social proof -->
                        <div class="mt-6 flex items-center justify-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                            <div class="flex items-center">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                                <span>50+ businesses interested</span>
                            </div>
                            <div class="flex items-center">
                                <span class="w-2 h-2 bg-purple-400 rounded-full mr-1"></span>
                                <span>100+ influencers waiting</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Problem Statements & Stories -->
                <div class="mt-20 max-w-6xl mx-auto space-y-16">
                    <!-- Problem Statement -->
                    <div class="text-center">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-gray-100 mb-8">
                            The Problem with Status Quo
                        </h2>
                        <div class="bg-red-50 dark:bg-red-900/20 rounded-2xl p-8 border border-red-200 dark:border-red-800/30 max-w-4xl mx-auto">
                            <p class="text-lg text-gray-700 dark:text-gray-200 leading-relaxed mb-6">
                                Most local businesses struggle to see real results from partnering with influencers who have
                                millions of followers — because their audience is spread all over the country… and they are
                                <span class="font-bold text-red-600 dark:text-red-400">EXPENSIVE</span>.
                            </p>
                            <p class="text-lg text-gray-700 dark:text-gray-200 leading-relaxed">
                                If you own a salon in Dayton or a coffee shop in Cincinnati, reaching thousands of people in
                                California and Maine won't bring customers through your door. Nationwide followings simply don't
                                translate to local sales.
                            </p>
                        </div>
                    </div>

                    <!-- Success Stories -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <!-- Business Owner Story -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-2xl p-8 border border-green-200 dark:border-green-800/30 relative overflow-hidden">
                            <!-- Coffee shop visual scene -->
                            <div class="absolute top-4 right-4 w-20 h-20">
                                <!-- Coffee shop mockup -->
                                <div class="relative">
                                    <!-- Shop building -->
                                    <div class="w-18 h-16 bg-gradient-to-b from-amber-300 to-amber-500 rounded-lg relative shadow-lg">
                                        <!-- Windows -->
                                        <div class="absolute top-2 left-1 w-3 h-3 bg-yellow-100 rounded-sm border border-amber-600"></div>
                                        <div class="absolute top-2 right-1 w-3 h-3 bg-yellow-100 rounded-sm border border-amber-600"></div>
                                        <!-- Door -->
                                        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2 w-4 h-6 bg-green-700 rounded-t">
                                            <div class="absolute right-0.5 top-1 w-0.5 h-0.5 bg-yellow-300 rounded-full"></div>
                                        </div>
                                        <!-- Coffee sign -->
                                        <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 text-xs">☕</div>
                                    </div>
                                    <!-- Awning -->
                                    <div class="absolute bottom-4 left-0 right-0 h-2 bg-green-600 rounded-b shadow-sm"></div>
                                    <!-- Customers waiting -->
                                    <div class="absolute -bottom-2 -left-1 text-xs">👤</div>
                                    <div class="absolute -bottom-2 -right-1 text-xs">👤</div>
                                    <!-- Sales indicator -->
                                    <div class="absolute -top-2 -right-2 text-green-500 text-sm animate-pulse">💰</div>
                                </div>
                            </div>
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-green-800 dark:text-green-200">Coffee Shop Owner Story</h3>
                            </div>
                            <p class="text-gray-700 dark:text-gray-200 leading-relaxed mb-4">
                                Imagine you own a cozy coffee shop in Dayton. You partner with a big-name influencer who boasts
                                millions of followers — but most of them live across the country, nowhere near your shop.
                            </p>
                            <p class="text-gray-700 dark:text-gray-200 leading-relaxed mb-4">
                                The buzz they create online looks impressive, but it doesn't fill your tables or ring your register.
                            </p>
                            <p class="text-gray-700 dark:text-gray-200 leading-relaxed font-medium">
                                Meanwhile, right around the corner, a local micro-influencer with a few thousand loyal followers
                                is sharing your latte art with people who actually walk by every day.
                            </p>
                        </div>

                        <!-- Influencer Story -->
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 dark:from-indigo-900/20 dark:to-purple-900/20 rounded-2xl p-8 border border-indigo-200 dark:border-indigo-800/30 relative overflow-hidden">
                            <!-- Influencer content creation scene -->
                            <div class="absolute top-4 right-4 w-20 h-20">
                                <div class="relative">
                                    <!-- Ring light -->
                                    <div class="w-16 h-16 border-3 border-yellow-300 rounded-full relative bg-yellow-100/30">
                                        <!-- Phone in center -->
                                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-6 h-10 bg-gray-800 rounded-lg">
                                            <div class="w-full h-full bg-gradient-to-br from-purple-400 to-pink-500 rounded-lg scale-90 flex items-center justify-center">
                                                <div class="text-white text-xs">📱</div>
                                            </div>
                                        </div>
                                        <!-- Light effect -->
                                        <div class="absolute inset-0 border-2 border-yellow-200 rounded-full animate-pulse"></div>
                                    </div>
                                    <!-- Coffee cup prop -->
                                    <div class="absolute -bottom-2 -left-2 text-sm">☕</div>
                                    <!-- Engagement indicators -->
                                    <div class="absolute -top-2 -right-1 text-red-400 text-xs animate-bounce">❤️</div>
                                    <div class="absolute -bottom-1 -right-2 text-blue-400 text-xs">👍</div>
                                    <!-- Local tag -->
                                    <div class="absolute -top-1 left-0 bg-indigo-500 text-white text-xs px-1 rounded">📍</div>
                                </div>
                            </div>
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-indigo-500 rounded-full flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold text-indigo-800 dark:text-indigo-200">Micro-Influencer Story</h3>
                            </div>
                            <p class="text-gray-700 dark:text-gray-200 leading-relaxed mb-4">
                                You've built something real. Not millions of passive followers scrolling by—but a tight-knit
                                community that trusts you. Maybe it's 2,000 people in Dayton who know your morning routine includes
                                your favorite iced coffee from a family-owned shop.
                            </p>
                            <p class="text-gray-700 dark:text-gray-200 leading-relaxed mb-4">
                                Big Brands chase mega-influencers, tossing budget at people with shiny numbers but zero connection
                                to your city, your culture, your people. And it stings—because deep down, you know that your
                                recommendation could drive 50 real customers through a local business's doors.
                            </p>
                            <p class="text-indigo-700 dark:text-indigo-300 leading-relaxed font-medium">
                                That's why CollabConnect exists. This isn't about follower count. It's about influence—and you've got it.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Video Demo Section -->
                <div class="mt-20 max-w-4xl mx-auto">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-gray-100 mb-4">
                            See CollabConnect in Action
                        </h2>
                        <p class="text-lg text-gray-600 dark:text-gray-300">
                            Watch how easy it is to connect local businesses with the perfect micro-influencers
                        </p>
                    </div>
                    
                    <!-- Interactive Video Demo -->
                    <div class="relative aspect-video bg-gradient-to-br from-white via-blue-50 to-purple-50 dark:from-gray-800 dark:via-blue-900/20 dark:to-purple-900/20 rounded-3xl border border-blue-200/50 dark:border-blue-700/30 overflow-hidden shadow-2xl group cursor-pointer hover:shadow-3xl transition-all duration-500">
                        <!-- Video mockup background -->
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-900 via-blue-900 to-purple-900 opacity-90"></div>
                        
                        <!-- Mock interface elements -->
                        <div class="relative z-10 h-full flex flex-col">
                            <!-- Video header bar -->
                            <div class="flex items-center justify-between p-4 bg-black/20">
                                <div class="flex items-center space-x-2">
                                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                    <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                </div>
                                <div class="text-white text-sm font-medium">CollabConnect Demo</div>
                                <div class="text-white/70 text-xs">2:15</div>
                            </div>
                            
                            <!-- Main video content area -->
                            <div class="flex-1 flex items-center justify-center relative overflow-hidden">
                                <!-- Background pattern -->
                                <div class="absolute inset-0 opacity-20">
                                    <div class="absolute top-1/4 left-1/4 w-32 h-32 bg-blue-400 rounded-full blur-3xl"></div>
                                    <div class="absolute bottom-1/4 right-1/4 w-24 h-24 bg-purple-400 rounded-full blur-2xl"></div>
                                </div>
                                
                                <!-- Play button and content -->
                                <div class="text-center z-10">
                                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300 group-hover:shadow-blue-500/25">
                                        <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </div>
                                    
                                    <h3 class="text-2xl font-bold text-white mb-3">
                                        See the Magic in Action
                                    </h3>
                                    <p class="text-blue-200 max-w-md mx-auto leading-relaxed mb-4">
                                        Watch how businesses and influencers connect in Cincinnati & Dayton
                                    </p>
                                    
                                    <!-- Features preview -->
                                    <div class="grid grid-cols-3 gap-4 mt-6 max-w-sm mx-auto">
                                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-2 text-center">
                                            <div class="text-lg mb-1">🎯</div>
                                            <div class="text-xs text-blue-200">Smart Matching</div>
                                        </div>
                                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-2 text-center">
                                            <div class="text-lg mb-1">📊</div>
                                            <div class="text-xs text-blue-200">Real Analytics</div>
                                        </div>
                                        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-2 text-center">
                                            <div class="text-lg mb-1">🚀</div>
                                            <div class="text-xs text-blue-200">Easy Setup</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Cincinnati skyline silhouette -->
                                <div class="absolute bottom-0 left-0 right-0 h-24 overflow-hidden">
                                    <svg class="w-full h-full text-white/10" viewBox="0 0 400 100" fill="currentColor">
                                        <!-- Simple city skyline -->
                                        <rect x="0" y="70" width="30" height="30"/>
                                        <rect x="35" y="60" width="25" height="40"/>
                                        <rect x="65" y="75" width="20" height="25"/>
                                        <rect x="90" y="50" width="35" height="50"/>
                                        <rect x="130" y="65" width="30" height="35"/>
                                        <rect x="165" y="40" width="40" height="60"/>
                                        <rect x="210" y="55" width="25" height="45"/>
                                        <rect x="240" y="70" width="30" height="30"/>
                                        <rect x="275" y="45" width="35" height="55"/>
                                        <rect x="315" y="60" width="25" height="40"/>
                                        <rect x="345" y="75" width="20" height="25"/>
                                        <rect x="370" y="65" width="30" height="35"/>
                                    </svg>
                                </div>
                            </div>
                            
                            <!-- Video controls bar -->
                            <div class="p-4 bg-black/30 flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <button class="text-white hover:text-blue-400 transition-colors">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z"/>
                                        </svg>
                                    </button>
                                    <div class="text-white text-sm">0:00 / 2:15</div>
                                </div>
                                <div class="flex-1 mx-4">
                                    <div class="bg-white/20 rounded-full h-1 relative">
                                        <div class="bg-blue-500 h-1 rounded-full w-1/4"></div>
                                    </div>
                                </div>
                                <div class="text-white/70 text-xs">HD</div>
                            </div>
                        </div>
                        
                        <!-- Hover overlay -->
                        <div class="absolute inset-0 bg-blue-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                </div>

                <!-- Features Preview -->
                <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-sky-500 rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Smart Matching</h3>
                        <p class="text-gray-600 dark:text-gray-300">Connect with the perfect local influencers for your brand</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-sky-500 to-cyan-500 rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Flat-Rate Pricing</h3>
                        <p class="text-gray-600 dark:text-gray-300">No commissions, no hidden fees—just simple, transparent pricing</p>
                    </div>
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gradient-to-br from-cyan-500 to-blue-500 rounded-xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-2">Real Analytics</h3>
                        <p class="text-gray-600 dark:text-gray-300">Track your campaigns with detailed insights and reporting</p>
                    </div>
                </div>

                <!-- Industry Examples -->
                <div class="mt-20 max-w-6xl mx-auto">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-gray-100 text-center mb-12">
                        Success Across All Industries
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Nonprofit -->
                        <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-xl p-6 border border-orange-200 dark:border-orange-800/30">
                            <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-orange-800 dark:text-orange-200 mb-2">Nonprofits</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                                Boost attendance, increase donations, and inspire involvement through trusted local voices who genuinely care about your cause.
                            </p>
                        </div>

                        <!-- Jewelry Designer -->
                        <div class="bg-gradient-to-br from-pink-50 to-rose-50 dark:from-pink-900/20 dark:to-rose-900/20 rounded-xl p-6 border border-pink-200 dark:border-pink-800/30">
                            <div class="w-12 h-12 bg-pink-500 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-pink-800 dark:text-pink-200 mb-2">Jewelry Designers</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                                Showcase handcrafted pieces through authentic styling posts and unboxing videos, turning followers into loyal customers.
                            </p>
                        </div>

                        <!-- Energy Drink -->
                        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl p-6 border border-yellow-200 dark:border-yellow-800/30">
                            <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">Energy Drinks</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                                Launch with high-energy posts, challenge videos, and honest reviews from fitness influencers and gamers.
                            </p>
                        </div>

                        <!-- Event Organizer -->
                        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 rounded-xl p-6 border border-purple-200 dark:border-purple-800/30">
                            <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-purple-800 dark:text-purple-200 mb-2">Event Organizers</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                                Pack the house with authentic sneak peeks and behind-the-scenes content from an army of local micro-creators.
                            </p>
                        </div>

                        <!-- Gym/Fitness -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 border border-green-200 dark:border-green-800/30">
                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-2">Fitness Studios</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                                Launch new classes with authentic workout content from micro-influencers who actually train in your community.
                            </p>
                        </div>

                        <!-- Boutique -->
                        <div class="bg-gradient-to-br from-teal-50 to-cyan-50 dark:from-teal-900/20 dark:to-cyan-900/20 rounded-xl p-6 border border-teal-200 dark:border-teal-800/30">
                            <div class="w-12 h-12 bg-teal-500 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-teal-800 dark:text-teal-200 mb-2">Boutique Stores</h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm leading-relaxed">
                                Turn outfits into must-haves with style-savvy creators who showcase your pieces to engaged local followers.
                            </p>
                        </div>
                    </div>

                    <div class="text-center mt-12">
                        <p class="text-lg text-gray-600 dark:text-gray-300 max-w-4xl mx-auto leading-relaxed">
                            From tattoo studios and craft breweries to pet stores and artisanal bakeries — no matter the industry,
                            local businesses can unlock powerful, targeted marketing by connecting with influencers who truly
                            understand and love their city.
                        </p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-8 text-center text-gray-500 dark:text-gray-400">
            <p>&copy; {{ date('Y') }} CollabConnect. All rights reserved.</p>
        </footer>

                <!-- Form handling -->
        <script>
            // Handle dynamic field visibility
            document.addEventListener('DOMContentLoaded', function() {
                const userTypeSelect = document.querySelector('select[name="user_type"]');
                const influencerFields = document.getElementById('influencer-fields');
                const businessFields = document.getElementById('business-fields');
                const followerCountSelect = document.querySelector('select[name="follower_count"]');
                const businessNameInput = document.querySelector('input[name="business_name"]');

                function updateFieldVisibility() {
                    const selectedType = userTypeSelect.value;

                    // Hide both sections first
                    influencerFields.classList.add('hidden');
                    businessFields.classList.add('hidden');

                    // Remove required attributes
                    followerCountSelect.removeAttribute('required');
                    businessNameInput.removeAttribute('required');

                    // Show appropriate section and set required attribute
                    if (selectedType === 'influencer') {
                        influencerFields.classList.remove('hidden');
                        followerCountSelect.setAttribute('required', 'required');
                    } else if (selectedType === 'business') {
                        businessFields.classList.remove('hidden');
                        businessNameInput.setAttribute('required', 'required');
                    }
                }

                // Update visibility when user type changes
                userTypeSelect.addEventListener('change', updateFieldVisibility);

                // Initialize on page load
                updateFieldVisibility();
            });

            document.getElementById('waitlist-form').addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const submitButton = form.querySelector('button[type="submit"]');
                const successMessage = document.getElementById('success-message');
                const successText = document.getElementById('success-text');

                // Disable submit button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="flex items-center justify-center"><svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Joining...</span>';

                // Get form data
                const formData = new FormData(form);

                // Submit form via fetch
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        successText.textContent = data.message;
                        successMessage.classList.remove('hidden');

                        // Hide form
                        form.style.display = 'none';

                        // Hide success message after 10 seconds and show form again
                        setTimeout(() => {
                            successMessage.classList.add('hidden');
                            form.style.display = 'block';
                            form.reset();
                            // Reset field visibility after form reset
                            document.querySelector('select[name="user_type"]').dispatchEvent(new Event('change'));

                            // Reset button state
                            submitButton.disabled = false;
                            submitButton.innerHTML = '<span class="flex items-center justify-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>Join the Beta Crew</span>';
                        }, 10000);
                    } else {
                        throw new Error(data.message || 'Something went wrong');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('There was an error submitting your information. Please try again.');

                    // Reset button state
                    submitButton.disabled = false;
                    submitButton.innerHTML = '<span class="flex items-center justify-center"><svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>Join the Beta Crew</span>';
                });
            });
        </script>

        @fluxScripts()
    </body>
</html>
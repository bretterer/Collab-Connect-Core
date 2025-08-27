@extends('layouts.marketing')

@section('title', 'CollabConnect - Coming Soon')
@section('description', 'The first platform designed specifically for local businesses to collaborate with micro-influencers in their community.')

@section('nav-cta')
@if (Route::has('login') && app()->environment('local'))
    @auth
        <a href="{{ url('/dashboard') }}" class="btn-secondary">
            Dashboard
        </a>
    @else
        <a href="{{ route('login') }}" class="nav-link">
            Sign In
        </a>
        <a href="#beta-signup" class="btn-primary" onclick="smoothScrollTo('beta-signup')">
            Join the Beta Crew
        </a>
    @endauth
@else
    <a href="#beta-signup" class="btn-primary" onclick="smoothScrollTo('beta-signup')">
        Join the Beta Crew
    </a>
@endif
@endsection

@section('header-nav')
    <a href="#features" class="nav-link">Features</a>
    <a href="#how-it-works" class="nav-link">How it Works</a>
    <a href="#beta-signup" class="nav-link">Join the Beta</a>
@endsection

@section('mobile-header-nav')
    <a href="#features" @click="mobileMenuOpen = false" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">Features</a>
    <a href="#how-it-works" @click="mobileMenuOpen = false" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">How it Works</a>
    <a href="#beta-signup" @click="mobileMenuOpen = false" class="block px-4 py-3 text-gray-700 dark:text-gray-300 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-lg transition-colors">Join the Beta</a>
@endsection

@section('content')

            <!-- Hero Section -->
            <section class="relative bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 pt-20 lg:pt-28">
                <div class="container-custom">
                    <div class="max-w-4xl mx-auto text-center animate-slide-up">
                        <!-- Beta Badge -->
                        <div class="inline-flex items-center px-4 py-2 bg-blue-100 dark:bg-blue-900/30 rounded-full text-blue-800 dark:text-blue-300 text-sm font-semibold mb-8">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2 animate-pulse"></span>
                            Now in Beta • Greater Dayton & Cincinnati Area
                        </div>

                        <!-- Main Headline -->
                        <h1 class="text-5xl lg:text-7xl font-bold text-gray-900 dark:text-white mb-6 leading-tight">
                            Connect Local
                            <span class="gradient-text">Influence</span>
                            <br>with Real Impact
                        </h1>

                        <!-- Subheadline -->
                        <p class="text-xl lg:text-2xl text-gray-600 dark:text-gray-300 mb-8 max-w-3xl mx-auto leading-relaxed">
                            The first platform designed specifically for local businesses to collaborate with micro-influencers in their community. No more wasted ad spend on distant audiences.
                        </p>

                        <!-- CTA Section -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center mb-12">
                            <a href="#beta-signup" class="btn-primary text-lg px-8 py-4 rounded-xl scroll-smooth" onclick="smoothScrollTo('beta-signup')">
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                Join the Beta Crew
                            </a>
                            <button class="btn-secondary text-lg px-8 py-4 rounded-xl flex items-center" onclick="playDemo()">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Watch Demo
                            </button>
                        </div>

                        <!-- Social Proof -->
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-8 text-sm text-gray-600 dark:text-gray-400">
                            @if(false === true)
                            <div class="flex items-center">
                                <div class="flex -space-x-2 mr-3">
                                    <div class="w-8 h-8 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                                    <div class="w-8 h-8 bg-gradient-to-r from-green-500 to-blue-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                                    <div class="w-8 h-8 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                                    <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-500 rounded-full border-2 border-white dark:border-gray-800"></div>
                                </div>
                                <span class="font-medium">150+ businesses waitlisted</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="font-medium">No setup fees • Cancel anytime</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Background decoration -->
                <div class="absolute inset-0 overflow-hidden pointer-events-none">
                    <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500/5 rounded-full blur-3xl"></div>
                    <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-purple-500/5 rounded-full blur-3xl"></div>
                </div>
            </section>

            <!-- Stats Section -->
            <section class="py-16 bg-white dark:bg-gray-800">
                <div class="container-custom">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
                        <div class="stat-card card-hover">
                            <div class="text-4xl font-bold text-blue-600 mb-2">8x</div>
                            <div class="text-gray-600 dark:text-gray-400 text-sm">Higher engagement with micro-influencers vs. macro</div>
                        </div>
                        <div class="stat-card card-hover">
                            <div class="text-4xl font-bold text-green-600 mb-2">73%</div>
                            <div class="text-gray-600 dark:text-gray-400 text-sm">More likely to trust local recommendations</div>
                        </div>
                        <div class="stat-card card-hover">
                            <div class="text-4xl font-bold text-purple-600 mb-2">50%</div>
                            <div class="text-gray-600 dark:text-gray-400 text-sm">Lower cost than traditional advertising</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section id="features" class="py-20 bg-gray-50 dark:bg-gray-900">
                <div class="container-custom">
                    <!-- Section Header -->
                    <div class="text-center mb-16">
                        <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                            Everything you need to succeed
                        </h2>
                        <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                            From smart matching to campaign management, CollabConnect provides all the tools you need to run successful local influencer campaigns.
                        </p>
                    </div>

                    <!-- Feature Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Smart Matching -->
                        <div class="feature-card card-hover">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Smart Matching</h3>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                Our algorithm matches businesses with perfect local influencers based on location, niche, audience demographics, and engagement rates.
                            </p>
                        </div>

                        <!-- Campaign Management -->
                        <div class="feature-card card-hover">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Campaign Management</h3>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                Create, manage, and track campaigns from one dashboard. Set budgets, deadlines, and deliverables with complete transparency.
                            </p>
                        </div>

                        <!-- Real Analytics -->
                        <div class="feature-card card-hover">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Real Analytics</h3>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                Track reach, engagement, conversions, and ROI in real-time. Understand which collaborations drive actual business results.
                            </p>
                        </div>

                        <!-- Secure Payments -->
                        <div class="feature-card card-hover">
                            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Secure Payments</h3>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                Automated, secure payment processing with milestone-based releases. Both parties are protected throughout the collaboration.
                            </p>
                        </div>

                        <!-- Content Approval -->
                        <div class="feature-card card-hover">
                            <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Content Approval</h3>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                Review and approve content before it goes live. Maintain brand consistency while giving creators creative freedom.
                            </p>
                        </div>

                        <!-- Local Focus -->
                        <div class="feature-card card-hover">
                            <div class="w-12 h-12 bg-cyan-100 dark:bg-cyan-900/30 rounded-xl flex items-center justify-center mb-6">
                                <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Local Focus</h3>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                Built specifically for local markets. Connect with influencers who actually live and shop in your community.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- How It Works Section -->
            <section id="how-it-works" class="py-20 bg-white dark:bg-gray-800">
                <div class="container-custom">
                    <!-- Section Header -->
                    <div class="text-center mb-16">
                        <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                            How CollabConnect Works
                        </h2>
                        <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                            From discovery to delivery, our platform makes local influencer marketing simple and effective.
                        </p>
                    </div>

                    <!-- Process Steps -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-12 max-w-6xl mx-auto">
                        <!-- Step 1 -->
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                <span class="text-2xl font-bold text-white">1</span>
                            </div>
                            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Create Your Campaign</h3>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                Set your goals, budget, and requirements. Our platform guides you through creating the perfect campaign brief.
                            </p>
                        </div>

                        <!-- Step 2 -->
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                <span class="text-2xl font-bold text-white">2</span>
                            </div>
                            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Get Matched</h3>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                Our AI finds the perfect local micro-influencers for your brand based on location, niche, and audience fit.
                            </p>
                        </div>

                        <!-- Step 3 -->
                        <div class="text-center">
                            <div class="w-20 h-20 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                <span class="text-2xl font-bold text-white">3</span>
                            </div>
                            <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">Launch & Track</h3>
                            <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                Collaborate, approve content, and track real-time results. Watch your local influence grow.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            @if (false === true)
            <!-- Testimonials Section -->
            <section class="py-20 bg-gray-50 dark:bg-gray-900">
                <div class="container-custom">
                    <!-- Section Header -->
                    <div class="text-center mb-16">
                        <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                            Trusted by Local Businesses
                        </h2>
                        <p class="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
                            See how Cincinnati and Dayton businesses are growing with CollabConnect.
                        </p>
                    </div>

                    <!-- Testimonial Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
                        <!-- Testimonial 1 -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700">
                            <div class="mb-6">
                                <div class="flex items-center text-yellow-400 mb-4">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                    "CollabConnect helped us connect with local food bloggers who actually understand our Cincinnati chili. Our foot traffic increased 40% after just one campaign."
                                </p>
                            </div>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold mr-4">
                                    SB
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white">Sarah Baldwin</div>
                                    <div class="text-gray-600 dark:text-gray-400 text-sm">Owner, Skyline Chili Downtown</div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 2 -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700">
                            <div class="mb-6">
                                <div class="flex items-center text-yellow-400 mb-4">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                    "As a micro-influencer in Dayton, CollabConnect opened doors to local businesses I never could have reached. The platform makes collaboration so smooth."
                                </p>
                            </div>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-semibold mr-4">
                                    MM
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white">Maya Martinez</div>
                                    <div class="text-gray-600 dark:text-gray-400 text-sm">@DaytonFoodie, 12K followers</div>
                                </div>
                            </div>
                        </div>

                        <!-- Testimonial 3 -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-200 dark:border-gray-700">
                            <div class="mb-6">
                                <div class="flex items-center text-yellow-400 mb-4">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                                    "Finally, a platform that understands local marketing. The ROI tracking shows exactly which campaigns drive customers through our doors."
                                </p>
                            </div>
                            <div class="flex items-center">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center text-white font-semibold mr-4">
                                    DK
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 dark:text-white">David Kim</div>
                                    <div class="text-gray-600 dark:text-gray-400 text-sm">Marketing Director, Findlay Market</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            @endif

            <!-- CTA Section -->
            <section id="beta-signup" class="py-20 bg-gradient-to-br from-blue-600 to-purple-700">
                <div class="container-custom">
                    <div class="max-w-4xl mx-auto text-center text-white">
                        <h2 class="text-4xl lg:text-5xl font-bold mb-6">
                            Ready to Transform Your Local Marketing?
                        </h2>
                        <p class="text-xl text-blue-100 mb-10 max-w-2xl mx-auto">
                            Join the beta and be among the first Cincinnati & Dayton businesses to experience the power of local micro-influencer marketing.
                        </p>

                        @if(session('success'))
                            <div class="max-w-lg mx-auto mb-8 p-4 bg-green-100 dark:bg-green-900/50 border border-green-200 dark:border-green-800 rounded-xl">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    <p class="text-green-800 dark:text-green-200 font-medium">{{ session('success') }}</p>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="max-w-lg mx-auto mb-8 p-4 bg-red-100 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-xl">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <p class="text-red-800 dark:text-red-200 font-medium">{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="max-w-lg mx-auto mb-8 p-4 bg-red-100 dark:bg-red-900/50 border border-red-200 dark:border-red-800 rounded-xl">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-600 dark:text-red-400 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    <div>
                                        @foreach($errors->all() as $error)
                                            <p class="text-red-800 dark:text-red-200">{{ $error }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form class="max-w-lg mx-auto" method="POST" action="{{ route('waitlist.store') }}" id="beta-signup-form">
                            @csrf
                            <div class="space-y-4 mb-6 text-left">
                                <div class="w-full px-4 py-4 rounded-xl border-0 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-white/25 shadow-lg bg-white dark:bg-gray-800">
                                    <label for="name" class="block text-xs font-medium text-gray-900 dark:text-gray-200">Name</label>
                                    <input type="text" name="name" placeholder="Your Name" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6 dark:bg-transparent dark:text-white dark:placeholder:text-gray-500" required />
                                </div>

                                <div class="w-full px-4 py-4 rounded-xl border-0 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-white/25 shadow-lg bg-white dark:bg-gray-800">
                                    <label for="email" class="block text-xs font-medium text-gray-900 dark:text-gray-200">Email</label>
                                    <input type="email" name="email" placeholder="Your Email" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6 dark:bg-transparent dark:text-white dark:placeholder:text-gray-500" required />
                                </div>

                                <div class="w-full px-4 py-4 rounded-xl border-0 text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:ring-4 focus:ring-white/25 shadow-lg bg-white dark:bg-gray-800">
                                    <label for="referral_code" class="block text-xs font-medium text-gray-900 dark:text-gray-200">Referral Code</label>
                                    <input type="text" name="referral_code" placeholder="Your Referral Code" value="{{ $referralCode ?? '' }}" class="block w-full text-gray-900 placeholder:text-gray-400 focus:outline-none sm:text-sm/6 dark:bg-transparent dark:text-white dark:placeholder:text-gray-500" />
                                </div>

                                <select name="user_type" required id="user-type-select"
                                    class="w-full px-4 py-4 rounded-xl border-0 text-gray-700 dark:text-gray-100 focus:ring-4 focus:ring-white/25 shadow-lg bg-white dark:bg-gray-800">
                                    <option value="">I'm interested as a...</option>
                                    <option value="business">Business Owner</option>
                                    <option value="influencer">Influencer/Creator</option>
                                </select>

                                <!-- Conditional Fields -->
                                <div id="influencer-fields" class="hidden transition-all duration-300 ease-in-out">
                                    <div class="bg-gradient-to-r from-purple-100 to-pink-100 dark:from-purple-900/30 dark:to-pink-900/30 rounded-xl p-4 border border-purple-200/50 dark:border-purple-700/30">
                                        <label class="block text-sm font-medium text-purple-700 dark:text-purple-300 mb-2">
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            </svg>
                                            Follower Count
                                        </label>
                                        <select name="follower_count" id="follower-count-select"
                                            class="w-full px-4 py-3 bg-white/90 dark:bg-gray-700/90 border border-purple-300 dark:border-purple-600 rounded-lg focus:ring-2 focus:ring-purple-500 dark:focus:ring-purple-400 focus:border-transparent transition-all duration-200 text-gray-900 dark:text-gray-100">
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
                                    <div class="bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 rounded-xl p-4 border border-blue-200/50 dark:border-blue-700/30">
                                        <label class="block text-sm font-medium text-blue-700 dark:text-blue-300 mb-2">
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            Business Name
                                        </label>
                                        <input type="text" name="business_name" placeholder="Your business name" id="business-name-input"
                                            class="w-full px-4 py-3 bg-white/90 dark:bg-gray-700/90 border border-blue-300 dark:border-blue-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent transition-all duration-200 placeholder-gray-500 dark:placeholder-gray-400 text-gray-900 dark:text-gray-100">
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="w-full btn-primary bg-white text-blue-600 hover:bg-blue-50 px-8 py-4 rounded-xl font-bold shadow-lg text-lg">
                                🚀 Join the Beta Crew - FREE Access!
                            </button>

                            <p class="text-blue-100 text-sm mt-4">Free during beta • No credit card required • Be among the first 100!</p>
                        </form>
                    </div>
                </div>
            </section>


<script>
            // Smooth scrolling function
            function smoothScrollTo(elementId) {
                document.getElementById(elementId).scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

            // Demo functionality
            function playDemo() {
                alert('Demo video coming soon! Join the beta to be notified when it\'s ready.');
            }

            // Form enhancement and conditional fields
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('beta-signup-form');
                const userTypeSelect = document.getElementById('user-type-select');
                const influencerFields = document.getElementById('influencer-fields');
                const businessFields = document.getElementById('business-fields');
                const followerCountSelect = document.getElementById('follower-count-select');
                const businessNameInput = document.getElementById('business-name-input');

                // Handle conditional field visibility
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
                if (userTypeSelect) {
                    userTypeSelect.addEventListener('change', updateFieldVisibility);
                    // Initialize on page load
                    updateFieldVisibility();
                }

                // Form submission handling
                if (form) {
                    form.addEventListener('submit', function(e) {
                        const button = form.querySelector('button[type="submit"]');
                        button.innerHTML = '⏳ Joining...';
                        button.disabled = true;
                    });
                }

                // Reset form if there's a success message (meaning signup was successful)
                @if(session('success'))
                if (form) {
                    form.reset();
                    updateFieldVisibility(); // Reset conditional fields

                    // Reset button state
                    const button = form.querySelector('button[type="submit"]');
                    if (button) {
                        button.innerHTML = '🚀 Join the Beta Crew - FREE Access!';
                        button.disabled = false;
                    }
                }
                @endif
            });
        </script>
@endsection

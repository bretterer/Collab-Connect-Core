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
        </style>

        @fluxAppearance()
    </head>
    <body class="font-inter bg-gradient-to-br from-blue-50 via-sky-50 to-cyan-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen">
        <!-- Header -->
        <header class="w-full p-6">
            @if (Route::has('login'))
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
                <!-- Logo/Brand -->
                <div class="mb-8">
                    <div class="floating">
                        <div class="w-20 h-20 mx-auto mb-6 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center glow-effect p-3">
                            <img src="{{ Vite::asset('resources/images/CollabConnectMark.png') }}" alt="CollabConnect Logo" class="w-14 h-14">
                        </div>
                    </div>
                    <h1 class="text-5xl md:text-7xl font-bold gradient-text mb-4">
                        CollabConnect
                    </h1>
                    <p class="text-xl md:text-2xl text-gray-600 dark:text-gray-300 font-medium">
                        Where Influence Meets Opportunity
                    </p>
                </div>

                <!-- Coming Soon Message -->
                <div class="mb-12">
                    <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-100 to-sky-100 dark:from-gray-700 dark:to-gray-600 rounded-full mb-6">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 pulse-slow"></span>
                        <span class="text-blue-700 dark:text-blue-300 font-medium">Coming Soon</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-gray-100 mb-6">
                        The Future of Local Influencer Marketing
                    </h2>
                    <p class="text-lg md:text-xl text-gray-600 dark:text-gray-300 max-w-2xl mx-auto leading-relaxed">
                        Connect local businesses with authentic influencers through our simple, flat-rate platform.
                        No commissions, no complexity—just genuine partnerships that drive real results.
                    </p>
                </div>

                <!-- Email Signup Form -->
                <div class="max-w-md mx-auto">
                    <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl p-8 shadow-xl border border-white/20 dark:border-gray-700/20">
                        <h3 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Get Early Access</h3>

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
                            <button
                                type="submit"
                                class="w-full bg-gradient-to-r from-blue-600 to-sky-600 hover:from-blue-700 hover:to-sky-700 dark:from-blue-500 dark:to-sky-500 dark:hover:from-blue-600 dark:hover:to-sky-600 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl"
                            >
                                Join the Waitlist
                            </button>
                        </form>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-4">
                            Be the first to know when we launch. No spam, ever.
                        </p>
                    </div>
                </div>

                <!-- Features Preview -->
                <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto">
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
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-8 text-center text-gray-500 dark:text-gray-400">
            <p>&copy; {{ date('Y') }} CollabConnect. All rights reserved.</p>
        </footer>

                <!-- Form handling -->
        <script>
            document.getElementById('waitlist-form').addEventListener('submit', function(e) {
                e.preventDefault();

                const form = this;
                const submitButton = form.querySelector('button[type="submit"]');
                const successMessage = document.getElementById('success-message');
                const successText = document.getElementById('success-text');

                // Disable submit button and show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = 'Joining...';

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

                            // Reset button state
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'Join the Waitlist';
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
                    submitButton.innerHTML = 'Join the Waitlist';
                });
            });
        </script>

        @fluxScripts()
    </body>
</html>
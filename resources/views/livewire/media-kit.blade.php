<div class="space-y-8 relative">
    <!-- Coming Soon Overlay -->
    <div class="absolute inset-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="text-center p-8 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 max-w-lg mx-4">
            <div class="h-16 w-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Media Kit Generator Coming Soon!</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">We're building a professional media kit generator that will showcase your brand, audience statistics, collaboration history, and rates. Create stunning PDFs to share with potential brand partners.</p>
            
            <!-- Feature Preview List -->
            <div class="text-left bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 mb-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">What's Coming:</h3>
                <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-purple-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Professional branded templates
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-purple-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Automatic analytics integration
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-purple-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Collaboration showcase
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-purple-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        Rate card builder
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-purple-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        One-click PDF download
                    </li>
                </ul>
            </div>
            
            <div class="flex items-center justify-center space-x-2 text-sm text-purple-600 dark:text-purple-400">
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="font-medium">Building Your Personal Brand Kit</span>
            </div>
        </div>
    </div>

    <!-- Blurred Content Below -->
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Media Kit</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Professional brand kit for collaboration opportunities</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Preview Button -->
            <button wire:click="previewMediaKit" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Preview
            </button>
            
            <!-- Download Button -->
            <button wire:click="generateMediaKit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-purple-600 to-pink-600 text-white text-sm font-medium rounded-lg hover:from-purple-700 hover:to-pink-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download PDF
            </button>
        </div>
    </div>

    <!-- Media Kit Preview -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-purple-600 to-pink-600 p-8 text-white">
            <div class="flex items-center space-x-6">
                <div class="h-24 w-24 bg-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold">{{ auth()->user()->name }}</h2>
                    <p class="text-xl opacity-90">{{ auth()->user()->influencerProfile?->primary_niche?->label() ?? 'Lifestyle Influencer' }}</p>
                    <p class="opacity-75 mt-1">{{ auth()->user()->influencerProfile?->primary_zip_code ?? 'Location TBD' }}</p>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="p-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Audience Overview</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">25.4K</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Instagram Followers</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">4.2%</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Engagement Rate</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">18-34</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Primary Age</div>
                </div>
                <div class="text-center p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">68%</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Female Audience</div>
                </div>
            </div>
        </div>

        <!-- Collaboration Examples -->
        <div class="p-8 border-t border-gray-100 dark:border-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Recent Collaborations</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6">
                    <div class="h-8 w-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Fashion Brand X</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Summer collection launch campaign featuring sustainable fashion pieces.</p>
                    <div class="text-xs text-gray-400">25.2K views • 1,240 engagements</div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6">
                    <div class="h-8 w-8 bg-gradient-to-br from-pink-500 to-pink-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Beauty Co.</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Skincare routine series with authentic product reviews and tutorials.</p>
                    <div class="text-xs text-gray-400">31.8K views • 2,156 engagements</div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6">
                    <div class="h-8 w-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Wellness Brand</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">Fitness journey documentation with health and wellness products.</p>
                    <div class="text-xs text-gray-400">42.1K views • 3,204 engagements</div>
                </div>
            </div>
        </div>

        <!-- Rate Card -->
        <div class="p-8 border-t border-gray-100 dark:border-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Content Rates</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl p-4 text-center">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Instagram Post</h4>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mb-1">$450</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Feed post + Stories</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl p-4 text-center">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Instagram Reel</h4>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mb-1">$650</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">15-30 second video</p>
                </div>
                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-4 text-center">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">TikTok Video</h4>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mb-1">$550</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">30-60 second video</p>
                </div>
                <div class="bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/20 dark:to-orange-900/20 rounded-xl p-4 text-center">
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Bundle Deal</h4>
                    <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mb-1">$1,200</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Multi-platform package</p>
                </div>
            </div>
        </div>
    </div>
</div>
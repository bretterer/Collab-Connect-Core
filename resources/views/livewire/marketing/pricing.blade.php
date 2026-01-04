<div class="bg-gradient-to-b from-gray-50 to-white dark:from-gray-900 dark:to-gray-800 min-h-screen">
    <!-- Hero Section -->
    <section class="pt-20 pb-12">
        <div class="container-custom">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-6">
                    Simple, Transparent
                    <span class="gradient-text">Pricing</span>
                </h1>
                <p class="text-xl text-gray-600 dark:text-gray-300 mb-8">
                    Choose the plan that best fits your needs. All plans include a free trial to get started.
                </p>
            </div>
        </div>
    </section>

    <!-- Pricing Tabs -->
    <section class="pb-20">
        <div class="container-custom">
            <!-- Tab Switcher -->
            <div class="flex justify-center mb-12">
                <div class="inline-flex bg-gray-100 dark:bg-gray-800 rounded-xl p-1">

                    <button
                        wire:click="setActiveTab('influencer')"
                        class="px-6 py-3 rounded-lg text-sm font-semibold transition-all {{ $activeTab === 'influencer' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        For Influencers
                    </button>
                    <button
                        wire:click="setActiveTab('business')"
                        class="px-6 py-3 rounded-lg text-sm font-semibold transition-all {{ $activeTab === 'business' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-md' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        For Businesses
                    </button>

                </div>
            </div>

            <!-- Business Pricing -->
            @if($activeTab === 'business')
                @if($this->hasBusinessFeatures && $this->businessPrices->count() > 0)
                    @include('livewire.marketing.partials.pricing-table', [
                        'categories' => $this->businessCategories,
                        'prices' => $this->businessPrices,
                        'highlightedPriceId' => $this->highlightedBusinessPriceId,
                    ])
                @else
                    @include('livewire.marketing.partials.pricing-cards', [
                        'prices' => $this->businessPrices,
                        'highlightedPriceId' => $this->highlightedBusinessPriceId,
                    ])
                @endif
            @endif

            <!-- Influencer Pricing -->
            @if($activeTab === 'influencer')
                @if($this->hasInfluencerFeatures && $this->influencerPrices->count() > 0)
                    @include('livewire.marketing.partials.pricing-table', [
                        'categories' => $this->influencerCategories,
                        'prices' => $this->influencerPrices,
                        'highlightedPriceId' => $this->highlightedInfluencerPriceId,
                    ])
                @else
                    @include('livewire.marketing.partials.pricing-cards', [
                        'prices' => $this->influencerPrices,
                        'highlightedPriceId' => $this->highlightedInfluencerPriceId,
                    ])
                @endif
            @endif

            <!-- No Plans Message -->
            @if(($activeTab === 'business' && $this->businessPrices->isEmpty()) || ($activeTab === 'influencer' && $this->influencerPrices->isEmpty()))
                <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Pricing Coming Soon</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">We're still finalizing our pricing plans. Sign up to be notified when they're available.</p>
                    <a href="{{ route('register') }}" class="btn-primary">
                        Create Free Account
                    </a>
                </div>
            @endif
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-900/50">
        <div class="container-custom">
            <div class="max-w-3xl mx-auto">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white text-center mb-12">
                    Frequently Asked Questions
                </h2>

                <div class="space-y-4">
                    <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                        <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between">
                            <span class="font-semibold text-gray-900 dark:text-white">Can I change my plan later?</span>
                            <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="px-6 pb-4 text-gray-600 dark:text-gray-400">
                            Yes! You can upgrade or downgrade your plan at any time.
                        </div>
                    </div>

                    <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                        <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between">
                            <span class="font-semibold text-gray-900 dark:text-white">Is there a free trial?</span>
                            <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="px-6 pb-4 text-gray-600 dark:text-gray-400">
                            Yes, all paid plans come with a free trial period so you can explore all features before committing.
                        </div>
                    </div>

                    <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                        <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between">
                            <span class="font-semibold text-gray-900 dark:text-white">What payment methods do you accept?</span>
                            <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="px-6 pb-4 text-gray-600 dark:text-gray-400">
                            We accept all major credit cards (Visa, Mastercard, American Express) through our secure payment processor, Stripe.
                        </div>
                    </div>

                    <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                        <button @click="open = !open" class="w-full px-6 py-4 text-left flex items-center justify-between">
                            <span class="font-semibold text-gray-900 dark:text-white">Can I cancel anytime?</span>
                            <svg class="w-5 h-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse class="px-6 pb-4 text-gray-600 dark:text-gray-400">
                            Absolutely. You can cancel your subscription at any time with no questions asked. You'll continue to have access until the end of your billing period.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20">
        <div class="container-custom">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-2xl p-8 lg:p-12 text-center">
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">
                    Ready to grow your local influence?
                </h2>
                <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                    Join hundreds of businesses and influencers already connecting on CollabConnect.
                </p>
                <a href="{{ route('register') }}" class="inline-flex items-center px-8 py-4 bg-white text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition-colors">
                    Get Started Free
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                    </svg>
                </a>
            </div>
        </div>
    </section>
</div>

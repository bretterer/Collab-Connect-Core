<div>
    <div class="text-center mb-8">
        <img class="block h-10 w-auto mx-auto dark:hidden"
             src="{{ Vite::asset('resources/images/CollabConnect.png') }}"
             alt="CollabConnect Logo" />
        <img class="hidden h-10 w-auto mx-auto dark:block"
             src="{{ Vite::asset('resources/images/CollabConnectDark.png') }}"
             alt="CollabConnect Logo" />
        <h2 class="mt-8 text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">Welcome to CollabConnect!</h2>
        <p class="mt-2 text-sm/6 text-gray-500 dark:text-gray-400">
            Let's get your account set up. Are you primarily looking to collaborate as a Business or an Influencer?
        </p>
    </div>

    <div class="space-y-4">
        <!-- Business Option -->
        <div class="relative">
            <button
                type="button"
                wire:click="selectAccountType('business')"
                class="w-full text-left p-6 border-2 rounded-lg transition-all duration-200 hover:border-blue-300 dark:hover:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                    {{ $selectedAccountType === 'business' ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' }}">

                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Business</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Perfect for restaurants, salons, retail stores, and other businesses looking to collaborate with local influencers to increase brand awareness and drive sales.
                        </p>
                    </div>
                    @if($selectedAccountType === 'business')
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    @endif
                </div>
            </button>
        </div>

        <!-- Influencer Option -->
        <div class="relative">
            <button
                type="button"
                wire:click="selectAccountType('influencer')"
                class="w-full text-left p-6 border-2 rounded-lg transition-all duration-200 hover:border-purple-300 dark:hover:border-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                    {{ $selectedAccountType === 'influencer' ? 'border-purple-500 dark:border-purple-400 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800' }}">

                <div class="flex items-start space-x-4">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Influencer/Creator</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Ideal for content creators, social media influencers, and local personalities who want to collaborate with businesses and monetize their audience.
                        </p>
                    </div>
                    @if($selectedAccountType === 'influencer')
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    @endif
                </div>
            </button>
        </div>
    </div>

    @if($selectedAccountType)
        <div class="mt-8">
            <flux:button
                class="w-full"
                type="button"
                variant="primary"
                wire:click="continue">
                Continue Setup
            </flux:button>
        </div>
    @endif

    @error('selectedAccountType')
        <div class="mt-4 text-sm text-red-600 dark:text-red-400">
            {{ $message }}
        </div>
    @enderror
</div>

<div>
    <div>
        <img class="block h-10 w-auto dark:hidden"
            src="{{ Vite::asset('resources/images/CollabConnect.png') }}"
            alt="CollabConnect Logo" />
        <img class="hidden h-10 w-auto dark:block"
            src="{{ Vite::asset('resources/images/CollabConnectDark.png') }}"
            alt="CollabConnect Logo" />
        <h2 class="mt-8 text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">Register for an account</h2>
        <p class="mt-2 text-sm/6 text-gray-500 dark:text-gray-400">
            Already a member?
            <flux:link :href="route('login')"> Sign in</flux:link>
        </p>
    </div>

    <div class="mt-10">
        <div>
            <form class="space-y-6"
                wire:submit.prevent="register"
                method="POST">

                <x-honeypot />

                <!-- Name -->
                <flux:input type="text"
                    wire:model="name"
                    :label="__('Name')"
                    required
                    autofocus
                    autocomplete="name"
                    :placeholder="__('Full name')" />

                <!-- Email Address -->
                <flux:input type="email"
                    wire:model="email"
                    :label="__('Email address')"
                    required
                    autocomplete="email"
                    placeholder="email@example.com" />

                <!-- Password -->
                <flux:input type="password"
                    wire:model="password"
                    :label="__('Password')"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Password')"
                    viewable />

                <!-- Confirm Password -->
                <flux:input type="password"
                    wire:model="password_confirmation"
                    :label="__('Confirm password')"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Confirm password')"
                    viewable />


                <flux:label>Account Type</flux:label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Business Option -->
                    <div class="relative">
                        <button
                            type="button"
                            wire:click="setAccountType('{{ App\Enums\AccountType::BUSINESS->value }}')"
                            class="w-full h-full text-left p-6 border-2 rounded-lg transition-all duration-200 hover:border-blue-300 dark:hover:border-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                        {{ $accountType === App\Enums\AccountType::BUSINESS ? 'border-blue-500 dark:border-blue-400 bg-blue-50 dark:bg-blue-900/20 opacity-100' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 opacity-60' }}">

                            <div class="flex flex-col items-center text-center space-y-4 min-h-[200px] justify-between">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 flex flex-col justify-center">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Business</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Perfect for restaurants, salons, retail stores, and other businesses looking to collaborate with local influencers.
                                    </p>
                                </div>
                                <div class="flex-shrink-0 h-6 flex items-center justify-center">
                                    @if($accountType === App\Enums\AccountType::BUSINESS)
                                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    @endif
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- Influencer Option -->
                    <div class="relative">
                        <button
                            type="button"
                            wire:click="setAccountType('{{ App\Enums\AccountType::INFLUENCER->value }}')"
                            class="w-full h-full text-left p-6 border-2 rounded-lg transition-all duration-200 hover:border-purple-300 dark:hover:border-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500
                        {{ $accountType === App\Enums\AccountType::INFLUENCER ? 'border-purple-500 dark:border-purple-400 bg-purple-50 dark:bg-purple-900/20 opacity-100' : 'border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 opacity-60' }}">

                            <div class="flex flex-col items-center text-center space-y-4 min-h-[200px] justify-between">
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 flex flex-col justify-center">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Influencer/Creator</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Ideal for content creators, social media influencers, and local personalities who want to collaborate with businesses.
                                    </p>
                                </div>
                                <div class="flex-shrink-0 h-6 flex items-center justify-center">
                                    @if($accountType === App\Enums\AccountType::INFLUENCER)
                                    <svg class="w-5 h-5 text-purple-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    @endif
                                </div>
                            </div>
                        </button>
                    </div>
                    <flux:error name="accountType" />
                </div>

                <div>
                    <x-turnstile data-size="flexible" wire:model="cf_turnstile_response" />
                    <flux:error name="cf_turnstile_response" />
                </div>

                <div>
                    <flux:button class="w-full"
                        type="submit"
                        variant="primary">{{ __('Register') }}</flux:button>
                </div>
            </form>

        </div>
    </div>

</div>
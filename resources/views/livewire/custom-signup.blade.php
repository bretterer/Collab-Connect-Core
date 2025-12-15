<div class="min-h-screen">
    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <img class="block h-8 w-auto dark:hidden"
                        src="{{ Vite::asset('resources/images/CollabConnect.png') }}"
                        alt="CollabConnect Logo" />
                    <img class="hidden h-8 w-auto dark:block"
                        src="{{ Vite::asset('resources/images/CollabConnectDark.png') }}"
                        alt="CollabConnect Logo" />
                </div>

                <!-- Dark mode toggle -->
                <button x-on:click="darkMode = !darkMode"
                    class="p-2 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg x-show="!darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                    </svg>
                    <svg x-show="darkMode" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                    </svg>
                </button>
            </div>
        </div>
    </header>

    @if($registrationComplete)
        <!-- Success State -->
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 md:p-12">
                <div class="w-16 h-16 mx-auto bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                @if($marketNotOpen)
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">You're on the list!</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Thank you for signing up! Your market isn't quite open yet, but we've saved your spot.
                        @if($trialDays)
                            You'll receive your {{ $trialDays }}-day trial as soon as your market opens.
                        @endif
                    </p>
                @else
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Welcome aboard!</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">
                        Your registration is complete. We're excited to have you join us!
                        @if($trialDays)
                            Your {{ $trialDays }}-day trial starts now.
                        @endif
                    </p>
                @endif

                <flux:button :href="route('dashboard')" variant="primary" class="w-full sm:w-auto">
                    Go to Dashboard
                </flux:button>
            </div>
        </div>
    @else
        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-start">
                <!-- Left Column: Offer Details -->
                <div class="mb-12 lg:mb-0">
                    <!-- Hero Section -->
                    <div class="mb-8">
                        @if($heroImageUrl)
                            <img src="{{ $heroImageUrl }}" alt="{{ $page->title }}" class="w-full h-64 object-cover rounded-2xl mb-8 shadow-lg">
                        @endif

                        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                            {{ $heroHeadline ?? $page->title }}
                        </h1>

                        @if($heroSubheadline ?? $page->description)
                            <p class="text-lg text-gray-600 dark:text-gray-400">
                                {{ $heroSubheadline ?? $page->description }}
                            </p>
                        @endif
                    </div>

                    <!-- Package Details -->
                    @if($packageName || count($packageBenefits) > 0)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 md:p-8 mb-8">
                            @if($packageName)
                                <div class="flex items-center gap-3 mb-6">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                        </svg>
                                    </div>
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $packageName }}</h2>
                                </div>
                            @endif

                            @if(count($packageBenefits) > 0)
                                <ul class="space-y-4">
                                    @foreach($packageBenefits as $benefit)
                                        <li class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-6 h-6 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mt-0.5">
                                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <span class="text-gray-700 dark:text-gray-300">{{ $benefit }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif

                    <!-- Pricing Info -->
                    @if($oneTimeAmount || $trialDays)
                        <div class="bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl shadow-lg p-6 md:p-8 text-white">
                            <h3 class="text-lg font-semibold mb-4 opacity-90">What You'll Get</h3>

                            @if($oneTimeAmount)
                                <div class="flex items-baseline gap-2 mb-4">
                                    <span class="text-4xl font-bold">${{ number_format($oneTimeAmount / 100, 2) }}</span>
                                    <span class="text-lg opacity-80">one-time</span>
                                </div>
                                @if($oneTimeDescription)
                                    <p class="opacity-90 mb-4">{{ $oneTimeDescription }}</p>
                                @endif
                            @endif

                            @if($trialDays)
                                <div class="flex items-center gap-2 bg-white/10 rounded-lg p-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">{{ $trialDays }}-day free trial included</span>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Right Column: Registration Form -->
                <div class="lg:sticky lg:top-8">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 md:p-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Create Your Account</h2>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">
                            Join as {{ Str::indefArticle('a', $page->account_type->label()) }} and get started today.
                        </p>

                        <form wire:submit="register" class="space-y-5">
                            <x-honeypot />

                            <!-- Name -->
                            <flux:field>
                                <flux:label>Full Name</flux:label>
                                <flux:input
                                    type="text"
                                    wire:model="name"
                                    required
                                    autofocus
                                    autocomplete="name"
                                    placeholder="Your full name"
                                    :disabled="$isProcessing"
                                />
                                <flux:error name="name" />
                            </flux:field>

                            <!-- Email -->
                            <flux:field>
                                <flux:label>Email Address</flux:label>
                                <flux:input
                                    type="email"
                                    wire:model="email"
                                    required
                                    autocomplete="email"
                                    placeholder="you@example.com"
                                    :disabled="$isProcessing"
                                />
                                <flux:error name="email" />
                            </flux:field>

                            <!-- Postal Code -->
                            @if($registrationMarketsEnabled)
                                <flux:field>
                                    <flux:label>Zip Code</flux:label>
                                    <flux:input
                                        type="text"
                                        wire:model="postal_code"
                                        required
                                        autocomplete="postal-code"
                                        placeholder="Enter your zip code"
                                        maxlength="10"
                                        :disabled="$isProcessing"
                                    />
                                    <flux:error name="postal_code" />
                                </flux:field>
                            @endif

                            <!-- Password -->
                            <flux:field>
                                <flux:label>Password</flux:label>
                                <flux:input
                                    type="password"
                                    wire:model="password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Create a password"
                                    viewable
                                    :disabled="$isProcessing"
                                />
                                <flux:error name="password" />
                            </flux:field>

                            <!-- Confirm Password -->
                            <flux:field>
                                <flux:label>Confirm Password</flux:label>
                                <flux:input
                                    type="password"
                                    wire:model="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Confirm your password"
                                    viewable
                                    :disabled="$isProcessing"
                                />
                                <flux:error name="password_confirmation" />
                            </flux:field>

                            <!-- Payment Section -->
                            @if($requiresPayment)
                                <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <livewire:components.stripe-payment-form />
                                </div>
                            @endif

                            <!-- Payment Error Display -->
                            @if($paymentError)
                                <div class="p-3 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                    <div class="flex items-start space-x-2">
                                        <svg class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-red-600 dark:text-red-400">{{ $paymentError }}</span>
                                    </div>
                                </div>
                            @endif

                            <!-- Submit Button -->
                            <div class="pt-2">
                                <flux:button type="submit" variant="primary" class="w-full" :disabled="$isProcessing">
                                    @if($isProcessing)
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Processing...
                                    @else
                                        @if($requiresPayment)
                                            Pay ${{ number_format($oneTimeAmount / 100, 2) }} & {{ $ctaButtonText }}
                                        @else
                                            {{ $ctaButtonText }}
                                        @endif
                                    @endif
                                </flux:button>
                            </div>

                            <!-- Secure Payment Notice -->
                            @if($requiresPayment)
                                <div class="flex items-center justify-center space-x-2 pt-2">
                                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        Secure payment powered by Stripe
                                    </span>
                                </div>
                            @endif

                            <!-- Terms -->
                            <p class="text-xs text-center text-gray-500 dark:text-gray-400">
                                By signing up, you agree to our
                                <a href="#" class="underline hover:text-gray-700 dark:hover:text-gray-300">Terms of Service</a>
                                and
                                <a href="#" class="underline hover:text-gray-700 dark:hover:text-gray-300">Privacy Policy</a>.
                            </p>
                        </form>

                        <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-center text-gray-600 dark:text-gray-400">
                                Already have an account?
                                <a href="{{ route('login') }}" class="font-medium text-purple-600 dark:text-purple-400 hover:underline">
                                    Sign in
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    @endif

    <!-- Footer -->
    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </p>
        </div>
    </footer>
</div>

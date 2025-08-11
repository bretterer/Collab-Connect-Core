<div>
    <div class="text-center mb-8">
        <img class="block h-10 w-auto mx-auto dark:hidden"
             src="{{ Vite::asset('resources/images/CollabConnect.png') }}"
             alt="CollabConnect Logo" />
        <img class="hidden h-10 w-auto mx-auto dark:block"
             src="{{ Vite::asset('resources/images/CollabConnectDark.png') }}"
             alt="CollabConnect Logo" />
        <h2 class="mt-8 text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">Check your email</h2>
        <p class="mt-2 text-sm/6 text-gray-500 dark:text-gray-400">
            We've sent a verification link to your email address
        </p>
    </div>

    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 text-center">
        <div class="flex items-center justify-center mb-4">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/40 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Verify your email to continue</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
            Please check your inbox and click the verification link to activate your account and start building connections.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <flux:button wire:click="sendVerification" variant="outline" size="sm">
                Resend verification email
            </flux:button>
            <flux:button wire:click="logout" variant="ghost" size="sm">
                Logout
            </flux:button>
        </div>
    </div>

</div>

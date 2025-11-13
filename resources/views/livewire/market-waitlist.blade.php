<div>
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img class="mx-auto h-12 w-auto dark:hidden"
                src="{{ Vite::asset('resources/images/CollabConnect.png') }}"
                alt="CollabConnect Logo" />
            <img class="mx-auto h-12 w-auto hidden dark:block"
                src="{{ Vite::asset('resources/images/CollabConnectDark.png') }}"
                alt="CollabConnect Logo" />

            <div class="mt-8 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/30">
                    <svg class="h-6 w-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>

                <h2 class="mt-6 text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
                    We're Not in Your Area... Yet!
                </h2>

                <p class="mt-4 text-base text-gray-600 dark:text-gray-400">
                    Thanks for your interest in CollabConnect! We're currently rolling out to select markets and haven't reached
                    <strong class="text-gray-900 dark:text-white">{{ $waitlistEntry->postal_code ?? $user->postal_code }}</strong> yet.
                </p>
            </div>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white dark:bg-gray-800 px-6 py-8 shadow sm:rounded-lg">
                <div class="space-y-6">
                    <flux:callout variant="info" icon="information-circle">
                        <strong>Good news!</strong> You're already on our waitlist. We'll notify you via email as soon as we launch in your area.
                    </flux:callout>

                    <div class="rounded-lg bg-gray-50 dark:bg-gray-900/50 p-6">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                            What happens next?
                        </h3>

                        <ul class="space-y-3">
                            <li class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    We're monitoring demand in your area
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    You'll get early access when we launch in <strong>{{ $waitlistEntry->postal_code ?? $user->postal_code }}</strong>
                                </span>
                            </li>
                            <li class="flex gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    We'll send you an email at <strong>{{ $user->email }}</strong>
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                            Registered on {{ $waitlistEntry?->created_at?->format('F j, Y') ?? now()->format('F j, Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Want to help us launch in your area faster? Share CollabConnect with local businesses and influencers!
                </p>
            </div>
        </div>
    </div>
</div>

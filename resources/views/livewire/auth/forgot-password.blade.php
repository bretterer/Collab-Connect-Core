<div>
    <div>
        <img class="block h-10 w-auto dark:hidden"
             src="{{ Vite::asset('resources/images/CollabConnectLogo.png') }}"
             alt="CollabConnect Logo" />
        <img class="hidden h-10 w-auto dark:block"
             src="{{ Vite::asset('resources/images/CollabConnectLogoDark.png') }}"
             alt="CollabConnect Logo" />
        <h2 class="mt-8 text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">Forgot your password?</h2>
        <p class="mt-2 text-sm/6 text-gray-500 dark:text-gray-400">
            No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.
        </p>
    </div>

    <div class="mt-10">
        <div>
            @if ($status)
                <div class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ $status }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form class="space-y-6"
                  wire:submit.prevent="sendResetLink"
                  method="POST">

                <flux:field>
                    <flux:label class="block text-sm/6 font-medium">Email address</flux:label>
                    <flux:input type="email"
                                wire:model="email"
                                autofocus />
                    <flux:error name="email" />
                </flux:field>

                <div>
                    <flux:button class="w-full"
                                 type="submit"
                                 variant="primary">{{ __('Email Password Reset Link') }}</flux:button>
                </div>
            </form>

            <div class="mt-6 text-center">
                <flux:link class="text-sm font-semibold"
                           :href="route('login')"
                           wire:navigate>
                    {{ __('Back to login') }}
                </flux:link>
            </div>
        </div>
    </div>
</div>
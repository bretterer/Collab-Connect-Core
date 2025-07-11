<div>
    <div>
        <img class="block h-10 w-auto dark:hidden"
             src="{{ Vite::asset('resources/images/CollabConnectLogo.png') }}"
             alt="CollabConnect Logo" />
        <img class="hidden h-10 w-auto dark:block"
             src="{{ Vite::asset('resources/images/CollabConnectLogoDark.png') }}"
             alt="CollabConnect Logo" />
        <h2 class="mt-8 text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">Reset your password</h2>
        <p class="mt-2 text-sm/6 text-gray-500 dark:text-gray-400">
            Enter your email address and new password below.
        </p>
    </div>

    <div class="mt-10">
        <div>
            <form class="space-y-6"
                  wire:submit.prevent="resetPassword"
                  method="POST">

                <!-- Email Address -->
                <flux:field>
                    <flux:label class="block text-sm/6 font-medium">Email address</flux:label>
                    <flux:input type="email"
                                wire:model="email"
                                autofocus
                                autocomplete="email" />
                    <flux:error name="email" />
                </flux:field>

                <!-- Password -->
                <flux:field>
                    <flux:label class="block text-sm/6 font-medium">Password</flux:label>
                    <flux:input type="password"
                                wire:model="password"
                                autocomplete="new-password"
                                viewable />
                    <flux:error name="password" />
                </flux:field>

                <!-- Confirm Password -->
                <flux:field>
                    <flux:label class="block text-sm/6 font-medium">Confirm Password</flux:label>
                    <flux:input type="password"
                                wire:model="password_confirmation"
                                autocomplete="new-password"
                                viewable />
                    <flux:error name="password_confirmation" />
                </flux:field>

                <div>
                    <flux:button class="w-full"
                                 type="submit"
                                 variant="primary">{{ __('Reset Password') }}</flux:button>
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
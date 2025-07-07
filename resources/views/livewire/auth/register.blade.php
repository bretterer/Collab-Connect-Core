<div>
    <div>
        <img class="block h-10 w-auto dark:hidden"
             src="{{ Vite::asset('resources/images/CollabConnectLogo.png') }}"
             alt="CollabConnect Logo" />
        <img class="hidden h-10 w-auto dark:block"
             src="{{ Vite::asset('resources/images/CollabConnectLogoDark.png') }}"
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

                <div>
                    <flux:button class="w-full"
                                 type="submit"
                                 variant="primary">{{ __('Register') }}</flux:button>
                </div>
            </form>

        </div>
    </div>

</div>

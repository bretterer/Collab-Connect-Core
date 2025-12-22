<div x-data="{ isOpen: false }">
    <!-- Half-circle trigger on right edge -->
    <div class="fixed right-0 top-1/2 -translate-y-1/2 z-50">
        <button
            x-on:click="isOpen = true; $flux.modal('developer-tools').show()"
            x-show="!isOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="translate-x-full opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0 opacity-100"
            x-transition:leave-end="translate-x-full opacity-0"
            class="flex items-center justify-center w-6 h-16 bg-amber-500 hover:bg-amber-600 text-white rounded-l-full shadow-lg hover:shadow-xl transition-all duration-200 hover:w-8 group"
            title="Developer Tools"
        >
            <svg class="w-4 h-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
            </svg>
        </button>
    </div>

    <!-- Flyout Modal -->
    <flux:modal
        name="developer-tools"
        variant="flyout"
        position="right"
        class="w-96"
        x-on:close="isOpen = false"
    >
        <div class="space-y-6">
            <div>
                <flux:heading size="xl">Developer Tools</flux:heading>
                <flux:text class="mt-2">Helpers and shortcuts for development.</flux:text>
            </div>

            <flux:separator />

            <!-- Quick Login Links -->
            <div>
                <flux:heading size="sm" class="mb-3">Quick Login</flux:heading>
                <div class="space-y-2">
                    <x-login-link
                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-white hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-md transition-colors"
                        :email="config('collabconnect.init_user_email')"
                        label="Login as Admin"
                        redirect-url="{{ route('dashboard') }}"
                    />
                    <x-login-link
                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-white hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-md transition-colors"
                        :email="config('collabconnect.init_business_email')"
                        label="Login as Business"
                        redirect-url="{{ route('dashboard') }}"
                    />
                    <x-login-link
                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-white hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-md transition-colors"
                        :email="config('collabconnect.init_influencer_email')"
                        label="Login as Influencer"
                        redirect-url="{{ route('dashboard') }}"
                    />
                </div>
            </div>

            <flux:separator />

            <!-- Environment Info -->
            <div>
                <flux:heading size="sm" class="mb-3">Environment</flux:heading>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">App Env</span>
                        <span class="font-mono text-zinc-800 dark:text-zinc-200">{{ app()->environment() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">Debug</span>
                        <span class="font-mono text-zinc-800 dark:text-zinc-200">{{ config('app.debug') ? 'true' : 'false' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">PHP</span>
                        <span class="font-mono text-zinc-800 dark:text-zinc-200">{{ PHP_VERSION }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-zinc-500 dark:text-zinc-400">Laravel</span>
                        <span class="font-mono text-zinc-800 dark:text-zinc-200">{{ app()->version() }}</span>
                    </div>
                </div>
            </div>

            <flux:separator />

            <!-- Useful Links -->
            <div>
                <flux:heading size="sm" class="mb-3">Quick Links</flux:heading>
                <div class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-3 py-2 text-sm bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-white hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-md transition-colors">
                        <flux:icon name="shield-check" class="w-4 h-4" />
                        Admin Dashboard
                    </a>
                    <a href="/horizon" target="_blank" class="flex items-center gap-2 px-3 py-2 text-sm bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-white hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-md transition-colors">
                        <flux:icon name="queue-list" class="w-4 h-4" />
                        Horizon
                    </a>
                    <a href="/telescope" target="_blank" class="flex items-center gap-2 px-3 py-2 text-sm bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-white hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-md transition-colors">
                        <flux:icon name="magnifying-glass" class="w-4 h-4" />
                        Telescope
                    </a>
                </div>
            </div>

            <flux:separator />

            <!-- Actions -->
            <div>
                <flux:heading size="sm" class="mb-3">Actions</flux:heading>
                <div class="space-y-2">
                    <button
                        wire:click="$dispatch('reset-cookie-consent')"
                        class="flex w-full items-center gap-2 px-3 py-2 text-sm bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-white hover:bg-zinc-200 dark:hover:bg-zinc-700 rounded-md transition-colors"
                    >
                        <flux:icon name="trash" class="w-4 h-4" />
                        Clear Consent Cookies
                    </button>
                </div>
            </div>

            <flux:separator />

            <!-- Current User Info -->
            @auth
                <div>
                    <flux:heading size="sm" class="mb-3">Current User</flux:heading>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-zinc-500 dark:text-zinc-400">ID</span>
                            <span class="font-mono text-zinc-800 dark:text-zinc-200">{{ auth()->id() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500 dark:text-zinc-400">Email</span>
                            <span class="font-mono text-zinc-800 dark:text-zinc-200 text-xs truncate max-w-48">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500 dark:text-zinc-400">Type</span>
                            <span class="font-mono text-zinc-800 dark:text-zinc-200">{{ auth()->user()->account_type?->label() ?? 'None' }}</span>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </flux:modal>
</div>

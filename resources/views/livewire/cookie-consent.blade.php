<div>
    @if($showBanner)
    <div class="fixed inset-x-0 bottom-0 z-50">
        <div class="border-t border-zinc-700 bg-zinc-900 shadow-2xl dark:bg-zinc-950">
            <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 text-amber-400">
                            <svg class="h-6 w-6"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="1.5"
                                 stroke="currentColor">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div>
                            <flux:heading class="text-white"
                                          size="sm">Cookie Preferences</flux:heading>
                            <flux:text class="mt-1 text-zinc-300"
                                       size="sm">
                                We use cookies to improve your experience and analyze site traffic. You can choose which cookies to allow.
                            </flux:text>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-2 sm:shrink-0">
                        <flux:button class="text-zinc-300 hover:text-white"
                                     variant="ghost"
                                     size="sm"
                                     wire:click="denyAll">
                            Deny All
                        </flux:button>
                        <flux:modal.trigger name="cookie-preferences">
                            <flux:button class="text-zinc-300 hover:text-white"
                                         variant="ghost"
                                         size="sm">
                                Manage
                            </flux:button>
                        </flux:modal.trigger>
                        <flux:button variant="primary"
                                     size="sm"
                                     wire:click="acceptAll">
                            Accept All
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>

        <flux:modal class="max-w-md"
                    name="cookie-preferences">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Cookie Preferences</flux:heading>
                    <flux:text class="mt-2">
                        Choose which cookies you want to allow. Your preferences will be saved for future visits.
                    </flux:text>
                </div>

                <div class="space-y-4">
                    {{-- Essential Cookies --}}
                    <div class="flex items-start justify-between gap-4 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800/50">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <flux:heading size="sm">Essential</flux:heading>
                                <flux:badge size="sm"
                                            color="zinc">Required</flux:badge>
                            </div>
                            <flux:text class="mt-1"
                                       size="sm">
                                Required for the website to function properly. These cannot be disabled.
                            </flux:text>
                        </div>
                        <flux:switch wire:model="essentialEnabled" disabled />
                    </div>

                    {{-- Analytics Cookies --}}
                    <div class="flex items-start justify-between gap-4 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800/50">
                        <div class="flex-1">
                            <flux:heading size="sm">Analytics</flux:heading>
                            <flux:text class="mt-1"
                                       size="sm">
                                Help us understand how visitors interact with our site to improve the experience.
                            </flux:text>
                        </div>
                        <flux:switch wire:model.live="analyticsEnabled" />
                    </div>

                    {{-- Marketing Cookies --}}
                    <div class="flex items-start justify-between gap-4 rounded-lg bg-zinc-50 p-4 dark:bg-zinc-800/50">
                        <div class="flex-1">
                            <flux:heading size="sm">Marketing</flux:heading>
                            <flux:text class="mt-1"
                                       size="sm">
                                Used to deliver relevant ads and track campaign performance.
                            </flux:text>
                        </div>
                        <flux:switch wire:model.live="marketingEnabled" />
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a class="text-sm text-zinc-500 transition-colors hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300"
                       href="{{ route('privacy') }}">
                        Privacy Policy
                    </a>
                    <div class="flex items-center gap-2">
                        <flux:button variant="ghost"
                                     wire:click="denyAll">
                            Deny All
                        </flux:button>
                        <flux:button variant="primary"
                                     wire:click="savePreferences">
                            Save Preferences
                        </flux:button>
                    </div>
                </div>
            </div>
        </flux:modal>
    </div>
    @endif
</div>

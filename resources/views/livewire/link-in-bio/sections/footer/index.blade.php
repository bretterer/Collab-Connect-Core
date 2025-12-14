<flux:card class="p-0 overflow-hidden">
    <flux:accordion transition>
        <flux:accordion.item>
            <flux:accordion.heading class="py-4 px-4">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-3">
                        <flux:icon name="window" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                        <span class="font-semibold text-gray-900 dark:text-white">Footer</span>
                    </div>
                    <flux:switch disabled wire:model.live="enabled" wire:click.stop />
                </div>
            </flux:accordion.heading>
            <flux:accordion.content>
                <div class="space-y-6 p-4 pt-0">
                    {{-- Currently no settings for footer --}}
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        The footer displays branding for {{ config('app.name') }}. There are currently no customizable settings for this section.
                    </p>
                </div>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:card>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <flux:heading size="xl" class="mb-2">Market Registration Settings</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400">Configure market-based registration features for the platform.</flux:text>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
        <div class="p-6 space-y-6">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <flux:heading size="base" class="mb-1">Enable Market-Based Registration</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400">
                        When enabled, new users will be required to select a market during registration. If disabled, users can register without selecting a market.
                    </flux:text>
                </div>
                <div class="ml-6">
                    <flux:switch wire:model.live="enabled" />
                </div>
            </div>

            <flux:separator />

            <div class="flex gap-2">
                <flux:button wire:click="save" variant="primary">
                    Save Settings
                </flux:button>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <flux:callout variant="info" class="text-gray-900 dark:text-gray-100">
            <strong>Note:</strong> Changes to market registration settings will affect all new user registrations immediately after saving.
        </flux:callout>
    </div>
</div>

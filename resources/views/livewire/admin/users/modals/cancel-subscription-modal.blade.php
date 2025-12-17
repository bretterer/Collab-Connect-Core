<flux:modal name="cancel-subscription-modal" class="max-w-lg">
    <div class="p-6">
        <flux:heading size="xl" class="mb-2">Cancel Subscription</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
            Choose how to cancel this subscription.
        </flux:text>

        <div class="space-y-4">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <flux:icon name="exclamation-triangle" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5" />
                    <div>
                        <flux:text class="text-sm text-yellow-700 dark:text-yellow-300">
                            Canceling at end of period allows the user to keep access until their current billing cycle ends.
                            Immediate cancellation will revoke access right away.
                        </flux:text>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:modal.close>
                <flux:button variant="ghost">Keep Subscription</flux:button>
            </flux:modal.close>
            <flux:button wire:click="cancelSubscription" variant="filled" wire:loading.attr="disabled">
                Cancel at End of Period
            </flux:button>
            <flux:button wire:click="cancelSubscriptionImmediately" variant="danger" wire:loading.attr="disabled">
                Cancel Immediately
            </flux:button>
        </div>
    </div>
</flux:modal>

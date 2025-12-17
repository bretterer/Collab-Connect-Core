<flux:modal name="cancel-trial-modal" class="max-w-lg">
    <div class="p-6">
        <flux:heading size="xl" class="mb-2">End Trial Early</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
            Choose what happens when you end this user's trial period.
        </flux:text>

        <div class="space-y-4">
            <flux:field>
                <flux:label>After Trial Ends</flux:label>
                <flux:radio.group wire:model="cancelTrialAction">
                    <flux:radio value="convert_to_paid" label="Convert to Paid Subscription" description="Immediately start billing the user on their selected plan." />
                    <flux:radio value="no_subscription" label="Cancel Subscription" description="End the trial without starting a paid subscription." />
                </flux:radio.group>
            </flux:field>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="cancelTrial" variant="danger" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="cancelTrial">End Trial</span>
                <span wire:loading wire:target="cancelTrial">Processing...</span>
            </flux:button>
        </div>
    </div>
</flux:modal>

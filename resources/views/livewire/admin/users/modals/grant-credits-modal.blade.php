<flux:modal name="grant-credits-modal" class="max-w-lg">
    <div class="p-6">
        <flux:heading size="xl" class="mb-2">Grant Promotion Credits</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
            Add promotion credits to this user's account. Each credit allows the user to promote their profile for 7 days.
        </flux:text>

        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <flux:text class="text-sm text-gray-500 dark:text-gray-400">Current Credits</flux:text>
                <flux:text class="font-semibold text-gray-900 dark:text-white">{{ $this->currentCredits }}</flux:text>
            </div>
        </div>

        <div class="space-y-4">
            <flux:field>
                <flux:label>Credits to Grant</flux:label>
                <flux:input type="number" wire:model="credits" min="1" max="100" />
                @error('credits')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
                <flux:description>
                    Number of promotion credits to add (1-100).
                </flux:description>
            </flux:field>

            <flux:field>
                <flux:label>Reason</flux:label>
                <flux:textarea wire:model="reason" rows="3" placeholder="Enter the reason for granting credits..." />
                @error('reason')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
                <flux:description>
                    This reason will be recorded in the audit log.
                </flux:description>
            </flux:field>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="grantCredits" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="grantCredits">Grant Credits</span>
                <span wire:loading wire:target="grantCredits">Granting...</span>
            </flux:button>
        </div>
    </div>
</flux:modal>

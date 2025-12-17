<flux:modal name="revoke-credits-modal" class="max-w-lg">
    <div class="p-6">
        <flux:heading size="xl" class="mb-2">Revoke Promotion Credits</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
            Remove promotion credits from this user's account.
        </flux:text>

        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4 mb-6">
            <div class="flex items-center justify-between">
                <flux:text class="text-sm text-gray-500 dark:text-gray-400">Current Credits</flux:text>
                <flux:text class="font-semibold text-gray-900 dark:text-white">{{ $this->currentCredits }}</flux:text>
            </div>
        </div>

        @if($this->currentCredits > 0)
            <div class="space-y-4">
                <flux:field>
                    <flux:label>Credits to Revoke</flux:label>
                    <flux:input type="number" wire:model="credits" min="1" max="{{ $this->maxRevokable }}" />
                    @error('credits')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                    <flux:description>
                        Number of promotion credits to remove (max: {{ $this->maxRevokable }}).
                    </flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>Reason</flux:label>
                    <flux:textarea wire:model="reason" rows="3" placeholder="Enter the reason for revoking credits..." />
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
                <flux:button wire:click="revokeCredits" variant="danger" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="revokeCredits">Revoke Credits</span>
                    <span wire:loading wire:target="revokeCredits">Revoking...</span>
                </flux:button>
            </div>
        @else
            <div class="text-center py-4">
                <flux:icon name="exclamation-circle" class="w-10 h-10 mx-auto text-gray-400 mb-3" />
                <flux:text class="text-gray-500 dark:text-gray-400">This user has no credits to revoke.</flux:text>
            </div>
            <div class="flex justify-end mt-6">
                <flux:modal.close>
                    <flux:button variant="ghost">Close</flux:button>
                </flux:modal.close>
            </div>
        @endif
    </div>
</flux:modal>

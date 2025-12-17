<flux:modal name="extend-trial-modal" class="max-w-lg">
    <div class="p-6">
        <flux:heading size="xl" class="mb-2">Add Trial Days</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
            Add trial days to this subscription. The user will not be charged until the trial ends.
        </flux:text>

        <div class="space-y-4">
            @if($this->currentTrialEnd)
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <flux:icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                        <div>
                            <flux:text class="text-sm text-blue-700 dark:text-blue-300">
                                Current trial ends: <strong>{{ $this->currentTrialEnd }}</strong>
                            </flux:text>
                        </div>
                    </div>
                </div>
            @endif

            <flux:field>
                <flux:label>Trial Days to Add</flux:label>
                <flux:input type="number" wire:model="trialDays" min="1" max="365" />
                @error('trialDays')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
                <flux:description>
                    The trial will be extended from today by this many days (1-365).
                </flux:description>
            </flux:field>

            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <flux:icon name="calendar" class="w-5 h-5 text-gray-400" />
                    <div>
                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">
                            New trial end date: <strong>{{ now()->addDays($trialDays)->format('F j, Y') }}</strong>
                        </flux:text>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="extendTrial" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="extendTrial">Add Trial Days</span>
                <span wire:loading wire:target="extendTrial">Processing...</span>
            </flux:button>
        </div>
    </div>
</flux:modal>

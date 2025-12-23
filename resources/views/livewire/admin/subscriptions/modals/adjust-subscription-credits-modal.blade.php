<flux:modal name="adjust-subscription-credits-modal" class="max-w-md">
    <div class="space-y-6">
        <div>
            <flux:heading size="xl">Adjust Subscription Credits</flux:heading>
            <flux:text class="mt-2">
                Modify the {{ $this->creditLabel }} for this account.
            </flux:text>
        </div>

        @if($this->billable)
            <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4 space-y-3">
                <div class="flex justify-between items-center">
                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Credit Type</flux:text>
                    <flux:text class="font-medium">{{ $this->creditLabel }}</flux:text>
                </div>
                <div class="flex justify-between items-center">
                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Current Value</flux:text>
                    <flux:badge color="{{ $this->currentCredits > 0 ? 'green' : 'zinc' }}">
                        {{ $this->currentCredits }}
                    </flux:badge>
                </div>
                <div class="flex justify-between items-center">
                    <flux:text class="text-sm text-zinc-500 dark:text-zinc-400">Plan Limit</flux:text>
                    <flux:badge color="blue">
                        @if($this->isUnlimited)
                            Unlimited
                        @else
                            {{ $this->planLimit }}
                        @endif
                    </flux:badge>
                </div>
            </div>

            @if($this->isUnlimited)
                <flux:callout variant="warning" icon="exclamation-triangle">
                    <flux:callout.text>
                        This account has unlimited credits for this feature. Adjustments are not necessary but can be made for tracking purposes.
                    </flux:callout.text>
                </flux:callout>
            @endif

            <form wire:submit="adjustCredits" class="space-y-4">
                <flux:field>
                    <flux:label>New Credit Value</flux:label>
                    <flux:input
                        type="number"
                        wire:model="newValue"
                        min="0"
                        max="1000"
                        required
                    />
                    <flux:error name="newValue" />
                    @if($newValue !== $this->currentCredits)
                        <flux:description>
                            @php
                                $change = $newValue - $this->currentCredits;
                                $changeText = $change > 0 ? "+{$change}" : $change;
                            @endphp
                            Change: <span class="{{ $change > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $changeText }}</span>
                        </flux:description>
                    @endif
                </flux:field>

                <flux:field>
                    <flux:label>Reason for Adjustment</flux:label>
                    <flux:textarea
                        wire:model="reason"
                        rows="3"
                        placeholder="e.g., Customer service credit, promotional bonus, billing correction..."
                        required
                    />
                    <flux:error name="reason" />
                    <flux:description>This will be logged in the audit trail.</flux:description>
                </flux:field>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:button variant="ghost" type="button" x-on:click="$flux.modal('adjust-subscription-credits-modal').close()">
                        Cancel
                    </flux:button>
                    <flux:button
                        type="submit"
                        variant="primary"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="adjustCredits">Save Changes</span>
                        <span wire:loading wire:target="adjustCredits">Saving...</span>
                    </flux:button>
                </div>
            </form>
        @else
            <flux:callout variant="danger" icon="exclamation-triangle">
                <flux:callout.text>Unable to load billable profile.</flux:callout.text>
            </flux:callout>
        @endif
    </div>
</flux:modal>

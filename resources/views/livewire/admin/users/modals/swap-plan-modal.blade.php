<flux:modal name="swap-plan-modal" class="max-w-lg">
    <div class="p-6">
        <flux:heading size="xl" class="mb-2">Change Plan</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
            Change the subscription plan for this user.
        </flux:text>

        <div class="space-y-4">
            @if($this->pendingSchedule)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-start justify-between">
                        <div class="flex items-start gap-3">
                            <flux:icon name="clock" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5" />
                            <div>
                                <flux:heading size="sm">Scheduled Plan Change</flux:heading>
                                <flux:text class="text-sm text-yellow-700 dark:text-yellow-300 mt-1">
                                    A plan change is scheduled for {{ $this->pendingSchedule['starts_at'] }}.
                                </flux:text>
                            </div>
                        </div>
                        <flux:button wire:click="cancelScheduledChange" variant="ghost" size="sm" wire:loading.attr="disabled">
                            Cancel
                        </flux:button>
                    </div>
                </div>
            @endif

            <flux:field>
                <flux:label>Select New Plan</flux:label>
                <flux:select wire:model="selectedPlan">
                    <option value="">Choose a plan...</option>
                    @foreach($this->availablePlans as $product)
                        @foreach($product->prices as $price)
                            <option value="{{ $price->stripe_id }}" @if($price->stripe_id === $this->currentPriceId) disabled @endif>
                                {{ $price->lookup_key ? Str::headline($price->lookup_key) : $product->name }} - ${{ number_format($price->unit_amount / 100, 2) }}/{{ $price->recurring['interval'] ?? 'month' }}
                                @if($price->stripe_id === $this->currentPriceId)
                                    (Current)
                                @endif
                            </option>
                        @endforeach
                    @endforeach
                </flux:select>
                @error('selectedPlan')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>

            <flux:field>
                <flux:label>When to Apply</flux:label>
                <flux:radio.group wire:model="swapTiming">
                    <flux:radio value="immediately" label="Immediately" description="Change the plan now with prorated billing adjustment." />
                    <flux:radio value="end_of_period" label="At End of Billing Period" description="Schedule the change for the next billing cycle." />
                </flux:radio.group>
                @error('swapTiming')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>

            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <flux:icon name="information-circle" class="w-5 h-5 text-gray-400 mt-0.5" />
                    <div>
                        <flux:text class="text-sm text-gray-500 dark:text-gray-400">
                            @if($swapTiming === 'immediately')
                                The user will be charged or credited immediately based on the price difference.
                            @else
                                The current plan will continue until the billing period ends, then the new plan will take effect.
                            @endif
                        </flux:text>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="swapPlan" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="swapPlan">Change Plan</span>
                <span wire:loading wire:target="swapPlan">Processing...</span>
            </flux:button>
        </div>
    </div>
</flux:modal>

<flux:modal name="start-trial-modal" class="max-w-lg">
    <div class="p-6">
        <flux:heading size="xl" class="mb-2">Start Trial Subscription</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
            Start a trial subscription for this user. They will have full access during the trial period.
        </flux:text>

        <div class="space-y-4">
            <flux:field>
                <flux:label>Select Plan</flux:label>
                <flux:select wire:model="selectedPlan">
                    <option value="">Choose a plan...</option>
                    @foreach($this->availablePlans as $product)
                        @foreach($product->prices as $price)
                            <option value="{{ $price->stripe_id }}">
                                {{ $price->lookup_key ? Str::headline($price->lookup_key) : $product->name }} - ${{ number_format($price->unit_amount / 100, 2) }}/{{ $price->recurring['interval'] ?? 'month' }}
                            </option>
                        @endforeach
                    @endforeach
                </flux:select>
                @error('selectedPlan')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
                <flux:description>
                    Select the subscription plan for after the trial ends.
                </flux:description>
            </flux:field>

            <flux:field>
                <flux:label>Trial Length (Days)</flux:label>
                <flux:input type="number" wire:model="trialDays" min="1" max="90" />
                @error('trialDays')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
                <flux:description>
                    Number of days for the trial period (1-90 days).
                </flux:description>
            </flux:field>
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="startTrial" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="startTrial">Start Trial</span>
                <span wire:loading wire:target="startTrial">Starting...</span>
            </flux:button>
        </div>
    </div>
</flux:modal>

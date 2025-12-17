<flux:modal name="apply-coupon-modal" class="max-w-lg">
    <div class="p-6">
        <flux:heading size="xl" class="mb-2">Apply Coupon</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
            Select a coupon to apply to this user's account.
        </flux:text>

        <div class="space-y-4">
            <flux:field>
                <flux:label>Select Coupon</flux:label>
                <flux:select wire:model="couponCode">
                    <option value="">Choose a coupon...</option>
                    @foreach($this->availableCoupons as $coupon)
                        <option value="{{ $coupon['id'] }}">{{ $coupon['label'] }}</option>
                    @endforeach
                </flux:select>
                @error('couponCode')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:field>

            @if(count($this->availableCoupons) === 0)
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <flux:icon name="exclamation-triangle" class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                        <div>
                            <flux:text class="text-sm text-yellow-700 dark:text-yellow-300">
                                No active coupons found in Stripe. Create coupons in your Stripe dashboard first.
                            </flux:text>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                    <div class="flex items-center gap-3">
                        <flux:icon name="information-circle" class="w-5 h-5 text-gray-400" />
                        <div>
                            <flux:text class="text-sm text-gray-500 dark:text-gray-400">
                                The coupon will be applied to this customer's account and used on their next invoice.
                            </flux:text>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <flux:modal.close>
                <flux:button variant="ghost">Cancel</flux:button>
            </flux:modal.close>
            <flux:button wire:click="applyCoupon" variant="primary" wire:loading.attr="disabled" :disabled="count($this->availableCoupons) === 0">
                <span wire:loading.remove wire:target="applyCoupon">Apply Coupon</span>
                <span wire:loading wire:target="applyCoupon">Applying...</span>
            </flux:button>
        </div>
    </div>
</flux:modal>

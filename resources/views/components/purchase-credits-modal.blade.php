@props([
    'name',
    'title',
    'description',
    'creditDescription',
    'unitAmount',
    'quantity',
    'quantityModel',
    'purchaseAction',
    'unavailableMessage' => 'Credit pricing is currently unavailable. Please try again later.',
    'hasPrice' => true,
])

<flux:modal :name="$name" class="max-w-lg not-prose">
    <div class="p-6">
        <flux:heading size="xl" class="mb-2">{{ $title }}</flux:heading>
        <flux:text class="text-gray-600 dark:text-gray-400 mb-6">
            {{ $description }}
        </flux:text>

        <div class="space-y-4">
            @if($hasPrice)
                <flux:field>
                    <flux:label>Number of Credits</flux:label>
                    <flux:select wire:model.live="{{ $quantityModel }}">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}">{{ $i }} credit{{ $i > 1 ? 's' : '' }}</option>
                        @endfor
                    </flux:select>
                    <flux:description>{{ $creditDescription }}</flux:description>
                </flux:field>

                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <flux:text class="text-gray-500 dark:text-gray-400">Price per credit</flux:text>
                        <flux:text class="font-medium">${{ number_format($unitAmount / 100, 2) }}</flux:text>
                    </div>
                    <div class="flex items-center justify-between mb-2">
                        <flux:text class="text-gray-500 dark:text-gray-400">Quantity</flux:text>
                        <flux:text class="font-medium">{{ $quantity }}</flux:text>
                    </div>
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                        <div class="flex items-center justify-between">
                            <flux:text class="font-medium">Total</flux:text>
                            <flux:text class="text-lg font-semibold text-green-600 dark:text-green-400">
                                ${{ number_format(($unitAmount * $quantity) / 100, 2) }}
                            </flux:text>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <flux:icon name="information-circle" class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" />
                        <div>
                            <flux:text class="text-sm text-blue-700 dark:text-blue-300">
                                Your default payment method will be charged immediately.
                            </flux:text>
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex items-start gap-3">
                        <flux:icon name="exclamation-triangle" class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mt-0.5" />
                        <div>
                            <flux:text class="text-sm text-yellow-700 dark:text-yellow-300">
                                {{ $unavailableMessage }}
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
            <flux:button
                wire:click="{{ $purchaseAction }}"
                variant="primary"
                wire:loading.attr="disabled"
                :disabled="!$hasPrice">
                <span wire:loading.remove wire:target="{{ $purchaseAction }}">Purchase Credits</span>
                <span wire:loading wire:target="{{ $purchaseAction }}">Processing...</span>
            </flux:button>
        </div>
    </div>
</flux:modal>

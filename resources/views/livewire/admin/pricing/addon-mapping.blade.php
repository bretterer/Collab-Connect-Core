<div>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Addon Price Mappings</h1>
                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                    Map Stripe one-time prices to subscription credit keys for addon purchases.
                </p>
            </div>
            <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                Add Mapping
            </flux:button>
        </div>

        @if($this->mappings->isEmpty())
            <flux:card>
                <div class="text-center py-12">
                    <flux:icon name="credit-card" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                    <flux:heading size="base">No Addon Mappings</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 mt-2">
                        Create a mapping to allow users to purchase credits as addons.
                    </flux:text>
                    <div class="mt-4">
                        <flux:button wire:click="openCreateModal" variant="primary" icon="plus">
                            Create First Mapping
                        </flux:button>
                    </div>
                </div>
            </flux:card>
        @else
            @foreach($this->mappings as $creditKey => $keyMappings)
                <flux:card>
                    <div class="mb-4">
                        <flux:heading>{{ \App\Subscription\SubscriptionMetadataSchema::getLabels()[$creditKey] ?? $creditKey }}</flux:heading>
                        <flux:text class="text-gray-600 dark:text-gray-400 text-sm">
                            {{ \App\Subscription\SubscriptionMetadataSchema::getDescription($creditKey) }}
                        </flux:text>
                    </div>

                    <div class="overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Price</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Unit Amount</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Credits Granted</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Account Type</th>
                                    <th class="px-4 py-3 text-left text-sm font-medium text-gray-500 dark:text-gray-400">Status</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-500 dark:text-gray-400">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($keyMappings as $mapping)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-900/30">
                                        <td class="px-4 py-3">
                                            <div>
                                                <flux:text class="font-medium">
                                                    {{ $mapping->display_name ?? $mapping->stripePrice?->product_name ?? 'Unknown' }}
                                                </flux:text>
                                                <flux:text class="text-xs text-gray-500">
                                                    {{ $mapping->stripePrice?->stripe_id }}
                                                </flux:text>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <flux:text>${{ number_format(($mapping->stripePrice?->unit_amount ?? 0) / 100, 2) }}</flux:text>
                                        </td>
                                        <td class="px-4 py-3">
                                            <flux:badge color="blue">{{ $mapping->credits_granted }} {{ Str::plural('credit', $mapping->credits_granted) }}</flux:badge>
                                        </td>
                                        <td class="px-4 py-3">
                                            <flux:badge color="{{ $mapping->account_type === 'both' ? 'zinc' : ($mapping->account_type === 'business' ? 'purple' : 'pink') }}">
                                                {{ ucfirst($mapping->account_type) }}
                                            </flux:badge>
                                        </td>
                                        <td class="px-4 py-3">
                                            <flux:badge color="{{ $mapping->is_active ? 'green' : 'red' }}">
                                                {{ $mapping->is_active ? 'Active' : 'Inactive' }}
                                            </flux:badge>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <flux:button wire:click="toggleActive({{ $mapping->id }})" variant="ghost" size="sm">
                                                    {{ $mapping->is_active ? 'Disable' : 'Enable' }}
                                                </flux:button>
                                                <flux:button wire:click="editMapping({{ $mapping->id }})" variant="ghost" size="sm" icon="pencil">
                                                    Edit
                                                </flux:button>
                                                <flux:button wire:click="deleteMapping({{ $mapping->id }})" wire:confirm="Are you sure you want to delete this mapping?" variant="ghost" size="sm" icon="trash" class="text-red-600 hover:text-red-700">
                                                    Delete
                                                </flux:button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </flux:card>
            @endforeach
        @endif

        @if($this->availablePrices->isNotEmpty())
            <flux:card>
                <div class="mb-4">
                    <flux:heading size="sm">Available One-Time Prices</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 text-sm">
                        These Stripe one-time prices can be mapped to credit keys.
                    </flux:text>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach($this->availablePrices as $price)
                        <flux:badge color="zinc">
                            {{ $price->product_name ?? $price->stripe_id }} - ${{ number_format($price->unit_amount / 100, 2) }}
                        </flux:badge>
                    @endforeach
                </div>
            </flux:card>
        @endif
    </div>

    <!-- Create Modal -->
    <flux:modal wire:model="showCreateModal" name="create-mapping">
        <div class="space-y-6">
            <div>
                <flux:heading>Create Addon Price Mapping</flux:heading>
                <flux:text class="text-gray-600 dark:text-gray-400">
                    Map a Stripe one-time price to a credit key.
                </flux:text>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Stripe Price</flux:label>
                    <flux:select wire:model="selectedPriceId">
                        <option value="">Select a price...</option>
                        @foreach($this->availablePrices as $price)
                            <option value="{{ $price->id }}">
                                {{ $price->product_name ?? $price->stripe_id }} - ${{ number_format($price->unit_amount / 100, 2) }}
                            </option>
                        @endforeach
                    </flux:select>
                    @error('selectedPriceId')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Credit Key</flux:label>
                    <flux:select wire:model="selectedCreditKey">
                        <option value="">Select a credit key...</option>
                        @foreach($this->creditKeyOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </flux:select>
                    @error('selectedCreditKey')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Credits Granted</flux:label>
                    <flux:input type="number" wire:model="creditsGranted" min="1" max="100" />
                    <flux:description>How many credits are granted when this price is purchased.</flux:description>
                    @error('creditsGranted')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Account Type</flux:label>
                    <flux:select wire:model="accountType">
                        @foreach($this->accountTypeOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </flux:select>
                    <flux:description>Which account types can see and purchase this addon.</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>Display Name (Optional)</flux:label>
                    <flux:input type="text" wire:model="displayName" placeholder="Custom display name..." />
                    <flux:description>Override the Stripe product name for display purposes.</flux:description>
                </flux:field>

                <flux:field>
                    <flux:label>Sort Order</flux:label>
                    <flux:input type="number" wire:model="sortOrder" min="0" />
                    <flux:description>Lower numbers appear first. Default is 0.</flux:description>
                </flux:field>

                <flux:field>
                    <flux:checkbox wire:model="isActive" label="Active" description="Only active mappings are shown to users." />
                </flux:field>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button wire:click="$set('showCreateModal', false)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="createMapping" variant="primary">Create Mapping</flux:button>
            </div>
        </div>
    </flux:modal>

    <!-- Edit Modal -->
    <flux:modal wire:model="showEditModal" name="edit-mapping">
        <div class="space-y-6">
            <div>
                <flux:heading>Edit Addon Price Mapping</flux:heading>
                <flux:text class="text-gray-600 dark:text-gray-400">
                    Update the mapping configuration.
                </flux:text>
            </div>

            <div class="space-y-4">
                <flux:field>
                    <flux:label>Credits Granted</flux:label>
                    <flux:input type="number" wire:model="creditsGranted" min="1" max="100" />
                    @error('creditsGranted')
                        <flux:error>{{ $message }}</flux:error>
                    @enderror
                </flux:field>

                <flux:field>
                    <flux:label>Account Type</flux:label>
                    <flux:select wire:model="accountType">
                        @foreach($this->accountTypeOptions as $option)
                            <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field>
                    <flux:label>Display Name (Optional)</flux:label>
                    <flux:input type="text" wire:model="displayName" placeholder="Custom display name..." />
                </flux:field>

                <flux:field>
                    <flux:label>Sort Order</flux:label>
                    <flux:input type="number" wire:model="sortOrder" min="0" />
                </flux:field>

                <flux:field>
                    <flux:checkbox wire:model="isActive" label="Active" />
                </flux:field>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button wire:click="$set('showEditModal', false)" variant="ghost">Cancel</flux:button>
                <flux:button wire:click="updateMapping" variant="primary">Save Changes</flux:button>
            </div>
        </div>
    </flux:modal>
</div>

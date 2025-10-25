<div>
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Pricing Management</h1>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">Manage Stripe prices and their metadata for pricing tables.</p>
        </div>

        @if (session()->has('message'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="space-y-6">
            @forelse ($products as $product)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm ring-1 ring-black ring-opacity-5 dark:ring-white dark:ring-opacity-10 rounded-lg">
                    <!-- Product Header -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $product->name }}
                                    </h3>
                                    @php
                                        $accountType = null;
                                        $accountTypeValue = $product->metadata['account_type'] ?? null;
                                        
                                        if ($accountTypeValue) {
                                            foreach (\App\Enums\AccountType::cases() as $case) {
                                                if ($case->name === $accountTypeValue || $case->value == $accountTypeValue) {
                                                    $accountType = $case;
                                                    break;
                                                }
                                            }
                                        }
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                        {{ $accountType === \App\Enums\AccountType::BUSINESS ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 
                                           ($accountType === \App\Enums\AccountType::INFLUENCER ? 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                        {{ $accountType?->label() ?? 'Not Set' }}
                                    </span>
                                </div>
                                @if($product->description)
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $product->description }}</p>
                                @endif
                                <div class="mt-2 flex items-center justify-between">
                                    <div class="flex items-center space-x-4 text-sm">
                                        <a href="https://dashboard.stripe.com/products/{{ $product->stripe_id }}" 
                                           target="_blank" 
                                           class="inline-flex items-center text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                            </svg>
                                            View in Stripe
                                        </a>
                                        <span class="text-gray-500 dark:text-gray-400">•</span>
                                        <span class="text-gray-500 dark:text-gray-400">{{ $product->prices->count() }} price{{ $product->prices->count() !== 1 ? 's' : '' }}</span>
                                        <span class="text-gray-500 dark:text-gray-400">•</span>
                                        <span class="text-gray-500 dark:text-gray-400">
                                            @if($product->metadata && count($product->metadata) > 0)
                                                {{ count($product->metadata) }} metadata field{{ count($product->metadata) !== 1 ? 's' : '' }}
                                            @else
                                                No product metadata
                                            @endif
                                        </span>
                                    </div>
                                    <button wire:click="editProduct({{ $product->id }})" 
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit Account Type
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Prices List -->
                    <div class="divide-y divide-gray-200 dark:divide-gray-600">
                        @foreach ($product->prices as $price)
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 grid grid-cols-4 gap-4">
                                        <!-- Price Details -->
                                        <div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                @if($price->unit_amount)
                                                    ${{ number_format($price->unit_amount / 100, 2) }}
                                                @else
                                                    Free
                                                @endif
                                            </div>
                                            @if($price->recurring)
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    per {{ $price->recurring['interval'] ?? 'period' }}
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Type -->
                                        <div>
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $price->type === 'recurring' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }}">
                                                {{ ucfirst($price->type) }}
                                            </span>
                                        </div>

                                        <!-- Features Status -->
                                        <div>
                                            @php
                                                $features = [];
                                                if (isset($price->metadata['features'])) {
                                                    $features = is_string($price->metadata['features']) ? 
                                                        json_decode($price->metadata['features'], true) : 
                                                        $price->metadata['features'];
                                                    $features = $features ?? [];
                                                }
                                            @endphp
                                            @if(!empty($features))
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    {{ count($features) }} feature{{ count($features) !== 1 ? 's' : '' }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                    No features
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Actions -->
                                        <div class="text-right">
                                            <button wire:click="editPrice({{ $price->id }})" 
                                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                Edit Features
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Price ID with Stripe Link -->
                                <div class="mt-2">
                                    <a href="https://dashboard.stripe.com/prices/{{ $price->stripe_id }}" 
                                       target="_blank" 
                                       class="inline-flex items-center text-xs text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        {{ $price->stripe_id }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.99 1.99 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No products found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No active products with prices were found.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Edit Metadata Modal -->
    @if($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                @if($editType === 'price')
                                    Edit Price Features
                                @elseif($editType === 'product')
                                    Edit Product Account Type
                                @endif
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($editType === 'price')
                                        Manage features for price: <strong class="text-gray-900 dark:text-white">{{ $selectedPrice?->stripe_id }}</strong>
                                        <br>Product: <strong class="text-gray-900 dark:text-white">{{ $selectedPrice?->stripeProduct?->name }}</strong>
                                        <br><span class="text-xs">Features will be stored as JSON in the price metadata.</span>
                                    @elseif($editType === 'product')
                                        Set account type for product: <strong class="text-gray-900 dark:text-white">{{ $selectedProduct?->name }}</strong>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <form wire:submit="saveMetadata">
                            @if($editType === 'product')
                                <!-- Product Account Type Selection -->
                                <div class="space-y-4">
                                    <flux:field>
                                        <flux:label>Account Type</flux:label>
                                        <flux:select
                                            wire:model="selectedAccountType"
                                            variant="listbox"
                                            placeholder="Select Account Type">
                                            @foreach($this->accountTypeOptions as $option)
                                                <flux:select.option value="{{ $option['value'] }}">{{ $option['label'] }}</flux:select.option>
                                            @endforeach
                                        </flux:select>
                                    </flux:field>
                                </div>
                            @elseif($editType === 'price')
                                <!-- Price Features Management -->
                                <div class="space-y-4">
                                    <flux:field>
                                        <flux:label>Features List</flux:label>
                                        <div class="space-y-3">
                                            @foreach($priceFeatures as $index => $feature)
                                                <div class="flex gap-3 items-start">
                                                    <div class="flex-1">
                                                        <flux:input 
                                                            wire:model="priceFeatures.{{ $index }}" 
                                                            placeholder="Enter feature description" />
                                                    </div>
                                                    <flux:button 
                                                        wire:click="removeFeature({{ $index }})"
                                                        variant="danger" 
                                                        size="sm"
                                                        icon="trash"
                                                        type="button" />
                                                </div>
                                            @endforeach
                                            
                                            @if(empty($priceFeatures))
                                                <p class="text-sm text-gray-500 dark:text-gray-400 italic">
                                                    No features added yet. Click "Add Feature" to get started.
                                                </p>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-3">
                                            <flux:button 
                                                wire:click="addFeature"
                                                variant="ghost" 
                                                size="sm"
                                                icon="plus"
                                                type="button">
                                                Add Feature
                                            </flux:button>
                                        </div>
                                    </flux:field>
                                </div>
                            @endif

                            <div class="mt-6 flex justify-end gap-3">
                                <flux:button 
                                    wire:click="closeModal"
                                    variant="ghost"
                                    type="button">
                                    Cancel
                                </flux:button>
                                <flux:button 
                                    type="submit"
                                    variant="primary">
                                    @if($editType === 'price')
                                        Save Features
                                    @elseif($editType === 'product')
                                        Save Account Type
                                    @else
                                        Save
                                    @endif
                                </flux:button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

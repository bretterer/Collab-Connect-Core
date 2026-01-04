<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">Pricing Matrix</flux:heading>
                <flux:text class="text-zinc-500">Define feature categories and features that appear in the pricing comparison table.</flux:text>
            </div>
            <flux:button href="{{ route('admin.pricing') }}" variant="ghost" icon="arrow-left">
                Back to Pricing
            </flux:button>
        </div>

        <!-- Highlighted Plans Section -->
        <flux:card>
            <div class="space-y-4">
                <flux:heading>Highlighted Plans</flux:heading>
                <flux:text class="text-zinc-500">Select which plan should be highlighted as "Most Popular" for each account type.</flux:text>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                    <flux:field>
                        <flux:label>Business - Most Popular Plan</flux:label>
                        <flux:select wire:model="highlightedBusinessPriceId" variant="listbox" placeholder="Select a plan...">
                            <flux:select.option value="">None</flux:select.option>
                            @foreach($this->businessPrices as $price)
                                <flux:select.option value="{{ $price->stripe_id }}">
                                    {{ $price->product->name }} - ${{ number_format($price->unit_amount / 100, 2) }}/{{ $price->recurring['interval'] ?? 'month' }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label>Influencer - Most Popular Plan</flux:label>
                        <flux:select wire:model="highlightedInfluencerPriceId" variant="listbox" placeholder="Select a plan...">
                            <flux:select.option value="">None</flux:select.option>
                            @foreach($this->influencerPrices as $price)
                                <flux:select.option value="{{ $price->stripe_id }}">
                                    {{ $price->product->name }} - ${{ number_format($price->unit_amount / 100, 2) }}/{{ $price->recurring['interval'] ?? 'month' }}
                                </flux:select.option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <div class="flex justify-end pt-2">
                    <flux:button wire:click="saveHighlightedPlan" variant="primary">
                        Save Highlighted Plans
                    </flux:button>
                </div>
            </div>
        </flux:card>

        <!-- Feature Categories -->
        <flux:card>
            <div class="space-y-6">
                <!-- Tabs -->
                <div class="border-b border-zinc-200 dark:border-zinc-700">
                    <nav class="flex space-x-8" aria-label="Account types">
                        <button
                            type="button"
                            wire:click="setActiveTab('business')"
                            class="py-3 px-1 border-b-2 text-sm font-medium transition-colors {{ $activeTab === 'business' ? 'border-purple-500 text-purple-600 dark:text-purple-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                            <flux:icon.building-office class="w-4 h-4 inline-block mr-1" />
                            Business Features
                        </button>
                        <button
                            type="button"
                            wire:click="setActiveTab('influencer')"
                            class="py-3 px-1 border-b-2 text-sm font-medium transition-colors {{ $activeTab === 'influencer' ? 'border-pink-500 text-pink-600 dark:text-pink-400' : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300' }}">
                            <flux:icon.user class="w-4 h-4 inline-block mr-1" />
                            Influencer Features
                        </button>
                    </nav>
                </div>

                <!-- Add Category Button -->
                <div class="flex justify-end">
                    <flux:button wire:click="openAddCategoryModal" variant="primary" icon="plus">
                        Add Category
                    </flux:button>
                </div>

                <!-- Categories List -->
                @php
                    $categories = $activeTab === 'business' ? $this->businessCategories : $this->influencerCategories;
                @endphp

                @forelse($categories as $catIndex => $category)
                    <div class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden">
                        <!-- Category Header -->
                        <div class="bg-zinc-50 dark:bg-zinc-800 px-4 py-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-1">
                                    <button
                                        type="button"
                                        wire:click="moveCategoryUp({{ $catIndex }})"
                                        class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 disabled:opacity-50"
                                        @if($catIndex === 0) disabled @endif>
                                        <flux:icon.chevron-up class="w-4 h-4" />
                                    </button>
                                    <button
                                        type="button"
                                        wire:click="moveCategoryDown({{ $catIndex }})"
                                        class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 disabled:opacity-50"
                                        @if($catIndex === count($categories) - 1) disabled @endif>
                                        <flux:icon.chevron-down class="w-4 h-4" />
                                    </button>
                                </div>
                                <div>
                                    <flux:heading size="sm">{{ $category['label'] }}</flux:heading>
                                    <flux:text class="text-xs text-zinc-500">Key: {{ $category['key'] }}</flux:text>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <flux:button wire:click="openAddFeatureModal({{ $catIndex }})" variant="ghost" size="sm" icon="plus">
                                    Add Feature
                                </flux:button>
                                <flux:button wire:click="openEditCategoryModal({{ $catIndex }})" variant="ghost" size="sm" icon="pencil" />
                                <flux:button
                                    wire:click="deleteCategory({{ $catIndex }})"
                                    wire:confirm="Are you sure you want to delete this category and all its features?"
                                    variant="ghost"
                                    size="sm"
                                    icon="trash"
                                    class="text-red-600 hover:text-red-700" />
                            </div>
                        </div>

                        <!-- Features List -->
                        <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                            @forelse($category['features'] ?? [] as $featIndex => $feature)
                                <div class="px-4 py-3 flex items-center justify-between hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                    <div class="flex items-center gap-3">
                                        <div class="flex flex-col gap-1">
                                            <button
                                                type="button"
                                                wire:click="moveFeatureUp({{ $catIndex }}, {{ $featIndex }})"
                                                class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 disabled:opacity-50"
                                                @if($featIndex === 0) disabled @endif>
                                                <flux:icon.chevron-up class="w-3 h-3" />
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="moveFeatureDown({{ $catIndex }}, {{ $featIndex }})"
                                                class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 disabled:opacity-50"
                                                @if($featIndex === count($category['features']) - 1) disabled @endif>
                                                <flux:icon.chevron-down class="w-3 h-3" />
                                            </button>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <flux:text class="font-medium">{{ $feature['label'] }}</flux:text>
                                            @if(!empty($feature['description']))
                                                <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400 truncate">{{ $feature['description'] }}</flux:text>
                                            @endif
                                            <div class="flex items-center gap-2 text-xs text-zinc-500">
                                                <span>Key: {{ $feature['key'] }}</span>
                                                <span>•</span>
                                                <flux:badge size="sm" color="{{ $feature['type'] === 'boolean' ? 'green' : ($feature['type'] === 'number' ? 'blue' : 'zinc') }}">
                                                    {{ ucfirst($feature['type']) }}
                                                </flux:badge>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <flux:button wire:click="openEditFeatureModal({{ $catIndex }}, {{ $featIndex }})" variant="ghost" size="sm" icon="pencil" />
                                        <flux:button
                                            wire:click="deleteFeature({{ $catIndex }}, {{ $featIndex }})"
                                            wire:confirm="Are you sure you want to delete this feature?"
                                            variant="ghost"
                                            size="sm"
                                            icon="trash"
                                            class="text-red-600 hover:text-red-700" />
                                    </div>
                                </div>
                            @empty
                                <div class="px-4 py-6 text-center text-zinc-500">
                                    <flux:icon.tag class="w-8 h-8 mx-auto mb-2 text-zinc-300 dark:text-zinc-600" />
                                    <flux:text>No features in this category yet.</flux:text>
                                </div>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <flux:icon.rectangle-stack class="w-12 h-12 mx-auto text-zinc-300 dark:text-zinc-600 mb-4" />
                        <flux:heading size="sm" class="mb-2">No categories defined</flux:heading>
                        <flux:text class="text-zinc-500 mb-4">Create your first category to start building the pricing matrix.</flux:text>
                        <flux:button wire:click="openAddCategoryModal" variant="primary" icon="plus">
                            Add Category
                        </flux:button>
                    </div>
                @endforelse
            </div>
        </flux:card>
    </div>

    <!-- Category Modal -->
    <flux:modal wire:model="showCategoryModal" class="max-w-md">
        <div class="p-6 space-y-6">
            <flux:heading size="xl">
                {{ $editingCategoryIndex !== null ? 'Edit Category' : 'Add Category' }}
            </flux:heading>

            <form wire:submit="saveCategory" class="space-y-4">
                <flux:field>
                    <flux:label>Category Key</flux:label>
                    <flux:input
                        wire:model="categoryKey"
                        placeholder="e.g., campaigns"
                        :disabled="$editingCategoryIndex !== null" />
                    <flux:description>Lowercase letters, numbers, and underscores only. Cannot be changed after creation.</flux:description>
                    <flux:error name="categoryKey" />
                </flux:field>

                <flux:field>
                    <flux:label>Category Label</flux:label>
                    <flux:input wire:model="categoryLabel" placeholder="e.g., Campaigns & Publishing" />
                    <flux:error name="categoryLabel" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">
                        {{ $editingCategoryIndex !== null ? 'Save Changes' : 'Add Category' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>

    <!-- Feature Modal -->
    <flux:modal wire:model="showFeatureModal" class="max-w-md">
        <div class="p-6 space-y-6">
            <flux:heading size="xl">
                {{ $editingFeatureIndex !== null ? 'Edit Feature' : 'Add Feature' }}
            </flux:heading>

            <form wire:submit="saveFeature" class="space-y-4">
                <flux:field>
                    <flux:label>Feature Key</flux:label>
                    <flux:input
                        wire:model="featureKey"
                        placeholder="e.g., campaigns_per_month"
                        :disabled="$editingFeatureIndex !== null" />
                    <flux:description>Lowercase letters, numbers, and underscores only.</flux:description>
                    <flux:error name="featureKey" />
                </flux:field>

                <flux:field>
                    <flux:label>Feature Label</flux:label>
                    <flux:input wire:model="featureLabel" placeholder="e.g., Campaigns per Month" />
                    <flux:error name="featureLabel" />
                </flux:field>

                <flux:field>
                    <flux:label>Feature Type</flux:label>
                    <flux:select wire:model="featureType" variant="listbox">
                        <flux:select.option value="boolean">Boolean (Yes/No checkmark)</flux:select.option>
                        <flux:select.option value="number">Number (shows value or "Unlimited")</flux:select.option>
                        <flux:select.option value="text">Text (shows custom text)</flux:select.option>
                    </flux:select>
                    <flux:error name="featureType" />
                </flux:field>

                <flux:field>
                    <flux:label>Description / Help Text</flux:label>
                    <flux:textarea wire:model="featureDescription" placeholder="Optional description that appears as a tooltip on the pricing table" rows="2" />
                    <flux:description>This will appear as a tooltip when users hover over the feature name.</flux:description>
                    <flux:error name="featureDescription" />
                </flux:field>

                <div class="flex justify-end gap-3 pt-4">
                    <flux:modal.close>
                        <flux:button variant="ghost">Cancel</flux:button>
                    </flux:modal.close>
                    <flux:button type="submit" variant="primary">
                        {{ $editingFeatureIndex !== null ? 'Save Changes' : 'Add Feature' }}
                    </flux:button>
                </div>
            </form>
        </div>
    </flux:modal>
</div>

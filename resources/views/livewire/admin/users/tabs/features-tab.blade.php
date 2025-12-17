<div>
    <flux:card class="mb-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Search -->
            <flux:field>
                <flux:label>Search Features</flux:label>
                <flux:input
                    wire:model.live.debounce.300ms="featureSearch"
                    placeholder="Search by feature name..."
                />
            </flux:field>

            <!-- Status Filter -->
            <flux:field>
                <flux:label>Status</flux:label>
                <flux:select wire:model.live="featureStatusFilter">
                    <option value="">All Features</option>
                    <option value="enabled">Enabled Only</option>
                    <option value="disabled">Disabled Only</option>
                </flux:select>
            </flux:field>
        </div>
    </flux:card>

    <flux:card>
        <div class="space-y-1">
            @forelse($this->getFilteredFeatures() as $feature)
                <div class="flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-900/50 rounded-lg transition-colors">
                    <div class="flex-1 min-w-0 mr-4">
                        <div class="flex items-center space-x-3">
                            <flux:heading size="sm">{{ $feature->title }}</flux:heading>
                            @if($this->isFeatureEnabled($feature->key))
                                <flux:badge color="green" size="sm">Enabled</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Disabled</flux:badge>
                            @endif
                        </div>
                        @if($feature->description)
                            <flux:text class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                {{ $feature->description }}
                            </flux:text>
                        @endif

                    </div>
                    <div class="flex-shrink-0">
                        <flux:switch
                            wire:model="features.{{ $feature->key }}"
                            wire:change="toggleFeature('{{ $feature->key }}')"
                        />
                    </div>
                </div>
                @if(!$loop->last)
                    <flux:separator />
                @endif
            @empty
                <div class="text-center py-12">
                    <flux:icon name="flag" class="w-12 h-12 mx-auto text-gray-400 mb-4" />
                    <flux:heading size="lg">No Features Found</flux:heading>
                    <flux:text class="text-gray-600 dark:text-gray-400 mt-2">
                        @if($featureSearch)
                            No features match your search criteria.
                        @else
                            No feature flags are configured for this application.
                        @endif
                    </flux:text>
                </div>
            @endforelse
        </div>
    </flux:card>
</div>
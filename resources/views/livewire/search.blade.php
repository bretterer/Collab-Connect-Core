<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <flux:heading level="1">{{ $this->pageTitle }}</flux:heading>
                    <flux:text class="mt-1">{{ $this->pageSubtitle }}</flux:text>
                </div>

                <!-- Quick Stats -->
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($results->total()) }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            @if($isProximitySearch && $searchPostalCode)
                                within {{ $searchRadius }} mi
                            @else
                                {{ $this->searchTargetLabel }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="mt-6">
                <div class="flex flex-col gap-3 md:flex-row">
                    <!-- Main Search -->
                    <div class="flex-1">
                        <flux:input
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search {{ $this->searchTargetLabel }} by name..."
                            icon="magnifying-glass"
                        />
                    </div>

                    <!-- Location Search -->
                    <div class="w-full md:w-48">
                        <flux:input
                            wire:model.live.debounce.300ms="location"
                            placeholder="Zip code"
                            icon="map-pin"
                        />
                    </div>

                    <!-- Radius (shows when location is valid) -->
                    @if($this->isValidZipCode())
                        <div class="w-full md:w-36">
                            <flux:select wire:model.live="searchRadius">
                                @foreach($this->filterOptions['radiusOptions'] as $option)
                                    <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                @endforeach
                            </flux:select>
                        </div>
                    @endif

                    <!-- Sort -->
                    <div class="w-full md:w-44">
                        <flux:select wire:model.live="sortBy">
                            @foreach($this->filterOptions['sortOptions'] as $option)
                                <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                            @endforeach
                            @if($this->isValidZipCode())
                                <option value="distance">Nearest</option>
                            @endif
                        </flux:select>
                    </div>

                    <!-- Mobile Filter Toggle -->
                    <div class="md:hidden">
                        <flux:button wire:click="toggleMobileFilters" variant="outline" class="w-full">
                            <flux:icon.funnel class="w-4 h-4 mr-2" />
                            Filters
                            @if($this->activeFilterCount > 0)
                                <flux:badge size="sm" color="blue" class="ml-2">{{ $this->activeFilterCount }}</flux:badge>
                            @endif
                        </flux:button>
                    </div>
                </div>

                <!-- Location Info Banner -->
                @if($isProximitySearch && $searchPostalCode)
                    <div class="mt-3 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <flux:icon.map-pin class="w-4 h-4 text-blue-500" />
                        <span>Showing {{ $this->searchTargetLabel }} within <strong>{{ $searchRadius }} miles</strong> of <strong>{{ $searchPostalCode->place_name }}, {{ $searchPostalCode->admin_code1 }}</strong></span>
                        <button wire:click="$set('location', '')" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 ml-1">
                            Clear
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(!auth()->user()->isAdmin() && !auth()->user()->profile->subscribed('default'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <livewire:components.subscription-prompt
                variant="blue"
                heading="Unlock Advanced Search"
                description="Subscribe to access powerful search capabilities including location-based filtering, social platform selection, follower count ranges, and more."
                :features="[
                    'Location-based filtering',
                    'Social platform selection',
                    'Follower count ranges',
                    'Advanced sorting options'
                ]"
            />
        </div>
    @else
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex gap-6">
            <!-- Filters Sidebar (Desktop) -->
            <div class="hidden md:block w-64 shrink-0 overflow-hidden">
                <div class="sticky top-6 space-y-4">
                    <!-- Filter Header -->
                    <div class="flex items-center justify-between">
                        <flux:heading level="3" class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                            Filters
                        </flux:heading>
                        @if($this->activeFilterCount > 0)
                            <flux:button wire:click="clearFilters" variant="ghost" size="sm">
                                Clear all
                            </flux:button>
                        @endif
                    </div>

                    @if($this->isBusinessUser)
                    <!-- Follower Count (Business searching for Influencers) -->
                    <flux:card class="p-4">
                        <flux:heading level="4" class="text-sm font-medium mb-3">Follower Count</flux:heading>

                        <!-- Quick Presets -->
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($this->filterOptions['followerPresets'] as $preset)
                                @php
                                    $isActive = (
                                        ($preset['min'] === null && $minFollowers === '') ||
                                        ($preset['min'] !== null && $minFollowers === (string)$preset['min'])
                                    ) && (
                                        ($preset['max'] === null && $maxFollowers === '') ||
                                        ($preset['max'] !== null && $maxFollowers === (string)$preset['max'])
                                    ) && !($minFollowers === '' && $maxFollowers === '');
                                @endphp
                                <button
                                    wire:click="applyFollowerPreset({{ $preset['min'] ?? 'null' }}, {{ $preset['max'] ?? 'null' }})"
                                    class="px-2 py-1 text-xs rounded-full border transition-colors
                                        {{ $isActive
                                            ? 'bg-blue-100 border-blue-300 text-blue-700 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-300'
                                            : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400 dark:hover:border-gray-500' }}"
                                >
                                    {{ $preset['label'] }}
                                </button>
                            @endforeach
                        </div>

                        <!-- Custom Range -->
                        <div class="grid grid-cols-2 gap-2">
                            <flux:input
                                wire:model.live.debounce.500ms="minFollowers"
                                type="number"
                                placeholder="Min"
                                size="sm"
                            />
                            <flux:input
                                wire:model.live.debounce.500ms="maxFollowers"
                                type="number"
                                placeholder="Max"
                                size="sm"
                            />
                        </div>
                    </flux:card>

                    <!-- Platforms (Business searching for Influencers) -->
                        <flux:card class="p-4">
                            <flux:heading level="4" class="text-sm font-medium mb-3">Platforms</flux:heading>
                            <div class="space-y-2">
                                @foreach($this->platformOptions as $platform)
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedPlatforms"
                                            value="{{ $platform['value'] }}"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                        >
                                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white">
                                            {{ $platform['label'] }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </flux:card>
                    @endif

                    <!-- Industries/Niches -->
                    <flux:card class="p-4">
                        <flux:heading level="4" class="text-sm font-medium mb-3">Industries</flux:heading>
                        <div class="max-h-48 overflow-y-auto space-y-2 pr-2">
                            @foreach($this->industryOptions as $industry)
                                <label class="flex items-center gap-2 cursor-pointer group">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedNiches"
                                        value="{{ $industry['value'] }}"
                                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700"
                                    >
                                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white truncate">
                                        {{ $industry['label'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </flux:card>

                    <!-- Saved & Hidden Filters -->
                    <flux:card class="p-4">
                        <flux:heading level="4" class="text-sm font-medium mb-3">My Lists</flux:heading>
                        <div class="space-y-3">
                            <label class="flex items-center justify-between cursor-pointer group">
                                <div class="flex items-center gap-2">
                                    <flux:icon.heart class="w-4 h-4 text-red-500" />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Saved only</span>
                                    @if($savedCount > 0)
                                        <flux:badge size="sm" color="red">{{ $savedCount }}</flux:badge>
                                    @endif
                                </div>
                                <flux:switch wire:model.live="showSavedOnly" />
                            </label>
                            <label class="flex items-center justify-between cursor-pointer group">
                                <div class="flex items-center gap-2">
                                    <flux:icon.eye-slash class="w-4 h-4 text-gray-500" />
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Hide hidden</span>
                                    @if($hiddenCount > 0)
                                        <flux:badge size="sm" color="zinc">{{ $hiddenCount }}</flux:badge>
                                    @endif
                                </div>
                                <flux:switch wire:model.live="hideHidden" />
                            </label>
                        </div>
                    </flux:card>
                </div>
            </div>

            <!-- Results Area -->
            <div class="flex-1 min-w-0">
                <!-- Active Filter Pills -->
                @if($this->activeFilterCount > 0)
                    <div class="flex flex-wrap gap-2 mb-4">
                        @if(!empty($selectedNiches))
                            @foreach($selectedNiches as $niche)
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700 rounded-full dark:bg-blue-900 dark:text-blue-300">
                                    {{ \App\Enums\BusinessIndustry::from($niche)->label() }}
                                    <button wire:click="removeNiche('{{ $niche }}')" class="hover:text-blue-900 dark:hover:text-blue-100">
                                        <flux:icon.x-mark class="w-3 h-3" />
                                    </button>
                                </span>
                            @endforeach
                        @endif

                        @if($this->isBusinessUser)
                            @if(!empty($selectedPlatforms))
                                @foreach($selectedPlatforms as $platform)
                                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-purple-100 text-purple-700 rounded-full dark:bg-purple-900 dark:text-purple-300">
                                        {{ \App\Enums\SocialPlatform::from($platform)->label() }}
                                        <button wire:click="removePlatform('{{ $platform }}')" class="hover:text-purple-900 dark:hover:text-purple-100">
                                            <flux:icon.x-mark class="w-3 h-3" />
                                        </button>
                                    </span>
                                @endforeach
                            @endif

                            @if(!empty($minFollowers) || !empty($maxFollowers))
                                <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full dark:bg-green-900 dark:text-green-300">
                                    @if(!empty($minFollowers) && !empty($maxFollowers))
                                        {{ number_format((int)$minFollowers) }} - {{ number_format((int)$maxFollowers) }} followers
                                    @elseif(!empty($minFollowers))
                                        {{ number_format((int)$minFollowers) }}+ followers
                                    @else
                                        Up to {{ number_format((int)$maxFollowers) }} followers
                                    @endif
                                    <button wire:click="clearFollowerFilter" class="hover:text-green-900 dark:hover:text-green-100">
                                        <flux:icon.x-mark class="w-3 h-3" />
                                    </button>
                                </span>
                            @endif
                        @endif
                    </div>
                @endif

                <!-- Loading Indicator -->
                <div wire:loading.delay class="mb-4">
                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                        <flux:icon.arrow-path class="w-4 h-4 animate-spin" />
                        <span>Searching...</span>
                    </div>
                </div>

                <!-- Results Grid -->
                @if($results->count() > 0)
                    <div wire:loading.class="opacity-50" class="transition-opacity">
                        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                            @foreach($results as $index => $result)
                                <div wire:key="result-{{ $result->id }}-{{ class_basename($result) }}">
                                    @if($this->isBusinessUser)
                                        <livewire:influencer-card
                                            :influencer="$result"
                                            :key="'influencer-card-'.$result->id"
                                        />
                                    @else
                                        <livewire:business-card
                                            :business="$result"
                                            :key="'business-card-'.$result->id"
                                        />
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($results->hasPages())
                            <div class="mt-8">
                                {{ $results->links() }}
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Empty State -->
                    <div wire:loading.remove>
                        <flux:card class="p-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
                                    @if($this->isBusinessUser)
                                        <flux:icon.users class="w-8 h-8 text-gray-400" />
                                    @else
                                        <flux:icon.building-storefront class="w-8 h-8 text-gray-400" />
                                    @endif
                                </div>
                                <flux:heading level="3" class="mb-2">No {{ $this->searchTargetLabel }} found</flux:heading>
                                <flux:text class="mb-6 max-w-md">
                                    @if($this->activeFilterCount > 0)
                                        Try adjusting your filters or expanding your search radius to find more {{ $this->searchTargetLabel }}.
                                    @else
                                        Start by entering a search term or applying some filters to discover {{ $this->searchTargetLabel }}.
                                    @endif
                                </flux:text>
                                @if($this->activeFilterCount > 0)
                                    <flux:button wire:click="clearFilters" variant="primary">
                                        Clear All Filters
                                    </flux:button>
                                @endif
                            </div>
                        </flux:card>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Mobile Filters Slide-over -->
    @if($showMobileFilters)
        <div class="fixed inset-0 z-50 md:hidden">
            <div class="absolute inset-0 bg-black/50" wire:click="toggleMobileFilters"></div>
            <div class="absolute right-0 top-0 h-full w-80 bg-white dark:bg-gray-800 shadow-xl overflow-y-auto">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <flux:heading level="3">Filters</flux:heading>
                        <flux:button wire:click="toggleMobileFilters" variant="ghost" size="sm" icon="x-mark" />
                    </div>

                    <div class="space-y-6">
                        @if($this->isBusinessUser)
                        <!-- Follower Count (Mobile) -->
                        <div>
                            <flux:heading level="4" class="text-sm font-medium mb-3">Follower Count</flux:heading>
                            <div class="flex flex-wrap gap-2 mb-3">
                                @foreach($this->filterOptions['followerPresets'] as $preset)
                                    <button
                                        wire:click="applyFollowerPreset({{ $preset['min'] ?? 'null' }}, {{ $preset['max'] ?? 'null' }})"
                                        class="px-2 py-1 text-xs rounded-full border bg-white border-gray-200 text-gray-600 hover:border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-400"
                                    >
                                        {{ $preset['label'] }}
                                    </button>
                                @endforeach
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <flux:input wire:model.live.debounce.500ms="minFollowers" type="number" placeholder="Min" size="sm" />
                                <flux:input wire:model.live.debounce.500ms="maxFollowers" type="number" placeholder="Max" size="sm" />
                            </div>
                        </div>

                        <!-- Platforms (Mobile) -->
                        <div>
                            <flux:heading level="4" class="text-sm font-medium mb-3">Platforms</flux:heading>
                            <div class="space-y-2">
                                @foreach($this->platformOptions as $platform)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" wire:model.live="selectedPlatforms" value="{{ $platform['value'] }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $platform['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Industries (Mobile) -->
                        <div>
                            <flux:heading level="4" class="text-sm font-medium mb-3">Industries</flux:heading>
                            <div class="max-h-48 overflow-y-auto space-y-2">
                                @foreach($this->industryOptions as $industry)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" wire:model.live="selectedNiches" value="{{ $industry['value'] }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $industry['label'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Saved & Hidden (Mobile) -->
                        <div>
                            <flux:heading level="4" class="text-sm font-medium mb-3">My Lists</flux:heading>
                            <div class="space-y-3">
                                <label class="flex items-center justify-between cursor-pointer">
                                    <div class="flex items-center gap-2">
                                        <flux:icon.heart class="w-4 h-4 text-red-500" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Saved only</span>
                                        @if($savedCount > 0)
                                            <flux:badge size="sm" color="red">{{ $savedCount }}</flux:badge>
                                        @endif
                                    </div>
                                    <flux:switch wire:model.live="showSavedOnly" />
                                </label>
                                <label class="flex items-center justify-between cursor-pointer">
                                    <div class="flex items-center gap-2">
                                        <flux:icon.eye-slash class="w-4 h-4 text-gray-500" />
                                        <span class="text-sm text-gray-700 dark:text-gray-300">Hide hidden</span>
                                        @if($hiddenCount > 0)
                                            <flux:badge size="sm" color="zinc">{{ $hiddenCount }}</flux:badge>
                                        @endif
                                    </div>
                                    <flux:switch wire:model.live="hideHidden" />
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex gap-2">
                        <flux:button wire:click="clearFilters" variant="outline" class="flex-1">Clear All</flux:button>
                        <flux:button wire:click="toggleMobileFilters" variant="primary" class="flex-1">Apply</flux:button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Invite Modal -->
    <livewire:invite-influencer-modal />
</div>

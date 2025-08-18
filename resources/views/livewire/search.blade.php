<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    Search {{ ucfirst($searchingFor) }}
                </h1>
                <p class="mt-2 text-gray-600 dark:text-gray-400">
                    @if($targetAccountType === App\Enums\AccountType::INFLUENCER)
                        Discover talented influencers for your next campaign
                    @else
                        Find businesses looking for collaboration opportunities
                    @endif
                </p>
            </div>


        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <!-- Enhanced Filters Sidebar -->
        <div class="col-span-2 lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 sticky top-6">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <flux:icon.funnel class="w-5 h-5" />
                            Search Filters
                        </h3>
                        <flux:button wire:click="clearFilters" variant="ghost" size="sm">
                            <flux:icon.x-mark class="w-4 h-4" />
                            Clear
                        </flux:button>
                    </div>
                </div>
                <div class="p-6 space-y-6">

                <!-- Search Input -->
                <div>
                    <flux:field>
                        <flux:label class="flex items-center gap-2">
                            <flux:icon.magnifying-glass class="w-4 h-4" />
                            Search Influencers
                        </flux:label>
                        <flux:input wire:model.live.debounce.300ms="search"
                                   placeholder="Search by name or email..." />
                    </flux:field>
                </div>

                <!-- Location Filter -->
                <div>
                    <flux:field>
                        <flux:label class="flex items-center gap-2">
                            <flux:icon.map-pin class="w-4 h-4" />
                            Location
                        </flux:label>
                        <flux:input wire:model.live.debounce.300ms="location"
                                   placeholder="Enter zipcode (e.g., 45066)..." />
                        <flux:description class="text-xs">
                            💡 Enter a 5-digit zipcode for proximity search
                        </flux:description>
                    </flux:field>
                </div>

                <!-- Search Radius -->
                @if(preg_match('/^\d{5}$/', $location))
                    <div>
                        <flux:field>
                            <flux:label>Search Radius</flux:label>
                            <flux:select wire:model.live="searchRadius">
                                <option value="10">10 miles</option>
                                <option value="25">25 miles</option>
                                <option value="50">50 miles</option>
                                <option value="100">100 miles</option>
                                <option value="250">250 miles</option>
                            </flux:select>
                        </flux:field>
                    </div>
                @endif

                <!-- Niche/Industry Filter -->
                <div>
                    <flux:field>
                        <flux:label>Industries</flux:label>
                        <div class="relative">
                            <div class="space-y-2 max-h-48 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg p-3 bg-gray-50 dark:bg-gray-700/50">
                                @foreach(App\Enums\BusinessIndustry::cases() as $niche)
                                    <flux:checkbox wire:model.live="selectedNiches"
                                                  value="{{ $niche->value }}"
                                                  label="{{ $niche->label() }}" />
                                @endforeach
                            </div>
                        </div>
                    </flux:field>
                </div>

                @if($targetAccountType === App\Enums\AccountType::INFLUENCER)
                    <!-- Social Platform Filter -->
                    <div>
                        <flux:field>
                            <flux:label>Social Platforms</flux:label>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach(App\Enums\SocialPlatform::cases() as $platform)
                                    <flux:checkbox wire:model.live="selectedPlatforms"
                                                  value="{{ $platform->value }}"
                                                  label="{{ $platform->label() }}" />
                                @endforeach
                            </div>
                        </flux:field>
                    </div>

                    <!-- Follower Count Filter -->
                    <div>
                        <flux:field>
                            <flux:label>Follower Count Range</flux:label>
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-2">
                                    <flux:input wire:model.live.debounce.300ms="minFollowers"
                                               type="number" placeholder="Min followers" />
                                    <flux:input wire:model.live.debounce.300ms="maxFollowers"
                                               type="number" placeholder="Max followers" />
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    Leave blank for no limit
                                </div>
                            </div>
                        </flux:field>
                    </div>
                @endif

                <!-- Sort By -->
                <div>
                    <flux:field>
                        <flux:label>Sort By</flux:label>
                        <flux:select wire:model.live="sortBy">
                            <option value="relevance">Most Relevant</option>
                            <option value="name">Name (A-Z)</option>
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            @if($targetAccountType === App\Enums\AccountType::INFLUENCER)
                                <option value="followers">Most Followers</option>
                                <option value="engagement">Best Engagement</option>
                                <option value="quality">Content Quality</option>
                            @endif
                            @if(preg_match('/^\d{5}$/', $location))
                                <option value="distance">Distance (Nearest)</option>
                            @endif
                        </flux:select>
                    </flux:field>
                </div>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="col-span-2 lg:col-span-3">
            <!-- Loading State -->
            <div wire:loading class="w-full space-y-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <flux:icon.arrow-path class="w-5 h-5 animate-spin text-blue-600" />
                        <span class="text-gray-600 dark:text-gray-400">
                            @if(preg_match('/^\d{5}$/', $location))
                                Searching and calculating distances...
                            @else
                                Searching...
                            @endif
                        </span>
                    </div>
                </div>

                <!-- Loading Skeleton -->

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                        @for($i = 0; $i < 6; $i++)
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden animate-pulse">
                                <div class="h-32 bg-gray-300 dark:bg-gray-600"></div>
                                <div class="p-4 space-y-3">
                                    <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-3/4"></div>
                                    <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/2"></div>
                                    <div class="flex space-x-2">
                                        <div class="h-6 bg-gray-300 dark:bg-gray-600 rounded w-16"></div>
                                        <div class="h-6 bg-gray-300 dark:bg-gray-600 rounded w-20"></div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
            </div>

            <!-- Results Content -->
            <div wire:loading.remove>
                <!-- Results Header -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $results->total() }} {{ $searchingFor }} found
                            @if($isProximitySearch && $searchPostalCode)
                                within {{ $searchRadius }} miles of {{ $searchPostalCode->place_name }}, {{ $searchPostalCode->admin_code1 }} {{ $searchPostalCode->postal_code }}
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Results List/Grid -->
                @if($results->count() > 0)
                    <!-- Grid View -->
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                        @foreach($results as $user)
                            <livewire:influencer-card :user="$user" wire:key="user-{{ $user->id }}" />
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $results->links() }}
                    </div>

                @else
                    <!-- No Results -->
                    <div class="text-center py-12">
                        <flux:icon.magnifying-glass class="mx-auto h-12 w-12 text-gray-400 mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No {{ $searchingFor }} found</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">
                            Try adjusting your search criteria or clearing some filters.
                        </p>
                        <flux:button wire:click="clearFilters" variant="primary">
                            Clear All Filters
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>
    </div>



</div>
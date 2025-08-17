<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
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

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Filters Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Filters</h3>
                    <button wire:click="clearFilters"
                            class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-500">
                        Clear All
                    </button>
                </div>

                <!-- Search Input -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Search
                    </label>
                    <input type="text"
                           id="search"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search by name or email..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                </div>

                <!-- Location Filter -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Location
                    </label>
                    <input type="text"
                           id="location"
                           wire:model.live.debounce.300ms="location"
                           placeholder="Enter zipcode (e.g., 45066)..."
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        Enter a 5-digit zipcode for proximity search
                    </p>
                </div>

                <!-- Search Radius (only show when location is a zipcode) -->
                @if(preg_match('/^\d{5}$/', $location))
                    <div>
                        <label for="searchRadius" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Search Radius
                        </label>
                        <select wire:model.live="searchRadius"
                                id="searchRadius"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <option value="10">10 miles</option>
                            <option value="25">25 miles</option>
                            <option value="50">50 miles</option>
                            <option value="100">100 miles</option>
                            <option value="250">250 miles</option>
                        </select>
                    </div>
                @endif

                <!-- Niche/Industry Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                       Industries
                    </label>
                    <div class="space-y-2">
                        @foreach(App\Enums\BusinessIndustry::cases() as $niche)
                            <label class="flex items-center">
                                <input type="checkbox"
                                       wire:model.live="selectedNiches"
                                       value="{{ $niche->value }}"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $niche->label() }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                @if($targetAccountType === App\Enums\AccountType::INFLUENCER)
                    <!-- Social Platform Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Social Platforms
                        </label>
                        <div class="space-y-2">
                            @foreach(App\Enums\SocialPlatform::cases() as $platform)
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           wire:model.live="selectedPlatforms"
                                           value="{{ $platform->value }}"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $platform->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Follower Count Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Follower Count
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number"
                                   wire:model.live.debounce.300ms="minFollowers"
                                   placeholder="Min"
                                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                            <input type="number"
                                   wire:model.live.debounce.300ms="maxFollowers"
                                   placeholder="Max"
                                   class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                @endif

                <!-- Sort By -->
                <div>
                    <label for="sortBy" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Sort By
                    </label>
                    <select wire:model.live="sortBy"
                            id="sortBy"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        <option value="name">Name (A-Z)</option>
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        @if($targetAccountType === App\Enums\AccountType::INFLUENCER)
                            <option value="followers">Most Followers</option>
                        @endif
                        @if(preg_match('/^\d{5}$/', $location))
                            <option value="distance">Distance (Nearest)</option>
                        @endif
                    </select>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="lg:col-span-3">
            <!-- Loading State -->
            <div wire:loading class="w-full space-y-6">
                <!-- Loading Header -->
                <div class="w-full flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <svg class="animate-spin h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
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
                <div class="w-full bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @for($i = 0; $i < 4; $i++)
                            <div class="p-6 animate-pulse">
                                <div class="flex items-center space-x-4 w-full">
                                    <div class="w-12 h-12 bg-gray-300 dark:bg-gray-600 rounded-full flex-shrink-0"></div>
                                    <div class="flex-1 space-y-2 min-w-0">
                                        <div class="h-4 bg-gray-300 dark:bg-gray-600 rounded w-1/4"></div>
                                        <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-1/3"></div>
                                        <div class="flex space-x-4">
                                            <div class="h-3 bg-gray-300 dark:bg-gray-600 rounded w-16"></div>
                                            @if(preg_match('/^\d{5}$/', $location))
                                                <div class="h-3 bg-blue-300 dark:bg-blue-600 rounded w-24"></div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-2 flex-shrink-0">
                                        <div class="h-8 bg-gray-300 dark:bg-gray-600 rounded w-20"></div>
                                        <div class="h-8 bg-gray-300 dark:bg-gray-600 rounded w-16"></div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Results Content (hidden while loading) -->
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

                <!-- Results List -->
                @if($results->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($results as $user)
                            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4 flex-1">
                                        <!-- Profile Avatar -->
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-lg flex-shrink-0">
                                            {{ $user->initials() }}
                                        </div>

                                        <!-- Profile Information -->
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-3 mb-2">
                                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                                                    {{ $user->name }}
                                                </h3>

                                                @if($targetAccountType === App\Enums\AccountType::INFLUENCER && $user->influencerProfile)
                                                    @if($user->influencerProfile->creator_name)
                                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                                            ({{ $user->influencerProfile->creator_name }})
                                                        </span>
                                                    @endif
                                                @elseif($targetAccountType === App\Enums\AccountType::BUSINESS && $user->businessProfile)
                                                    @if($user->businessProfile->business_name)
                                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                                            ({{ $user->businessProfile->business_name }})
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>

                                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400 mb-2">
                                                <span>{{ $user->email }}</span>

                                                @if($targetAccountType === App\Enums\AccountType::INFLUENCER && $user->influencerProfile)
                                                    @if($user->influencerProfile->primary_zip_code)
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            </svg>
                                                            {{ $user->influencerProfile->primary_zip_code }}
                                                        </span>
                                                    @endif
                                                    @if(isset($user->distance) && $user->distance !== null)
                                                        <span class="flex items-center text-blue-600 dark:text-blue-400">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                            </svg>
                                                            {{ number_format($user->distance, 1) }} miles away
                                                        </span>
                                                    @endif
                                                @elseif($targetAccountType === App\Enums\AccountType::BUSINESS && $user->businessProfile)
                                                    @if($user->businessProfile->primary_zip_code)
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                            </svg>
                                                            {{ $user->businessProfile->primary_zip_code }}
                                                        </span>
                                                    @endif
                                                    @if(isset($user->distance) && $user->distance !== null)
                                                        <span class="flex items-center text-blue-600 dark:text-blue-400">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                                            </svg>
                                                            {{ number_format($user->distance, 1) }} miles away
                                                        </span>
                                                    @endif
                                                    @if($user->businessProfile->location_count > 1)
                                                        <span class="text-xs bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                            {{ $user->businessProfile->location_count }} locations
                                                        </span>
                                                    @endif
                                                @endif
                                            </div>

                                            <!-- Tags and Additional Info -->
                                            <div class="flex items-center space-x-3">
                                                @if($targetAccountType === App\Enums\AccountType::INFLUENCER && $user->influencerProfile)
                                                    @if($user->influencerProfile->primary_niche)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                                            {{ is_object($user->influencerProfile->primary_niche) ? $user->influencerProfile->primary_niche->label() : $user->influencerProfile->primary_niche }}
                                                        </span>
                                                    @endif

                                                    @if($user->socialMediaAccounts->count() > 0)
                                                        <div class="flex items-center space-x-3">
                                                            @foreach($user->socialMediaAccounts as $account)
                                                                <div class="flex items-center space-x-1 text-sm">
                                                                    <span class="text-gray-500 dark:text-gray-400">{{ is_object($account->platform) ? $account->platform->label() : $account->platform }}:</span>
                                                                    <span class="font-medium text-gray-900 dark:text-white">{{ number_format($account->follower_count) }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                @elseif($targetAccountType === App\Enums\AccountType::BUSINESS && $user->businessProfile)
                                                    @if($user->businessProfile->industry)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                                            {{ is_object($user->businessProfile->industry) ? $user->businessProfile->industry->label() : $user->businessProfile->industry }}
                                                        </span>
                                                    @endif

                                                    @if($user->businessProfile->websites && count($user->businessProfile->websites) > 0)
                                                        <div class="flex items-center space-x-1 text-sm">
                                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                                            </svg>
                                                            <a href="{{ $user->businessProfile->websites[0] }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                                {{ parse_url($user->businessProfile->websites[0], PHP_URL_HOST) }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex items-center space-x-3 ml-4">
                                        <flux:button variant="outline" size="sm">
                                            View Details
                                        </flux:button>
                                        <flux:button variant="primary" size="sm">
                                            @if($targetAccountType === App\Enums\AccountType::INFLUENCER)
                                                Contact
                                            @else
                                                View Profile
                                            @endif
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $results->links() }}
                </div>
            @else
                <!-- No Results -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No {{ $searchingFor }} found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Try adjusting your search criteria or clearing some filters.
                    </p>
                    <div class="mt-6">
                        <flux:button variant="primary" wire:click="clearFilters">
                            Clear All Filters
                        </flux:button>
                    </div>
                </div>
            @endif
            </div> <!-- End wire:loading.remove -->
        </div>
    </div>
</div>
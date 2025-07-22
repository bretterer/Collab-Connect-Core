<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-pink-500 to-purple-600 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">
                        Discover Campaigns ✨
                    </h1>
                    <p class="text-pink-100 text-lg">
                        Find the perfect collaboration opportunities that match your profile and interests.
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Filters</h3>
                <!-- Quick Actions Row -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            🔍 Search Campaigns
                        </label>
                        <input
                            type="text"
                            id="search"
                            wire:model.live.debounce.300ms="search"
                            placeholder="Search by campaign goal or description..."
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label for="sortBy" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📊 Sort By
                        </label>
                        <select
                            id="sortBy"
                            wire:model.live="sortBy"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="match_score">Best Match</option>
                            <option value="newest">Newest First</option>
                            <option value="budget">Highest Budget</option>
                            <option value="deadline">Application Deadline</option>
                        </select>
                    </div>

                    <!-- Results Per Page -->
                    <div>
                        <label for="perPage" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📄 Results Per Page
                        </label>
                        <select
                            id="perPage"
                            wire:model.live="perPage"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="6">6 per page</option>
                            <option value="12">12 per page</option>
                            <option value="24">24 per page</option>
                            <option value="48">48 per page</option>
                        </select>
                    </div>

                    <!-- Clear Filters -->
                    <div class="flex items-end">
                        <button
                            type="button"
                            wire:click="clearFilters"
                            class="w-full px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-md transition-colors duration-200"
                        >
                            🗑️ Clear All Filters
                        </button>
                    </div>
                </div>

                <!-- Filter Categories Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Industries Filter -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            🏭 Industries
                            <span class="ml-2 text-xs text-gray-500">({{ count($selectedNiches) }} selected)</span>
                        </h4>
                        <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto">
                            @foreach($nicheOptions as $niche)
                                <label class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded cursor-pointer">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedNiches"
                                        value="{{ $niche->value }}"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $niche->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if(count($selectedNiches) > 0)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <button
                                    type="button"
                                    wire:click="$set('selectedNiches', [])"
                                    class="text-xs text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                >
                                    Clear Industries
                                </button>
                            </div>
                        @endif
                    </div>

                    <!-- Campaign Types Filter -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            📢 Campaign Types
                            <span class="ml-2 text-xs text-gray-500">({{ count($selectedCampaignTypes) }} selected)</span>
                        </h4>
                        <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto">
                            @foreach($campaignTypeOptions as $campaignType)
                                <label class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-gray-600 rounded cursor-pointer">
                                    <input
                                        type="checkbox"
                                        wire:model.live="selectedCampaignTypes"
                                        value="{{ $campaignType->value }}"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                    >
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $campaignType->label() }}</span>
                                </label>
                            @endforeach
                        </div>
                        @if(count($selectedCampaignTypes) > 0)
                            <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <button
                                    type="button"
                                    wire:click="$set('selectedCampaignTypes', [])"
                                    class="text-xs text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300"
                                >
                                    Clear Campaign Types
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaigns Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($campaigns as $campaign)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200">
                    <!-- Match Score Badge -->
                    <div class="relative">
                        <div class="absolute top-3 right-3 z-10">
                            <div class="flex items-center space-x-1 bg-white dark:bg-gray-700 rounded-full px-3 py-1 shadow-md">
                                <div class="w-2 h-2 rounded-full {{ $campaign->match_score >= 80 ? 'bg-green-500' : ($campaign->match_score >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"></div>
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    {{ number_format($campaign->match_score, 0) }}% Match
                                </span>
                            </div>
                        </div>

                        <!-- Campaign Image Placeholder -->
                        <div class="h-48 bg-gradient-to-br from-blue-400 to-purple-500 rounded-t-lg flex items-center justify-center">
                            <svg class="w-12 h-12 text-white opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>

                    <!-- Campaign Content -->
                    <div class="p-6">
                        <!-- Business Info -->
                        <div class="flex items-center mb-3">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                {{ substr($campaign->user->businessProfile?->business_name ?? $campaign->user->name, 0, 2) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $campaign->user->businessProfile?->business_name ?? $campaign->user->name }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $campaign->user->businessProfile?->industry?->label() ?? 'Business' }}
                                </p>
                            </div>
                        </div>

                        <!-- Campaign Title -->
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                            {{ Str::limit($campaign->campaign_goal, 60) }}
                        </h3>

                        <!-- Campaign Description -->
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-3">
                            {{ Str::limit($campaign->campaign_description, 120) }}
                        </p>

                        <!-- Campaign Details -->
                        <div class="space-y-2 mb-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Campaign Type:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $campaign->campaign_type?->label() ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Compensation:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $campaign->compensation_display }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Influencers Needed:</span>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $campaign->influencer_count }}</span>
                            </div>
                            @if($campaign->application_deadline)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Deadline:</span>
                                    <span class="font-medium {{ $campaign->application_deadline->isPast() ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                                        {{ $campaign->application_deadline->format('M j, Y') }}
                                    </span>
                                </div>
                            @endif
                            @if($campaign->target_zip_code)
                                <div class="flex items-center justify-between text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Location:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $campaign->target_zip_code }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <a href="{{ route('campaigns.edit', $campaign) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors duration-200 text-center">
                                View Details
                            </a>
                            <button class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors duration-200">
                                Apply Now
                            </button>
                        </div>
                    </div>

                    @if($showDebug)
                        <!-- Debug Information -->
                        <div class="mt-4 p-4 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600">
                            <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">🔍 Debug Information (Local Only)</h4>

                            @php
                                $debugData = $this->getDebugData($campaign);
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
                                <!-- Influencer Data -->
                                <div>
                                    <h5 class="font-medium text-gray-600 dark:text-gray-400 mb-2">👤 Influencer Profile</h5>
                                    <div class="space-y-1">
                                        <div><span class="font-medium">Name:</span> {{ $debugData['influencer']['name'] }}</div>
                                        <div><span class="font-medium">Email:</span> {{ $debugData['influencer']['email'] }}</div>
                                        <div><span class="font-medium">Primary Niche:</span> {{ $debugData['influencer']['primary_niche'] }}</div>
                                        <div><span class="font-medium">Zip Code:</span> {{ $debugData['influencer']['primary_zip_code'] }}</div>
                                        <div><span class="font-medium">Followers:</span> {{ $debugData['influencer']['follower_count'] }}</div>
                                    </div>
                                </div>

                                <!-- Campaign Data -->
                                <div>
                                    <h5 class="font-medium text-gray-600 dark:text-gray-400 mb-2">📋 Campaign Data</h5>
                                    <div class="space-y-1">
                                        <div><span class="font-medium">Business:</span> {{ $debugData['campaign']['business_name'] }}</div>
                                        <div><span class="font-medium">Industry:</span> {{ $debugData['campaign']['business_industry'] }}</div>
                                        <div><span class="font-medium">Type:</span> {{ $debugData['campaign']['campaign_type'] }}</div>
                                        <div><span class="font-medium">Compensation:</span> {{ $debugData['campaign']['compensation_type'] }}</div>
                                        <div><span class="font-medium">Amount:</span> {{ $debugData['campaign']['compensation_amount'] }}</div>
                                        <div><span class="font-medium">Target Zip:</span> {{ $debugData['campaign']['target_zip_code'] }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Score Breakdown -->
                            <div class="mt-4">
                                <h5 class="font-medium text-gray-600 dark:text-gray-400 mb-2">📊 Score Breakdown</h5>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <div class="bg-white dark:bg-gray-700 p-2 rounded border">
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-blue-600">{{ $debugData['scores']['location']['raw'] }}%</div>
                                            <div class="text-xs text-gray-500">Location</div>
                                            <div class="text-xs text-gray-400">({{ $debugData['scores']['location']['weight'] }})</div>
                                            <div class="text-xs text-gray-400">→ {{ $debugData['scores']['location']['weighted'] }}</div>
                                        </div>
                                    </div>
                                    <div class="bg-white dark:bg-gray-700 p-2 rounded border">
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-green-600">{{ $debugData['scores']['niche']['raw'] }}%</div>
                                            <div class="text-xs text-gray-500">Niche</div>
                                            <div class="text-xs text-gray-400">({{ $debugData['scores']['niche']['weight'] }})</div>
                                            <div class="text-xs text-gray-400">→ {{ $debugData['scores']['niche']['weighted'] }}</div>
                                        </div>
                                    </div>
                                    <div class="bg-white dark:bg-gray-700 p-2 rounded border">
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-purple-600">{{ $debugData['scores']['campaign_type']['raw'] }}%</div>
                                            <div class="text-xs text-gray-500">Type</div>
                                            <div class="text-xs text-gray-400">({{ $debugData['scores']['campaign_type']['weight'] }})</div>
                                            <div class="text-xs text-gray-400">→ {{ $debugData['scores']['campaign_type']['weighted'] }}</div>
                                        </div>
                                    </div>
                                    <div class="bg-white dark:bg-gray-700 p-2 rounded border">
                                        <div class="text-center">
                                            <div class="text-lg font-bold text-orange-600">{{ $debugData['scores']['compensation']['raw'] }}%</div>
                                            <div class="text-xs text-gray-500">Compensation</div>
                                            <div class="text-xs text-gray-400">({{ $debugData['scores']['compensation']['weight'] }})</div>
                                            <div class="text-xs text-gray-400">→ {{ $debugData['scores']['compensation']['weighted'] }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 text-center">
                                    <div class="text-2xl font-bold text-gray-800 dark:text-white">
                                        Total: {{ $debugData['scores']['total'] }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No campaigns found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Try adjusting your filters or check back later for new opportunities.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Results Summary and Pagination -->
        @if($campaigns->count() > 0)
            <div class="mt-8">
                <!-- Results Summary -->
                <div class="text-center mb-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Showing {{ $campaigns->firstItem() ?? 0 }}-{{ $campaigns->lastItem() ?? 0 }} of {{ $campaigns->total() }} campaign{{ $campaigns->total() !== 1 ? 's' : '' }}
                        @if($search || !empty($selectedNiches) || !empty($selectedCampaignTypes))
                            matching your criteria
                        @endif
                    </p>
                </div>

                <!-- Pagination Controls -->
                <div class="flex items-center justify-center">
                    <nav class="flex items-center space-x-2" role="navigation" aria-label="Pagination Navigation">
                        <!-- Previous Page -->
                        @if($campaigns->onFirstPage())
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 cursor-default rounded-md">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @else
                            <button wire:click="previousPage" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        @endif

                        <!-- Page Numbers -->
                        <div class="flex items-center space-x-1">
                                                    @foreach(range(1, min(5, $campaigns->lastPage())) as $page)
                            @if($page == $campaigns->currentPage())
                                <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-md">
                                    {{ $page }}
                                </span>
                            @else
                                <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">
                                    {{ $page }}
                                </button>
                            @endif
                        @endforeach

                            @if($campaigns->lastPage() > 5)
                                @if($campaigns->currentPage() < $campaigns->lastPage() - 2)
                                    <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md">
                                        ...
                                    </span>
                                @endif

                                                            @if($campaigns->currentPage() >= $campaigns->lastPage() - 2)
                                @foreach(range(max(6, $campaigns->lastPage() - 1), $campaigns->lastPage()) as $page)
                                    @if($page == $campaigns->currentPage())
                                        <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 rounded-md">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">
                                            {{ $page }}
                                        </button>
                                    @endif
                                @endforeach
                            @endif
                            @endif
                        </div>

                        <!-- Next Page -->
                        @if($campaigns->hasMorePages())
                            <button wire:click="nextPage" wire:loading.attr="disabled" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        @else
                            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 cursor-default rounded-md">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        @endif
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>
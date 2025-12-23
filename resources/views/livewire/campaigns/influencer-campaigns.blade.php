<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-pink-500 to-purple-600 dark:from-pink-600 dark:to-purple-700 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">
                        Discover Campaigns
                    </h1>
                    <p class="text-pink-100 text-lg">
                        Find the perfect collaboration opportunities that match your profile and interests.
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <flux:tabs wire:model.live="activeTab" variant="segmented">
                <flux:tab name="all" icon="magnifying-glass">
                    All Campaigns
                </flux:tab>
                <flux:tab name="saved" icon="heart">
                    Saved
                    @if($savedCount > 0)
                        <flux:badge size="sm" color="pink" class="ml-1">{{ $savedCount }}</flux:badge>
                    @endif
                </flux:tab>
                <flux:tab name="hidden" icon="eye-slash">
                    Hidden
                    @if($hiddenCount > 0)
                        <flux:badge size="sm" color="zinc" class="ml-1">{{ $hiddenCount }}</flux:badge>
                    @endif
                </flux:tab>
            </flux:tabs>
        </div>

        <!-- Filters (not shown on hidden tab) -->
        @if($activeTab !== 'hidden')
        <div class="bg-white dark:bg-zinc-800 shadow rounded-lg mb-6" x-data="{ filtersOpen: false }">
            <div class="p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-white">Filters</h3>
                        <!-- Active Filter Badges (when collapsed) -->
                        <div x-show="!filtersOpen" class="flex items-center gap-2 flex-wrap">
                            @if($search)
                            <flux:badge color="blue" size="sm">
                                Search: {{ Str::limit($search, 15) }}
                                <button wire:click="$set('search', '')" class="ml-1 hover:text-blue-200">&times;</button>
                            </flux:badge>
                            @endif
                            @if(count($selectedNiches) > 0)
                            <flux:badge color="green" size="sm">
                                {{ count($selectedNiches) }} Industry{{ count($selectedNiches) > 1 ? 'ies' : '' }}
                                <button wire:click="$set('selectedNiches', [])" class="ml-1 hover:text-green-200">&times;</button>
                            </flux:badge>
                            @endif
                            @if(count($selectedCampaignTypes) > 0)
                            <flux:badge color="purple" size="sm">
                                {{ count($selectedCampaignTypes) }} Type{{ count($selectedCampaignTypes) > 1 ? 's' : '' }}
                                <button wire:click="$set('selectedCampaignTypes', [])" class="ml-1 hover:text-purple-200">&times;</button>
                            </flux:badge>
                            @endif
                            @if($sortBy !== 'match_score')
                            <flux:badge color="amber" size="sm">
                                Sort: {{ ucfirst(str_replace('_', ' ', $sortBy)) }}
                            </flux:badge>
                            @endif

                            @if($search || count($selectedNiches) > 0 || count($selectedCampaignTypes) > 0 || $sortBy !== 'match_score')
                            <flux:button wire:click="clearFilters" size="sm" variant="ghost">
                                Clear All
                            </flux:button>
                            @endif
                        </div>
                    </div>
                    <button
                        @click="filtersOpen = !filtersOpen"
                        type="button"
                        class="inline-flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">
                        <span x-text="filtersOpen ? 'Hide Filters' : 'Show Filters'"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                             class="w-4 h-4 transition-transform duration-200"
                             :class="{ 'rotate-180': filtersOpen }">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>

                <div x-show="filtersOpen" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
                    <!-- Quick Actions Row -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <!-- Search -->
                        <div>
                            <flux:input
                                wire:model.live.debounce.300ms="search"
                                placeholder="Search campaigns..."
                                icon="magnifying-glass"
                                clearable />
                        </div>

                        <!-- Sort By -->
                        <div>
                            <flux:select wire:model.live="sortBy">
                                <flux:select.option value="match_score">Best Match</flux:select.option>
                                <flux:select.option value="newest">Newest First</flux:select.option>
                                <flux:select.option value="compensation">Highest Compensation</flux:select.option>
                                <flux:select.option value="deadline">Application Deadline</flux:select.option>
                            </flux:select>
                        </div>

                        <!-- Results Per Page -->
                        <div>
                            <flux:select wire:model.live="perPage">
                                <flux:select.option value="6">6 per page</flux:select.option>
                                <flux:select.option value="12">12 per page</flux:select.option>
                                <flux:select.option value="24">24 per page</flux:select.option>
                                <flux:select.option value="48">48 per page</flux:select.option>
                            </flux:select>
                        </div>

                        <!-- Clear Filters -->
                        <div class="flex items-end">
                            <flux:button
                                wire:click="clearFilters"
                                variant="filled"
                                class="w-full">
                                Clear All Filters
                            </flux:button>
                        </div>
                    </div>

                    <!-- Filter Categories Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Industries Filter -->
                        <div class="bg-zinc-50 dark:bg-zinc-700/50 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3 flex items-center">
                                <flux:icon name="building-office" class="w-4 h-4 mr-2" />
                                Industries
                                <span class="ml-2 text-xs text-zinc-500">({{ count($selectedNiches) }} selected)</span>
                            </h4>
                            <div class="grid grid-cols-2 gap-2 max-h-48 overflow-y-auto">
                                @foreach($nicheOptions as $niche)
                                <flux:checkbox
                                    wire:model.live="selectedNiches"
                                    value="{{ $niche->value }}"
                                    label="{{ $niche->label() }}" />
                                @endforeach
                            </div>
                            @if(count($selectedNiches) > 0)
                            <div class="mt-3 pt-3 border-t border-zinc-200 dark:border-zinc-600">
                                <flux:button
                                    wire:click="$set('selectedNiches', [])"
                                    variant="ghost"
                                    size="sm">
                                    Clear Industries
                                </flux:button>
                            </div>
                            @endif
                        </div>

                        <!-- Campaign Types Filter -->
                        <div class="bg-zinc-50 dark:bg-zinc-700/50 rounded-lg p-4">
                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-white mb-3 flex items-center">
                                <flux:icon name="megaphone" class="w-4 h-4 mr-2" />
                                Campaign Types
                                <span class="ml-2 text-xs text-zinc-500">({{ count($selectedCampaignTypes) }} selected)</span>
                            </h4>
                            <div class="grid grid-cols-1 gap-2 max-h-48 overflow-y-auto">
                                @foreach($campaignTypeOptions as $campaignType)
                                <flux:checkbox
                                    wire:model.live="selectedCampaignTypes"
                                    value="{{ $campaignType->value }}"
                                    label="{{ $campaignType->label() }}" />
                                @endforeach
                            </div>
                            @if(count($selectedCampaignTypes) > 0)
                            <div class="mt-3 pt-3 border-t border-zinc-200 dark:border-zinc-600">
                                <flux:button
                                    wire:click="$set('selectedCampaignTypes', [])"
                                    variant="ghost"
                                    size="sm">
                                    Clear Campaign Types
                                </flux:button>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Hidden Tab Message -->
        @if($activeTab === 'hidden')
        <flux:callout icon="eye-slash" class="mb-6">
            <flux:callout.heading>Hidden Campaigns</flux:callout.heading>
            <flux:callout.text>These campaigns won't appear in your main feed. Click "Restore" to show them again.</flux:callout.text>
        </flux:callout>
        @endif

        <!-- Campaigns Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($campaigns as $campaign)
            @php
                $isSaved = in_array($campaign->id, $savedCampaignIds);
                $isHidden = in_array($campaign->id, $hiddenCampaignIds);
            @endphp
            <div wire:key="campaign-{{ $campaign->id }}" class="bg-white dark:bg-zinc-800 rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200 flex flex-col">
                <!-- Match Score Badge & Actions -->
                <div class="relative">
                    <div class="absolute top-3 left-3 z-10 flex flex-col gap-1">
                        <div class="flex items-center gap-1 bg-white dark:bg-zinc-700 rounded-full px-3 py-1 shadow-md">
                            <div class="w-2 h-2 rounded-full {{ $campaign->match_score >= 80 ? 'bg-green-500' : ($campaign->match_score >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"></div>
                            <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                                {{ number_format($campaign->match_score, 0) }}% Match
                            </span>
                        </div>
                        @if($campaign->isBoosted())
                        <div class="flex items-center gap-1 bg-gradient-to-r from-amber-500 to-orange-500 rounded-full px-3 py-1 shadow-md">
                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span class="text-xs font-semibold text-white">Boosted</span>
                        </div>
                        @endif
                    </div>

                    <!-- Actions Dropdown -->
                    <div class="absolute top-3 right-3 z-10">
                        <flux:dropdown position="bottom" align="end">
                            <flux:button variant="filled" size="sm" icon="ellipsis-vertical" />
                            <flux:menu>
                                <flux:menu.item wire:click="openQuickView({{ $campaign->id }})" icon="eye">
                                    Quick View
                                </flux:menu.item>
                                <flux:menu.item href="{{ route('campaigns.show', $campaign) }}" icon="arrow-top-right-on-square">
                                    View Full Details
                                </flux:menu.item>
                                <flux:menu.separator />
                                @if($activeTab === 'hidden')
                                <flux:menu.item wire:click="unhideCampaign({{ $campaign->id }})" icon="eye">
                                    Restore to Feed
                                </flux:menu.item>
                                @else
                                <flux:menu.item wire:click="toggleSaveCampaign({{ $campaign->id }})" icon="{{ $isSaved ? 'heart' : 'heart' }}">
                                    {{ $isSaved ? 'Remove from Saved' : 'Save for Later' }}
                                </flux:menu.item>
                                <flux:menu.item wire:click="hideCampaign({{ $campaign->id }})" icon="eye-slash" variant="danger">
                                    Not Interested
                                </flux:menu.item>
                                @endif
                            </flux:menu>
                        </flux:dropdown>
                    </div>

                    <!-- Campaign Image Placeholder -->
                    <div class="h-32 bg-gradient-to-br from-blue-400 to-purple-500 dark:from-blue-500 dark:to-purple-600 rounded-t-lg flex items-center justify-center">
                        <flux:icon name="document-text" class="w-10 h-10 text-white/75" />
                    </div>

                    <!-- Save indicator -->
                    @if($isSaved && $activeTab !== 'saved')
                    <div class="absolute bottom-2 right-2">
                        <flux:badge color="pink" size="sm" icon="heart">Saved</flux:badge>
                    </div>
                    @endif
                </div>

                <!-- Campaign Content -->
                <div class="p-4 flex-1 flex flex-col">
                    <!-- Business Info -->
                    <div class="flex items-center mb-3">
                        @if(auth()->user()->profile->subscribed('default'))
                        <flux:avatar size="sm" name="{{ $campaign->business->name ?? 'N/A' }}" />
                        <div class="ml-3">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white">
                                {{ $campaign->business->name ?? 'Unknown Business' }}
                            </p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                {{ $campaign->business->industry?->label() ?? 'Business' }}
                            </p>
                        </div>
                        @else
                        <flux:avatar size="sm" name="??" />
                        <div class="ml-3">
                            <p class="text-sm font-medium text-zinc-900 dark:text-white">
                                Subscribe to View
                            </p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                                Business Info Hidden
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Campaign Title -->
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-white mb-2 line-clamp-2">
                        {{ Str::limit($campaign->project_name ?: $campaign->campaign_goal, 60) }}
                    </h3>

                    <!-- Campaign Description -->
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mb-4 line-clamp-2 flex-1">
                        {{ Str::limit($campaign->campaign_description, 100) }}
                    </p>

                    <!-- Campaign Details -->
                    <div class="space-y-2 mb-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500 dark:text-zinc-400">Compensation:</span>
                            <flux:badge color="green" size="sm">{{ $campaign->compensation_display }}</flux:badge>
                        </div>
                        @if($campaign->application_deadline)
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500 dark:text-zinc-400">Deadline:</span>
                            <span class="font-medium {{ $campaign->application_deadline->isPast() ? 'text-red-600' : 'text-zinc-900 dark:text-white' }}">
                                {{ $campaign->application_deadline->format('M j, Y') }}
                            </span>
                        </div>
                        @endif
                        @if($campaign->target_zip_code)
                        <div class="flex items-center justify-between">
                            <span class="text-zinc-500 dark:text-zinc-400">Location:</span>
                            <span class="font-medium text-zinc-900 dark:text-white">{{ $campaign->target_zip_code }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    @if($activeTab !== 'hidden')
                    <div class="flex gap-2 mt-auto">
                        <flux:button wire:click="openQuickView({{ $campaign->id }})" variant="ghost" class="flex-1">
                            Quick View
                        </flux:button>
                        @if(auth()->user()->profile->subscribed('default'))
                        @livewire('campaigns.apply-to-campaign', [
                            'campaign' => $campaign,
                            'buttonText' => 'Apply',
                            'existingApplication' => $userApplications->get($campaign->id),
                            'applicationPreloaded' => true
                        ], key('apply-'.$campaign->id))
                        @else
                        <flux:button href="{{ route('billing') }}" variant="primary" class="flex-1">
                            Subscribe
                        </flux:button>
                        @endif
                    </div>
                    @else
                    <div class="mt-auto">
                        <flux:button wire:click="unhideCampaign({{ $campaign->id }})" variant="filled" class="w-full" icon="eye">
                            Restore to Feed
                        </flux:button>
                    </div>
                    @endif
                </div>

                @if($showDebug)
                <!-- Debug Information -->
                <div class="mt-4 p-4 bg-zinc-100 dark:bg-zinc-900 rounded-b-lg border-t border-zinc-200 dark:border-zinc-700">
                    <h4 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-3">Debug Information (Local Only)</h4>

                    @php
                    $debugData = $this->getDebugData($campaign);
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs text-zinc-700 dark:text-zinc-300">
                        <!-- Influencer Data -->
                        <div>
                            <h5 class="font-medium text-zinc-600 dark:text-zinc-400 mb-2">Influencer Profile</h5>
                            <div class="space-y-1">
                                <div><span class="font-medium">Name:</span> {{ $debugData['influencer']['name'] }}</div>
                                <div><span class="font-medium">Email:</span> {{ $debugData['influencer']['email'] }}</div>
                                <div><span class="font-medium">Primary Industry:</span> {{ $debugData['influencer']['primary_industry'] }}</div>
                                <div><span class="font-medium">Zip Code:</span> {{ $debugData['influencer']['primary_zip_code'] }}</div>
                                <div><span class="font-medium">Followers:</span> {{ $debugData['influencer']['follower_count'] }}</div>
                            </div>
                        </div>

                        <!-- Campaign Data -->
                        <div>
                            <h5 class="font-medium text-zinc-600 dark:text-zinc-400 mb-2">Campaign Data</h5>
                            <div class="space-y-1">
                                <div><span class="font-medium">Business:</span> {{ $debugData['campaign']['name'] }}</div>
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
                        <h5 class="font-medium text-zinc-600 dark:text-zinc-400 mb-2">Score Breakdown</h5>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <div class="bg-white dark:bg-zinc-800 p-2 rounded border border-zinc-200 dark:border-zinc-700">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-blue-600">{{ $debugData['scores']['location']['raw'] }}%</div>
                                    <div class="text-xs text-zinc-500">Location</div>
                                    <div class="text-xs text-zinc-400">({{ $debugData['scores']['location']['weight'] }})</div>
                                    <div class="text-xs text-zinc-400">{{ $debugData['scores']['location']['weighted'] }}</div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-zinc-800 p-2 rounded border border-zinc-200 dark:border-zinc-700">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-green-600">{{ $debugData['scores']['industry']['raw'] }}%</div>
                                    <div class="text-xs text-zinc-500">Industry</div>
                                    <div class="text-xs text-zinc-400">({{ $debugData['scores']['industry']['weight'] }})</div>
                                    <div class="text-xs text-zinc-400">{{ $debugData['scores']['industry']['weighted'] }}</div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-zinc-800 p-2 rounded border border-zinc-200 dark:border-zinc-700">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-purple-600">{{ $debugData['scores']['campaign_type']['raw'] }}%</div>
                                    <div class="text-xs text-zinc-500">Type</div>
                                    <div class="text-xs text-zinc-400">({{ $debugData['scores']['campaign_type']['weight'] }})</div>
                                    <div class="text-xs text-zinc-400">{{ $debugData['scores']['campaign_type']['weighted'] }}</div>
                                </div>
                            </div>
                            <div class="bg-white dark:bg-zinc-800 p-2 rounded border border-zinc-200 dark:border-zinc-700">
                                <div class="text-center">
                                    <div class="text-lg font-bold text-orange-600">{{ $debugData['scores']['compensation']['raw'] }}%</div>
                                    <div class="text-xs text-zinc-500">Compensation</div>
                                    <div class="text-xs text-zinc-400">({{ $debugData['scores']['compensation']['weight'] }})</div>
                                    <div class="text-xs text-zinc-400">{{ $debugData['scores']['compensation']['weighted'] }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <div class="text-2xl font-bold text-zinc-800 dark:text-white">
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
                    @if($activeTab === 'saved')
                    <flux:icon name="heart" class="mx-auto h-12 w-12 text-zinc-400" />
                    <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-white">No saved campaigns</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Save campaigns you're interested in to review them later.
                    </p>
                    <div class="mt-4">
                        <flux:button wire:click="setActiveTab('all')" variant="primary">
                            Browse Campaigns
                        </flux:button>
                    </div>
                    @elseif($activeTab === 'hidden')
                    <flux:icon name="eye-slash" class="mx-auto h-12 w-12 text-zinc-400" />
                    <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-white">No hidden campaigns</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Campaigns you mark as "Not Interested" will appear here.
                    </p>
                    @else
                    <flux:icon name="document-text" class="mx-auto h-12 w-12 text-zinc-400" />
                    <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-white">No campaigns found</h3>
                    <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                        Try adjusting your filters or check back later for new opportunities.
                    </p>
                    @endif
                </div>
            </div>
            @endforelse
        </div>

        <!-- Results Summary and Pagination -->
        @if($campaigns->count() > 0)
        <div class="mt-8">
            <!-- Results Summary -->
            <div class="text-center mb-6">
                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                    Showing {{ $campaigns->firstItem() ?? 0 }}-{{ $campaigns->lastItem() ?? 0 }} of {{ $campaigns->total() }} campaign{{ $campaigns->total() !== 1 ? 's' : '' }}
                    @if($search || !empty($selectedNiches) || !empty($selectedCampaignTypes))
                    matching your criteria
                    @endif
                </p>
            </div>

            <!-- Pagination Controls -->
            <div class="flex items-center justify-center">
                <nav class="flex items-center gap-2" role="navigation" aria-label="Pagination Navigation">
                    <!-- Previous Page -->
                    @if($campaigns->onFirstPage())
                    <flux:button disabled variant="ghost" icon="chevron-left" />
                    @else
                    <flux:button wire:click="previousPage" wire:loading.attr="disabled" variant="ghost" icon="chevron-left" />
                    @endif

                    <!-- Page Numbers -->
                    <div class="flex items-center gap-1">
                        @foreach(range(1, min(5, $campaigns->lastPage())) as $page)
                        @if($page == $campaigns->currentPage())
                        <flux:button variant="primary">{{ $page }}</flux:button>
                        @else
                        <flux:button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled" variant="ghost">{{ $page }}</flux:button>
                        @endif
                        @endforeach

                        @if($campaigns->lastPage() > 5)
                        @if($campaigns->currentPage() < $campaigns->lastPage() - 2)
                        <span class="px-2 text-zinc-500">...</span>
                        @endif

                        @if($campaigns->currentPage() >= $campaigns->lastPage() - 2)
                        @foreach(range(max(6, $campaigns->lastPage() - 1), $campaigns->lastPage()) as $page)
                        @if($page == $campaigns->currentPage())
                        <flux:button variant="primary">{{ $page }}</flux:button>
                        @else
                        <flux:button wire:click="gotoPage({{ $page }})" wire:loading.attr="disabled" variant="ghost">{{ $page }}</flux:button>
                        @endif
                        @endforeach
                        @endif
                        @endif
                    </div>

                    <!-- Next Page -->
                    @if($campaigns->hasMorePages())
                    <flux:button wire:click="nextPage" wire:loading.attr="disabled" variant="ghost" icon="chevron-right" />
                    @else
                    <flux:button disabled variant="ghost" icon="chevron-right" />
                    @endif
                </nav>
            </div>
        </div>
        @endif
    </div>

    <!-- Quick View Modal -->
    <flux:modal wire:model="showQuickViewModal" name="quick-view-campaign" class="md:w-2xl" @close="$wire.closeQuickView()">
        @if($quickViewCampaign)
        <div class="space-y-6">
            <!-- Header -->
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div class="flex items-center gap-1 bg-zinc-100 dark:bg-zinc-700 rounded-full px-3 py-1">
                        <div class="w-2 h-2 rounded-full {{ $quickViewCampaign->match_score >= 80 ? 'bg-green-500' : ($quickViewCampaign->match_score >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"></div>
                        <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                            {{ number_format($quickViewCampaign->match_score, 0) }}% Match
                        </span>
                    </div>
                    @if($quickViewCampaign->isBoosted())
                    <div class="flex items-center gap-1 bg-gradient-to-r from-amber-500 to-orange-500 rounded-full px-3 py-1">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <span class="text-xs font-semibold text-white">Boosted</span>
                    </div>
                    @endif
                </div>
                <flux:heading size="xl">{{ $quickViewCampaign->project_name ?: $quickViewCampaign->campaign_goal }}</flux:heading>
                @if(auth()->user()->profile->subscribed('default'))
                <flux:text class="mt-1">
                    by <strong>{{ $quickViewCampaign->business->name }}</strong>
                    <flux:badge size="sm" class="ml-2">{{ $quickViewCampaign->business->industry?->label() ?? 'Business' }}</flux:badge>
                </flux:text>
                @endif
            </div>

            <!-- Description -->
            <div>
                <flux:heading size="sm" class="mb-2">About this Campaign</flux:heading>
                <flux:text>{{ $quickViewCampaign->campaign_description }}</flux:text>
            </div>

            <!-- Key Details -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4">
                    <flux:text class="text-xs text-zinc-500 mb-1">Compensation</flux:text>
                    <flux:badge color="green">{{ $quickViewCampaign->compensation_display }}</flux:badge>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4">
                    <flux:text class="text-xs text-zinc-500 mb-1">Influencers Needed</flux:text>
                    <p class="font-semibold text-zinc-900 dark:text-white">{{ $quickViewCampaign->influencer_count }}</p>
                </div>
                @if($quickViewCampaign->application_deadline)
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4">
                    <flux:text class="text-xs text-zinc-500 mb-1">Application Deadline</flux:text>
                    <p class="font-semibold {{ $quickViewCampaign->application_deadline->isPast() ? 'text-red-600' : 'text-zinc-900 dark:text-white' }}">
                        {{ $quickViewCampaign->application_deadline->format('M j, Y') }}
                    </p>
                </div>
                @endif
                @if($quickViewCampaign->target_zip_code)
                <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-4">
                    <flux:text class="text-xs text-zinc-500 mb-1">Location</flux:text>
                    <p class="font-semibold text-zinc-900 dark:text-white">{{ $quickViewCampaign->target_zip_code }}</p>
                </div>
                @endif
            </div>

            <!-- Campaign Types -->
            @if($quickViewCampaign->campaign_type && count($quickViewCampaign->campaign_type) > 0)
            <div>
                <flux:heading size="sm" class="mb-2">Campaign Types</flux:heading>
                <div class="flex flex-wrap gap-2">
                    @foreach($quickViewCampaign->campaign_type as $type)
                    <flux:badge>{{ $type->label() }}</flux:badge>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Deliverables -->
            @if($quickViewCampaign->deliverables && count($quickViewCampaign->deliverables) > 0)
            <div>
                <flux:heading size="sm" class="mb-2">Deliverables</flux:heading>
                <div class="flex flex-wrap gap-2">
                    @foreach($quickViewCampaign->deliverables as $deliverable)
                    @php
                        $deliverableLabel = is_string($deliverable)
                            ? ucwords(str_replace('_', ' ', $deliverable))
                            : $deliverable->label();
                    @endphp
                    <flux:badge variant="outline">{{ $deliverableLabel }}</flux:badge>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Actions -->
            <div class="flex gap-2 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                @php
                    $isQuickViewSaved = in_array($quickViewCampaign->id, $savedCampaignIds);
                @endphp
                <flux:button wire:click="toggleSaveCampaign({{ $quickViewCampaign->id }})" variant="ghost" icon="{{ $isQuickViewSaved ? 'heart' : 'heart' }}">
                    {{ $isQuickViewSaved ? 'Saved' : 'Save' }}
                </flux:button>
                <flux:spacer />
                <flux:button href="{{ route('campaigns.show', $quickViewCampaign) }}" variant="ghost">
                    View Full Details
                </flux:button>
                @if(auth()->user()->profile->subscribed('default'))
                @livewire('campaigns.apply-to-campaign', [
                    'campaign' => $quickViewCampaign,
                    'buttonText' => 'Apply Now',
                    'buttonVariant' => 'primary',
                    'existingApplication' => $userApplications->get($quickViewCampaign->id),
                    'applicationPreloaded' => true
                ], key('modal-apply-'.$quickViewCampaign->id))
                @else
                <flux:button href="{{ route('billing') }}" variant="primary">
                    Subscribe to Apply
                </flux:button>
                @endif
            </div>
        </div>
        @endif
    </flux:modal>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</div>

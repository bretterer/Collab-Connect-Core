<!-- BUSINESS DASHBOARD -->

<!-- Quick Actions -->
<div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
        <span class="text-sm text-gray-500 dark:text-gray-400">Get things done faster</span>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('campaigns.create') }}" class="group">
            <div class="flex flex-col items-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all duration-200 group-hover:scale-105">
                <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Create Campaign</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">Start a new collaboration</p>
            </div>
        </a>

        <a href="{{ route('campaigns.index') }}" class="group">
            <div class="flex flex-col items-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/10 transition-all duration-200 group-hover:scale-105">
                <div class="h-12 w-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2"></path>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">My Campaigns</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">Manage all campaigns</p>
            </div>
        </a>

        <a href="{{ route('search') }}" class="group">
            <div class="flex flex-col items-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-emerald-300 dark:hover:border-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-all duration-200 group-hover:scale-105">
                <div class="h-12 w-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Find Influencers</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">Discover talent</p>
            </div>
        </a>

        <a href="{{ route('analytics') }}" class="group">
            <div class="flex flex-col items-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-amber-300 dark:hover:border-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/10 transition-all duration-200 group-hover:scale-105">
                <div class="h-12 w-12 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-200">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <p class="text-sm font-medium text-gray-900 dark:text-white">Analytics</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">View insights</p>
            </div>
        </a>
    </div>
</div>

<!-- Main Content Grid -->
<div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

    <!-- Left Column - Campaign Overview -->
    <div class="xl:col-span-2 space-y-6">

        <!-- Active Campaigns -->
        @if($this->getPublishedCampaigns()->count() > 0)
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Active Campaigns</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $this->getPublishedCampaigns()->count() }} campaigns running</p>
                    </div>
                    <a href="{{ route('campaigns.index') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                        View all
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>
                <div class="space-y-4">
                    @foreach($this->getPublishedCampaigns()->take(3) as $campaign)
                        <div class="group p-4 rounded-xl border border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-1.5"></div>
                                            Active
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $campaign->published_at?->diffForHumans() }}</span>
                                    </div>
                                    <h4 class="text-base font-medium text-gray-900 dark:text-white mb-1 truncate">
                                        {{ $campaign->project_name }}
                                    </h4>
                                    <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                        <span class="flex items-center">
                                            {{ $campaign->compensation_display }}
                                        </span>
                                        <span class="flex items-center">
                                            {{ $campaign->influencer_count }} {{ Str::plural('influencer', $campaign->influencer_count) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4 flex items-center space-x-2">
                                    <a href="{{ route('campaigns.show', $campaign) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 text-xs font-medium rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/30 transition-colors">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Draft Campaigns -->
        @if($this->getDraftCampaigns()->count() > 0)
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Draft Campaigns</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $this->getDraftCampaigns()->count() }} campaigns in progress</p>
                    </div>
                </div>
                <div class="space-y-4">
                    @foreach($this->getDraftCampaigns()->take(2) as $campaign)
                        <div class="group p-4 rounded-xl border border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                            Draft
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Step {{ $campaign->current_step }} of 4</span>
                                    </div>
                                    <h4 class="text-base font-medium text-gray-900 dark:text-white mb-1 truncate">
                                        {{ $campaign->project_name ?: 'Untitled Campaign' }}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Last edited {{ $campaign->updated_at->diffForHumans() }}</p>
                                </div>
                                <div class="ml-4 flex items-center space-x-2">
                                    <a href="{{ route('campaigns.edit', $campaign) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-xs font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                        Continue
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column - Applications & Activity -->
    <div class="space-y-6">

        <!-- Application Activity -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Application Activity</h3>
                <a href="{{ route('applications.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                    View All →
                </a>
            </div>

            @php
                $pendingCount = $this->getPendingApplications()->count();
                $totalApplications = $this->getTotalApplicationsCount();
                $recentApplications = $this->getPendingApplications()->take(3);
            @endphp

            @if($totalApplications > 0)
                <!-- Quick Stats -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $pendingCount }}</div>
                        <div class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">Pending Review</div>
                    </div>
                    <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/10 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $totalApplications }}</div>
                        <div class="text-xs text-blue-600 dark:text-blue-400 font-medium">Total Applications</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 dark:bg-green-900/10 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $this->getAcceptedApplications()->count() }}
                        </div>
                        <div class="text-xs text-green-600 dark:text-green-400 font-medium">Approved</div>
                    </div>
                </div>

                <!-- Recent Applications -->
                @if($recentApplications->count() > 0)
                    <div class="space-y-3">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Recent Applications</h4>
                        @foreach($recentApplications as $application)
                            <div class="flex items-center space-x-3 p-4 border border-gray-100 dark:border-gray-800 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <div class="h-10 w-10 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white text-sm font-semibold flex-shrink-0">
                                    {{ substr($application->user->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $application->user->name }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">
                                            {{ ucfirst($application->status->value) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ Str::limit($application->campaign->campaign_goal, 30) }} • Applied {{ $application->submitted_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('applications.show', $application->id) }}" class="p-2 text-blue-600 hover:bg-blue-100 dark:hover:bg-blue-900/20 rounded-lg transition-colors" title="View">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                    <button wire:click="acceptApplication({{ $application->id }})" class="p-2 text-green-600 hover:bg-green-100 dark:hover:bg-green-900/20 rounded-lg transition-colors" title="Accept">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                    <button wire:click="declineApplication({{ $application->id }})" class="p-2 text-red-600 hover:bg-red-100 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Decline">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <div class="h-12 w-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">No pending applications</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Applications will appear here as they come in</p>
                    </div>
                @endif

            @else
                <!-- Empty State -->
                <div class="text-center py-8">
                    <div class="h-16 w-16 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h4 class="text-base font-medium text-gray-900 dark:text-white mb-2">No Applications Yet</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Once you publish campaigns, applications from influencers will appear here.</p>
                    <a href="{{ route('campaigns.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Your First Campaign
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
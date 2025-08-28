<div class="space-y-8">
    @if(auth()->user()->account_type === App\Enums\AccountType::BUSINESS)
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2"></path>
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
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $campaign->published_at->diffForHumans() }}</span>
                                            </div>
                                            <h4 class="text-base font-medium text-gray-900 dark:text-white mb-1 truncate">
                                                {{ $campaign->campaign_goal }}
                                            </h4>
                                            <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                    </svg>
                                                    {{ $campaign->compensation_display }}
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 9 19.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
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
                                                {{ $campaign->campaign_goal ?: 'Untitled Campaign' }}
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

                @if(false === true)
                <!-- Recent Activity -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="h-8 w-8 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <span class="font-medium">@sarah_lifestyle</span> completed "Summer Collection" campaign
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">2 hours ago</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="h-8 w-8 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    New application from <span class="font-medium">@mike_fitness</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">4 hours ago</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div class="h-8 w-8 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <span class="font-medium">@jenny_food</span> sent a message
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">1 day ago</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

    @elseif(auth()->user()->account_type === App\Enums\AccountType::INFLUENCER)
        <!-- INFLUENCER DASHBOARD -->



        <!-- Quick Actions -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">Discover opportunities</span>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('discover') }}" class="group">
                    <div class="flex flex-col items-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-pink-300 dark:hover:border-pink-600 hover:bg-pink-50 dark:hover:bg-pink-900/10 transition-all duration-200 group-hover:scale-105">
                        <div class="h-12 w-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Discover</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">Find campaigns</p>
                    </div>
                </a>

                <a href="{{ route('search') }}" class="group">
                    <div class="flex flex-col items-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition-all duration-200 group-hover:scale-105">
                        <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Search</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">Find businesses</p>
                    </div>
                </a>

                <div class="group">
                    <div class="flex flex-col items-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-emerald-300 dark:hover:border-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/10 transition-all duration-200 group-hover:scale-105 cursor-pointer">
                        <div class="h-12 w-12 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Profile</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">Update profile</p>
                    </div>
                </div>

                <a href="{{ route('media-kit') }}" class="group">
                    <div class="flex flex-col items-center p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-600 hover:bg-purple-50 dark:hover:bg-purple-900/10 transition-all duration-200 group-hover:scale-105">
                        <div class="h-12 w-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform duration-200">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Media Kit</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-1">Download kit</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

            <!-- Left Column - Recommended Campaigns -->
            <div class="xl:col-span-2">
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                <svg class="w-5 h-5 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                                Recommended For You
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Based on your profile and preferences</p>
                        </div>
                        <a href="{{ route('discover') }}" class="inline-flex items-center text-sm font-medium text-pink-600 hover:text-pink-800 dark:text-pink-400">
                            View all
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    </div>

                    @if($this->getRecommendedCampaigns()->count() > 0)
                        <div class="space-y-6">
                            @foreach($this->getRecommendedCampaigns() as $matchData)
                                @php
                                    $campaign = $matchData['campaign'];
                                    $matchScore = $matchData['match_score'];
                                    $matchReasons = $matchData['match_reasons'];
                                    $distanceDisplay = $matchData['distance_display'];
                                    $postedAgo = $matchData['posted_ago'];
                                @endphp

                                <div class="group p-6 rounded-2xl border-2 border-gradient-to-r from-pink-200 to-purple-200 dark:from-pink-900/20 dark:to-purple-900/20 bg-gradient-to-r from-pink-50 to-purple-50 dark:from-pink-900/5 dark:to-purple-900/5 hover:shadow-lg transition-all duration-200">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="h-12 w-12 bg-gradient-to-br from-pink-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold">
                                                {{ substr($campaign->user->currentBusiness?->business_name ?? $campaign->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="flex items-center space-x-2 mb-1">
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gradient-to-r from-pink-600 to-purple-600 text-white">
                                                        {{ $matchScore }}% Match
                                                    </span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $distanceDisplay }}</span>
                                                </div>
                                                <h4 class="text-base font-semibold text-gray-900 dark:text-white">
                                                    {{ Str::limit($campaign->campaign_goal, 60) }}
                                                </h4>
                                            </div>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $postedAgo }}</span>
                                    </div>

                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                        {{ Str::limit($campaign->campaign_description ?? 'Campaign details will be provided upon acceptance.', 120) }}
                                    </p>

                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                </svg>
                                                {{ $campaign->compensation_display }}
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 715.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 9 19.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                {{ $campaign->influencer_count }} spots
                                            </span>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('campaigns.show', $campaign) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                                View Details
                                            </a>
                                            <button class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-pink-600 to-purple-600 text-white text-sm font-medium rounded-lg hover:from-pink-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
                                                Apply Now
                                            </button>
                                        </div>
                                    </div>

                                    @if(count($matchReasons) > 0)
                                        <div class="mt-4 pt-4 border-t border-pink-200 dark:border-pink-800">
                                            <div class="flex items-center space-x-2 text-xs text-pink-600 dark:text-pink-400">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd"></path>
                                                </svg>
                                                <span class="font-medium">Perfect match because:</span>
                                                <span>{{ implode(', ', $matchReasons) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="h-16 w-16 bg-gradient-to-br from-pink-100 to-purple-100 dark:from-pink-900/20 dark:to-purple-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No recommendations yet</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">We're learning about your preferences. Browse all campaigns to discover opportunities!</p>
                            <a href="{{ route('discover') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-600 to-purple-600 text-white font-medium rounded-xl hover:from-pink-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
                                Discover Campaigns
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Column - Applications & Active Campaigns -->
            <div class="space-y-6">

                <!-- My Applications -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">My Applications</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $this->getInfluencerApplications()->count() }} total</p>
                        </div>
                    </div>

                    @if($this->getInfluencerApplications()->count() > 0)
                        <div class="space-y-3">
                            @foreach($this->getInfluencerApplications()->take(3) as $application)
                                <div class="p-4 border border-gray-100 dark:border-gray-800 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all duration-200">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $application->status === \App\Enums\CampaignApplicationStatus::PENDING ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' :
                                               ($application->status === \App\Enums\CampaignApplicationStatus::ACCEPTED ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                                                'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400') }}">
                                            {{ $application->status->label() }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $application->submitted_at->diffForHumans() }}</span>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                        {{ Str::limit($application->campaign->campaign_goal, 40) }}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                        {{ $application->campaign->name }}
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $application->campaign->compensation_display }}</span>
                                        <a href="{{ route('campaigns.show', $application->campaign) }}" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                                            View →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="h-12 w-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No applications yet</p>
                        </div>
                    @endif
                </div>

                <!-- Active Campaigns -->
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Active Work</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $this->getActiveCampaigns()->count() }} active</p>
                        </div>
                    </div>

                    @if($this->getActiveCampaigns()->count() > 0)
                        <div class="space-y-3">
                            @foreach($this->getActiveCampaigns()->take(3) as $application)
                                <div class="p-4 border-2 border-green-200 dark:border-green-800 rounded-xl bg-green-50 dark:bg-green-900/10">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-1.5"></div>
                                            Active
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Due {{ $application->campaign->campaign_completion_date?->format('M j') ?? 'TBD' }}</span>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                                        {{ Str::limit($application->campaign->campaign_goal, 40) }}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                        {{ $application->campaign->business->name ?? $application->campaign->user->name }}
                                    </p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $application->campaign->compensation_display }}</span>
                                        <a href="{{ route('campaigns.show', $application->campaign) }}" class="text-xs text-green-600 hover:text-green-800 dark:text-green-400 font-medium">
                                            Work on it →
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="h-12 w-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">No active work</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    @else
        <!-- DEFAULT DASHBOARD -->
        <div class="text-center py-12">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">
                Welcome to CollabConnect, {{ auth()->user()->name }}!
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
                Your account setup is complete and you're ready to start collaborating.
            </p>
            <div class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-200 transform hover:scale-105">
                Get Started
            </div>
        </div>
    @endif
</div>
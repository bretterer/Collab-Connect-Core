<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    @if(session('message'))
        <div class="mb-6 rounded-md bg-green-50 dark:bg-green-900/20 p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ session('message') }}
                    </p>
                </div>
            </div>
        </div>
    @endif

    @if(auth()->user()->account_type === App\Enums\AccountType::BUSINESS)
        <!-- BUSINESS DASHBOARD -->
        <!-- Welcome Section -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">
                        Welcome back, {{ auth()->user()->name }}! 🚀
                    </h1>
                    <p class="text-blue-100 text-lg">
                        Your business profile is active. Ready to connect with local influencers and grow your brand?
                    </p>
                </div>
            </div>
        </div>

        <!-- Business Stats Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Active Campaigns -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Campaigns</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $this->getDraftCampaigns()->count() + $this->getPublishedCampaigns()->count() + $this->getScheduledCampaigns()->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Applications Received -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">New Applications</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $this->getTotalApplicationsCount() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Connected Influencers -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Connected Influencers</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">8</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Reach -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Reach</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">45.2K</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('campaigns.create') }}">
                    <flux:button variant="primary" icon="plus" class="flex items-center justify-center space-x-2 h-12 w-full">
                        <span>Create Campaign</span>
                    </flux:button>
                </a>
                <a href="{{ route('campaigns.index') }}">
                    <flux:button variant="outline" icon="document-text" class="flex items-center justify-center space-x-2 h-12 w-full">
                        <span>My Campaigns</span>
                    </flux:button>
                </a>
                <a href="{{ route('search') }}">
                    <flux:button variant="outline" icon="magnifying-glass" class="flex items-center justify-center space-x-2 h-12 w-full">
                        <span>Find Influencers</span>
                    </flux:button>
                </a>
                <flux:button variant="outline" icon="chat-bubble-left-right" class="flex items-center justify-center space-x-2 h-12">
                    <span>Messages</span>
                </flux:button>
            </div>
        </div>

        <!-- Recent Activity & Pending Applications -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Activity</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <span class="font-medium">@sarah_lifestyle</span> completed your "Summer Collection" campaign
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">2 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    New application from <span class="font-medium">@mike_fitness</span> for "Protein Shake Review"
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">4 hours ago</p>
                            </div>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/20 rounded-full flex items-center justify-center">
                                    <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    <span class="font-medium">@jenny_food</span> sent you a message about collaboration terms
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">1 day ago</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <flux:button variant="subtle" size="sm">View All Activity</flux:button>
                    </div>
                </div>
            </div>

            <!-- Application Overview -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Application Activity</h3>
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
                                    {{ $totalApplications > 0 ? round(($totalApplications - $pendingCount) / $totalApplications * 100) : 0 }}%
                                </div>
                                <div class="text-xs text-green-600 dark:text-green-400 font-medium">Processed</div>
                            </div>
                        </div>

                        @if($pendingCount > 0)
                            <!-- Recent Pending Applications (Top 3) -->
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Recent Applications Needing Review</h4>
                                <div class="space-y-2">
                                    @foreach($recentApplications as $application)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                            <div class="flex items-center space-x-2 flex-1 min-w-0">
                                                <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white text-xs font-medium">
                                                    {{ substr($application->user->name, 0, 1) }}
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $application->user->name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Str::limit($application->campaign->campaign_goal, 25) }}</p>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ $application->submitted_at->diffForHumans() }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="flex space-x-3">
                            @if($pendingCount > 0)
                                <a href="{{ route('applications.index') }}?statusFilter=pending" class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium py-2 px-4 rounded-md text-center transition-colors">
                                    Review {{ $pendingCount }} Pending
                                </a>
                            @endif
                            <a href="{{ route('applications.index') }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-md text-center transition-colors">
                                View All Applications
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No applications yet</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Applications from influencers will appear here when you publish campaigns.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('campaigns.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 px-4 rounded-md transition-colors">
                                    Create Your First Campaign
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Campaigns Overview -->
        @if($this->getDraftCampaigns()->count() > 0 || $this->getPublishedCampaigns()->count() > 0 || $this->getScheduledCampaigns()->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-8">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Campaigns Overview</h3>
                        <a href="{{ route('campaigns.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                            View All →
                        </a>
                    </div>

                    <!-- Draft Campaigns -->
                    @if($this->getDraftCampaigns()->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Draft Campaigns ({{ $this->getDraftCampaigns()->count() }})
                            </h4>
                            <div class="space-y-3">
                                @foreach($this->getDraftCampaigns()->take(2) as $campaign)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                        Draft
                                                    </span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        Step {{ $campaign->current_step }} of 4
                                                    </span>
                                                </div>
                                                <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ Str::limit($campaign->campaign_goal, 50) }}
                                                </h5>
                                                <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    <span>{{ $campaign->compensation_display }}</span>
                                                    <span>{{ $campaign->influencer_count }} influencers</span>
                                                    <span>{{ $campaign->updated_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                            <a href="{{ route('campaigns.edit', $campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-medium">
                                                Continue
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Published Campaigns -->
                    @if($this->getPublishedCampaigns()->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Published Campaigns ({{ $this->getPublishedCampaigns()->count() }})
                            </h4>
                            <div class="space-y-3">
                                @foreach($this->getPublishedCampaigns()->take(2) as $campaign)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                                        Published
                                                    </span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $campaign->published_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ Str::limit($campaign->campaign_goal, 50) }}
                                                </h5>
                                                <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    <span>{{ $campaign->compensation_display }}</span>
                                                    <span>{{ $campaign->influencer_count }} influencers</span>
                                                    <span>Active</span>
                                                </div>
                                            </div>
                                            @if($campaign->isPublished())
                                                <a href="{{ route('campaigns.show', $campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-medium">
                                                    View
                                                </a>
                                            @else
                                                <a href="{{ route('campaigns.edit', $campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-medium">
                                                    Edit
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Scheduled Campaigns -->
                    @if($this->getScheduledCampaigns()->count() > 0)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                Scheduled Campaigns ({{ $this->getScheduledCampaigns()->count() }})
                            </h4>
                            <div class="space-y-3">
                                @foreach($this->getScheduledCampaigns()->take(2) as $campaign)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center space-x-2 mb-1">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                                        Scheduled
                                                    </span>
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $campaign->scheduled_date->format('M j, Y') }}
                                                    </span>
                                                </div>
                                                <h5 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ Str::limit($campaign->campaign_goal, 50) }}
                                                </h5>
                                                <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    <span>{{ $campaign->compensation_display }}</span>
                                                    <span>{{ $campaign->influencer_count }} influencers</span>
                                                    <span>Pending</span>
                                                </div>
                                            </div>
                                            @if($campaign->isPublished())
                                                <a href="{{ route('campaigns.show', $campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-medium">
                                                    View
                                                </a>
                                            @else
                                                <a href="{{ route('campaigns.edit', $campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-xs font-medium">
                                                    Edit
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Summary Stats -->
                    <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $this->getDraftCampaigns()->count() }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Drafts</div>
                            </div>
                            <div>
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $this->getPublishedCampaigns()->count() }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Published</div>
                            </div>
                            <div>
                                <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $this->getScheduledCampaigns()->count() }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">Scheduled</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif



    @elseif(auth()->user()->account_type === App\Enums\AccountType::INFLUENCER)
        <!-- INFLUENCER DASHBOARD -->
        <!-- Welcome Section -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-pink-500 to-purple-600 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">
                        Hey {{ auth()->user()->name }}! ✨
                    </h1>
                    <p class="text-pink-100 text-lg">
                        Your influencer profile is live. Time to discover amazing collaboration opportunities!
                    </p>
                </div>
            </div>
        </div>

        <!-- Influencer Stats Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Active Campaigns -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-pink-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Campaigns</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">2</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending Applications -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pending Applications</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">5</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Earnings -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">This Month</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">$1,250</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profile Views -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Profile Views</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">89</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <a href="{{ route('discover') }}">
                    <flux:button variant="primary" icon="sparkles" class="flex items-center justify-center space-x-2 h-12 w-full">
                        <span>Discover Campaigns</span>
                    </flux:button>
                </a>
                <a href="{{ route('search') }}">
                    <flux:button variant="outline" icon="magnifying-glass" class="flex items-center justify-center space-x-2 h-12 w-full">
                        <span>Find Businesses</span>
                    </flux:button>
                </a>
                <flux:button variant="outline" icon="user" class="flex items-center justify-center space-x-2 h-12">
                    <span>Update Profile</span>
                </flux:button>
                <flux:button variant="outline" icon="document-text" class="flex items-center justify-center space-x-2 h-12">
                    <span>Media Kit</span>
                </flux:button>
            </div>
        </div>

        <!-- Recommended Campaigns -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                            </svg>
                            Campaigns Just For You
                        </h3>
                        <a href="{{ route('discover') }}" class="text-sm text-pink-600 hover:text-pink-800 dark:text-pink-400 dark:hover:text-pink-300">
                            View All →
                        </a>
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Based on your profile, location, and interests, here are some campaigns that might be perfect for you:
                    </p>

                    <!-- Campaign Cards -->
                    @if($this->getRecommendedCampaigns()->count() > 0)
                        <div class="space-y-4">
                            @foreach($this->getRecommendedCampaigns() as $matchData)
                                @php
                                    $campaign = $matchData['campaign'];
                                    $matchScore = $matchData['match_score'];
                                    $matchReasons = $matchData['match_reasons'];
                                    $distanceDisplay = $matchData['distance_display'];
                                    $postedAgo = $matchData['posted_ago'];

                                    // Determine styling based on match score
                                    if ($matchScore >= 90) {
                                        $borderClass = 'border-2 border-green-200 dark:border-green-800';
                                        $bgClass = 'bg-green-50 dark:bg-green-900/10';
                                        $badgeClass = 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400';
                                        $badgeIcon = true;
                                        $primaryButtonVariant = 'primary';
                                        $secondaryButtonVariant = 'outline';
                                        $matchColor = 'text-green-600 dark:text-green-400';
                                    } elseif ($matchScore >= 75) {
                                        $borderClass = 'border border-blue-200 dark:border-blue-800';
                                        $bgClass = 'bg-blue-50/50 dark:bg-blue-900/5';
                                        $badgeClass = 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400';
                                        $badgeIcon = false;
                                        $primaryButtonVariant = 'outline';
                                        $secondaryButtonVariant = 'ghost';
                                        $matchColor = 'text-blue-600 dark:text-blue-400';
                                    } else {
                                        $borderClass = 'border border-gray-200 dark:border-gray-700';
                                        $bgClass = '';
                                        $badgeClass = 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200';
                                        $badgeIcon = false;
                                        $primaryButtonVariant = 'outline';
                                        $secondaryButtonVariant = 'ghost';
                                        $matchColor = 'text-gray-600 dark:text-gray-400';
                                    }
                                @endphp

                                <div class="{{ $borderClass }} rounded-lg p-4 {{ $bgClass }}">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                                                    @if($badgeIcon)
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    @endif
                                                    {{ $matchScore }}% Match
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $campaign->user->businessProfile ? 'Local Business' : 'Business' }} • {{ $distanceDisplay }}
                                                </span>
                                            </div>
                                            <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
                                                {{ Str::limit($campaign->campaign_goal, 60) }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                {{ Str::limit($campaign->campaign_description ?? 'Campaign details will be provided upon acceptance.', 120) }}
                                            </p>
                                            <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                    </svg>
                                                    {{ $campaign->compensation_display }}
                                                </span>
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 515.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 919.288 0M15 7a3 3 0 11-6 0 3 3 0 616 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                    </svg>
                                                    Looking for {{ $campaign->influencer_count }} {{ Str::plural('influencer', $campaign->influencer_count) }}
                                                </span>
                                                @if($campaign->campaign_type)
                                                    <span>{{ $campaign->campaign_type->label() }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-4 flex flex-col space-y-2">
                                            <flux:button variant="{{ $primaryButtonVariant }}" size="sm">Apply Now</flux:button>
                                            <a href="{{ route('campaigns.show', $campaign) }}">
                                                <flux:button variant="{{ $secondaryButtonVariant }}" size="sm" class="w-full">View Details</flux:button>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between text-xs">
                                        <div class="flex items-center space-x-2">
                                            @if(count($matchReasons) > 0)
                                                <span class="{{ $matchColor }} font-medium">Why this matches:</span>
                                                <span class="text-gray-600 dark:text-gray-400">{{ implode(', ', $matchReasons) }}</span>
                                            @endif
                                        </div>
                                        <span class="text-gray-500 dark:text-gray-400">{{ $postedAgo }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No matching campaigns found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                No worries, you can still browse all campaigns in case there is one that you like.
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('discover') }}">
                                    <flux:button variant="primary">Browse All Campaigns</flux:button>
                                </a>
                            </div>
                        </div>
                    @endif



                    <!-- Match Insights -->
                    <div class="mt-6 p-4 bg-gradient-to-r from-pink-50 to-purple-50 dark:from-pink-900/10 dark:to-purple-900/10 rounded-lg border border-pink-200 dark:border-pink-800">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <svg class="w-5 h-5 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-1">💡 Tips to Improve Your Matches</h4>
                                <ul class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                                    <li>• Complete your bio with more specific interests to get better matches</li>
                                    <li>• Add more social media accounts to increase your visibility</li>
                                    <li>• Update your content categories to match trending campaign types</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Applications & Active Campaigns -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- My Applications -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            My Applications
                        </h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $this->getInfluencerApplications()->count() }} total</span>
                    </div>

                    @if($this->getInfluencerApplications()->count() > 0)
                        <div class="space-y-4">
                            @foreach($this->getInfluencerApplications()->take(3) as $application)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                    {{ $application->status === \App\Enums\CampaignApplicationStatus::PENDING ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : 
                                                       ($application->status === \App\Enums\CampaignApplicationStatus::ACCEPTED ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 
                                                        'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400') }}">
                                                    {{ $application->status->label() }}
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $application->submitted_at->diffForHumans() }}</span>
                                            </div>
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                                {{ Str::limit($application->campaign->campaign_goal, 50) }}
                                            </h4>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                                {{ $application->campaign->user->businessProfile?->business_name ?? $application->campaign->user->name }}
                                            </p>
                                            <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                    </svg>
                                                    {{ $application->campaign->compensation_display }}
                                                </span>
                                                @if($application->campaign->campaign_type)
                                                    <span>{{ $application->campaign->campaign_type->label() }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-4 flex flex-col space-y-1">
                                            <a href="{{ route('campaigns.show', $application->campaign) }}">
                                                <flux:button variant="outline" size="sm" class="w-full">View Campaign</flux:button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($this->getInfluencerApplications()->count() > 3)
                            <div class="mt-4 text-center">
                                <flux:button variant="subtle" size="sm">View All Applications ({{ $this->getInfluencerApplications()->count() }})</flux:button>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No applications yet</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Start applying to campaigns to see your applications here.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('discover') }}">
                                    <flux:button variant="primary" size="sm">Discover Campaigns</flux:button>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Active Campaigns -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Active Campaigns
                        </h3>
                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $this->getActiveCampaigns()->count() }} active</span>
                    </div>

                    @if($this->getActiveCampaigns()->count() > 0)
                        <div class="space-y-4">
                            @foreach($this->getActiveCampaigns()->take(3) as $application)
                                <div class="border-2 border-green-200 dark:border-green-800 rounded-lg p-4 bg-green-50 dark:bg-green-900/10">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-2 mb-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    Active
                                                </span>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">Accepted {{ $application->accepted_at ? $application->accepted_at->diffForHumans() : 'recently' }}</span>
                                            </div>
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">
                                                {{ Str::limit($application->campaign->campaign_goal, 50) }}
                                            </h4>
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">
                                                {{ $application->campaign->user->businessProfile?->business_name ?? $application->campaign->user->name }}
                                            </p>
                                            <div class="flex items-center space-x-3 text-xs text-gray-500 dark:text-gray-400">
                                                <span class="flex items-center">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                                    </svg>
                                                    {{ $application->campaign->compensation_display }}
                                                </span>
                                                @if($application->campaign->campaign_completion_date)
                                                    <span class="flex items-center">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        Due {{ $application->campaign->campaign_completion_date->format('M j') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="ml-4 flex flex-col space-y-1">
                                            <a href="{{ route('campaigns.show', $application->campaign) }}">
                                                <flux:button variant="primary" size="sm" class="w-full">View Details</flux:button>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        @if($this->getActiveCampaigns()->count() > 3)
                            <div class="mt-4 text-center">
                                <flux:button variant="subtle" size="sm">View All Active ({{ $this->getActiveCampaigns()->count() }})</flux:button>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No active campaigns</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Once your applications are accepted, they'll appear here.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    @else
        <!-- DEFAULT DASHBOARD (for undefined or other account types) -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                        Welcome to CollabConnect, {{ auth()->user()->name }}! 🎉
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Great! You're all set up and ready to start collaborating.
                    </p>
                </div>
            </div>
        </div>

        <!-- Basic Stats -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Profile</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">Complete</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Account Type</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ auth()->user()->account_type->label() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Status</dt>
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">Active</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Get Started -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Get Started</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-4">
                    Complete your profile setup to unlock all CollabConnect features.
                </p>
                <flux:button variant="primary">Complete Setup</flux:button>
            </div>
        </div>
    @endif
</div>

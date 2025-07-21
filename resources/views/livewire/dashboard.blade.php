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
                                <dd class="text-lg font-medium text-gray-900 dark:text-white">12</dd>
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

            <!-- Pending Applications -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pending Applications</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-pink-400 to-red-400 rounded-full flex items-center justify-center text-white font-medium">
                                    AL
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">@alex_local</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">12.5K followers • Local Food</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <flux:button variant="primary" size="sm">Accept</flux:button>
                                <flux:button variant="outline" size="sm">Decline</flux:button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white font-medium">
                                    MR
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">@maria_reviews</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">8.2K followers • Product Reviews</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <flux:button variant="primary" size="sm">Accept</flux:button>
                                <flux:button variant="outline" size="sm">Decline</flux:button>
                            </div>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-gradient-to-br from-green-400 to-teal-400 rounded-full flex items-center justify-center text-white font-medium">
                                    TF
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">@tom_fitness</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">25.1K followers • Fitness</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <flux:button variant="primary" size="sm">Accept</flux:button>
                                <flux:button variant="outline" size="sm">Decline</flux:button>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <flux:button variant="subtle" size="sm">View All Applications</flux:button>
                    </div>
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
                                                    <span>${{ number_format($campaign->budget) }}</span>
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
                                                    <span>${{ number_format($campaign->budget) }}</span>
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
                                                    <span>${{ number_format($campaign->budget) }}</span>
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
                <a href="{{ route('search') }}">
                    <flux:button variant="primary" icon="magnifying-glass" class="flex items-center justify-center space-x-2 h-12 w-full">
                        <span>Find Businesses</span>
                    </flux:button>
                </a>
                <flux:button variant="outline" icon="user" class="flex items-center justify-center space-x-2 h-12">
                    <span>Update Profile</span>
                </flux:button>
                <flux:button variant="outline" icon="document-text" class="flex items-center justify-center space-x-2 h-12">
                    <span>Media Kit</span>
                </flux:button>
                <flux:button variant="outline" icon="chat-bubble-left-right" class="flex items-center justify-center space-x-2 h-12">
                    <span>Messages</span>
                </flux:button>
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
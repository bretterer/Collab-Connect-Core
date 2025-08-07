<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-red-600 to-orange-600 overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6 text-white">
                <h1 class="text-3xl font-bold mb-2">
                    Admin Dashboard 🛡️
                </h1>
                <p class="text-red-100 text-lg">
                    Complete control over the CollabConnect platform. Monitor, manage, and maintain system health.
                </p>
            </div>
        </div>
    </div>

    @php
        $userCounts = $this->getUserCounts();
        $campaignStats = $this->getCampaignStats();
        $applicationStats = $this->getApplicationStats();
        $systemHealth = $this->getSystemHealth();
    @endphp

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Total Users -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Users</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($userCounts['total_users']) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-2">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        +{{ $userCounts['new_users_this_month'] }} this month
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Campaigns -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Campaigns</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($campaignStats['total_campaigns']) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-2">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        +{{ $campaignStats['campaigns_this_month'] }} this month
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Applications -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Applications</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($applicationStats['total_applications']) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-2">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        +{{ $applicationStats['applications_this_month'] }} this month
                    </p>
                </div>
            </div>
        </div>

        <!-- Platform Health -->
        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Platform Health</dt>
                            <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $systemHealth['platform_health_score'] }}%</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-2">
                    <p class="text-xs text-gray-600 dark:text-gray-400">
                        {{ $systemHealth['active_users_today'] }} active today
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Admin Actions</h3>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <a href="{{ route('admin.users.index') }}">
                <flux:button variant="primary" icon="users" class="flex items-center justify-center space-x-2 h-12 w-full">
                    <span>Manage Users</span>
                </flux:button>
            </a>
            <a href="{{ route('admin.campaigns.index') }}">
                <flux:button variant="outline" icon="document-text" class="flex items-center justify-center space-x-2 h-12 w-full">
                    <span>Manage Campaigns</span>
                </flux:button>
            </a>
            <a href="{{ route('admin.analytics') }}">
                <flux:button variant="outline" icon="chart-bar-square" class="flex items-center justify-center space-x-2 h-12 w-full">
                    <span>Analytics</span>
                </flux:button>
            </a>
            <a href="{{ route('admin.settings') }}">
                <flux:button variant="outline" icon="cog-6-tooth" class="flex items-center justify-center space-x-2 h-12 w-full">
                    <span>System Settings</span>
                </flux:button>
            </a>
        </div>
    </div>

    <!-- Detailed Stats Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- User Breakdown -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    User Distribution
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Business Users</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($userCounts['business_users']) }}</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $userCounts['total_users'] > 0 ? ($userCounts['business_users'] / $userCounts['total_users']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Influencers</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($userCounts['influencer_users']) }}</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-pink-600 h-2 rounded-full" style="width: {{ $userCounts['total_users'] > 0 ? ($userCounts['influencer_users'] / $userCounts['total_users']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Admins</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($userCounts['admin_users']) }}</span>
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full" style="width: {{ $userCounts['total_users'] > 0 ? ($userCounts['admin_users'] / $userCounts['total_users']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Campaign Status -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Campaign Status
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Published</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($campaignStats['published_campaigns']) }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">Live</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Drafts</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($campaignStats['draft_campaigns']) }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">Draft</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Scheduled</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($campaignStats['scheduled_campaigns']) }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">Scheduled</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Publish Rate: <span class="font-medium text-gray-900 dark:text-white">{{ $systemHealth['campaign_publish_rate'] }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Performance -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Application Status
                </h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($applicationStats['pending_applications']) }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400">{{ $applicationStats['pending_applications'] }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Accepted</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($applicationStats['accepted_applications']) }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">{{ $applicationStats['accepted_applications'] }}</span>
                        </div>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Rejected</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($applicationStats['rejected_applications']) }}</span>
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400">{{ $applicationStats['rejected_applications'] }}</span>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Conversion Rate: <span class="font-medium text-gray-900 dark:text-white">{{ $systemHealth['application_conversion_rate'] }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Management -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Recent Users -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Users</h3>
                    <a href="{{ route('admin.users.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                        View All →
                    </a>
                </div>

                @if($this->getRecentUsers()->count() > 0)
                    <div class="space-y-4">
                        @foreach($this->getRecentUsers() as $user)
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                        {{ $user->initials() }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->account_type->label() }} • {{ $user->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $user->account_type === \App\Enums\AccountType::BUSINESS ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' : 'bg-pink-100 text-pink-800 dark:bg-pink-900/20 dark:text-pink-400' }}">
                                        {{ $user->account_type->label() }}
                                    </span>
                                    <a href="{{ route('admin.users.show', $user) }}" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No recent users</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Recent Campaigns -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Campaigns</h3>
                    <a href="{{ route('admin.campaigns.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">
                        View All →
                    </a>
                </div>

                @if($this->getRecentCampaigns()->count() > 0)
                    <div class="space-y-4">
                        @foreach($this->getRecentCampaigns() as $campaign)
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $campaign->status === \App\Enums\CampaignStatus::PUBLISHED ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                                               ($campaign->status === \App\Enums\CampaignStatus::DRAFT ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' :
                                                'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400') }}">
                                            {{ $campaign->status->label() }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $campaign->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ Str::limit($campaign->campaign_goal, 40) }}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $campaign->user->businessProfile?->business_name ?? $campaign->user->name }}
                                    </p>
                                </div>
                                <a href="{{ route('admin.campaigns.show', $campaign) }}" class="ml-4 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-sm text-gray-500 dark:text-gray-400">No recent campaigns</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Pending Applications (if any) -->
    @if($this->getPendingApplications()->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.98-.833-2.75 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        Applications Needing Attention
                    </h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $this->getPendingApplications()->count() }} pending</span>
                </div>

                <div class="space-y-3">
                    @foreach($this->getPendingApplications() as $application)
                        <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/10 rounded border border-yellow-200 dark:border-yellow-800">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-1">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $application->user->name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">applied to</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($application->campaign->campaign_goal, 30) }}</span>
                                </div>
                                <p class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $application->campaign->user->businessProfile?->business_name ?? $application->campaign->user->name }} • {{ $application->submitted_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <a href="{{ route('campaigns.show', $application->campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 text-xs font-medium">
                                    Review
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

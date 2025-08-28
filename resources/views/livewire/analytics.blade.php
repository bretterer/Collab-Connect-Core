<div class="space-y-8 relative">
    <!-- Coming Soon Overlay -->
    <div class="absolute inset-0 bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="text-center p-8 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-700 max-w-md mx-4">
            <div class="h-16 w-16 bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">Analytics Coming Soon!</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">We're building comprehensive analytics and insights for your campaigns. Track performance, ROI, and influencer engagement all in one place.</p>
            <div class="flex items-center justify-center space-x-2 text-sm text-amber-600 dark:text-amber-400">
                <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                <span class="font-medium">In Development</span>
            </div>
        </div>
    </div>

    <!-- Blurred Content Below -->
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Analytics & Insights</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track your campaign performance and business growth</p>
        </div>
        <div class="flex items-center space-x-4">
            <!-- Time Frame Selector -->
            <select wire:model.live="timeFrame" class="text-sm border-gray-300 dark:border-gray-700 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white">
                <option value="3">Last 3 months</option>
                <option value="6">Last 6 months</option>
                <option value="12">Last 12 months</option>
            </select>
            
            <!-- Export Button -->
            <button wire:click="exportData" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                </svg>
                Export Data
            </button>
        </div>
    </div>

    <!-- Key Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Campaigns -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Campaigns</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $campaign_performance['total_campaigns'] }}</p>
                </div>
                <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-center mt-4 text-sm">
                <span class="text-green-500">{{ $campaign_performance['published_campaigns'] }} published</span>
                <span class="mx-2 text-gray-300">•</span>
                <span class="text-gray-500 dark:text-gray-400">{{ $campaign_performance['draft_campaigns'] }} drafts</span>
            </div>
        </div>

        <!-- Total Applications -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Applications</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $application_analytics['total_applications'] }}</p>
                </div>
                <div class="h-12 w-12 bg-purple-100 dark:bg-purple-900/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 7715.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 9 19.288 0M15 7a3 3 0 11-6 0 3 3 0 6 16 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-center mt-4 text-sm">
                <span class="text-green-500">{{ $application_analytics['acceptance_rate'] }}% accepted</span>
                <span class="mx-2 text-gray-300">•</span>
                <span class="text-yellow-500">{{ $application_analytics['pending_applications'] }} pending</span>
            </div>
        </div>

        <!-- Total Budget -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Budget</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($financial_analytics['total_budget'], 0) }}</p>
                </div>
                <div class="h-12 w-12 bg-green-100 dark:bg-green-900/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-center mt-4 text-sm">
                <span class="text-blue-500">${{ number_format($financial_analytics['active_budget'], 0) }} active</span>
                <span class="mx-2 text-gray-300">•</span>
                <span class="text-gray-500 dark:text-gray-400">${{ number_format($financial_analytics['spent_budget'], 0) }} spent</span>
            </div>
        </div>

        <!-- Completion Rate -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Completion Rate</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $campaign_performance['completion_rate'] }}%</p>
                </div>
                <div class="h-12 w-12 bg-amber-100 dark:bg-amber-900/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="flex items-center mt-4 text-sm">
                <span class="text-green-500">{{ $campaign_performance['completed_campaigns'] }} completed</span>
                <span class="mx-2 text-gray-300">•</span>
                <span class="text-blue-500">{{ $campaign_performance['published_campaigns'] }} active</span>
            </div>
        </div>
    </div>

    <!-- Performance Charts & Insights -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- Campaign Trends Chart -->
        <div class="xl:col-span-2">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Campaign Performance Trends</h3>
                    <div class="flex items-center space-x-2 text-xs">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-2"></div>
                            <span class="text-gray-500 dark:text-gray-400">Campaigns Created</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                            <span class="text-gray-500 dark:text-gray-400">Applications</span>
                        </div>
                    </div>
                </div>
                
                <!-- Simple Chart Implementation -->
                <div class="space-y-4">
                    @foreach($campaign_trends as $trend)
                        <div class="flex items-center space-x-4">
                            <div class="w-16 text-xs font-medium text-gray-500 dark:text-gray-400">{{ $trend['month'] }}</div>
                            <div class="flex-1">
                                <!-- Campaigns Bar -->
                                <div class="flex items-center space-x-2 mb-1">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-blue-500 h-2 rounded-full" style="width: {{ min(100, ($trend['campaigns_created'] / max(1, collect($campaign_trends)->max('campaigns_created'))) * 100) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 w-6">{{ $trend['campaigns_created'] }}</span>
                                </div>
                                <!-- Applications Bar -->
                                <div class="flex items-center space-x-2">
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ min(100, ($trend['applications_received'] / max(1, collect($campaign_trends)->max('applications_received'))) * 100) }}%"></div>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 w-6">{{ $trend['applications_received'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Quick Insights -->
        <div class="space-y-6">
            <!-- Application Response Time -->
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Response Time</h3>
                    <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mb-1">
                        {{ $application_analytics['avg_response_time_hours'] }}h
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Average response to applications</p>
                </div>
            </div>

            <!-- Top Performing Area -->
            @if(count($geographic_performance['top_performing_areas']) > 0)
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Area</h3>
                        <div class="h-10 w-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="text-center">
                        <p class="text-xl font-bold text-gray-900 dark:text-white mb-1">
                            {{ $geographic_performance['top_performing_areas'][0]['zip_code'] }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $geographic_performance['top_performing_areas'][0]['avg_applications'] }} avg applications
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Detailed Analytics Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        
        <!-- Top Performing Campaigns -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Top Performing Campaigns</h3>
                <a href="{{ route('campaigns.index') }}" class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 font-medium">View All →</a>
            </div>
            
            @if($top_performing_campaigns->count() > 0)
                <div class="space-y-4">
                    @foreach($top_performing_campaigns as $campaign)
                        <div class="flex items-center justify-between p-4 border border-gray-100 dark:border-gray-800 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                    {{ Str::limit($campaign->campaign_goal, 40) }}
                                </h4>
                                <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <span>{{ $campaign->applications_count }} applications</span>
                                    <span>{{ $campaign->accepted_applications_count }} accepted</span>
                                </div>
                            </div>
                            <div class="ml-4 text-right">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $campaign->applications_count > 0 ? round(($campaign->accepted_applications_count / $campaign->applications_count) * 100, 1) : 0 }}%
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">acceptance</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="h-12 w-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No performance data yet</p>
                </div>
            @endif
        </div>

        <!-- Campaign Type Performance -->
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Campaign Type Performance</h3>
            </div>
            
            @if(count($campaign_type_performance) > 0)
                <div class="space-y-4">
                    @foreach($campaign_type_performance as $typeData)
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $typeData['type_label'] }}</span>
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $typeData['avg_applications'] }} avg applications</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full" style="width: {{ min(100, $typeData['avg_applications'] * 10) }}%"></div>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $typeData['acceptance_rate'] }}% accepted</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <div class="h-12 w-12 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No campaign type data yet</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Influencer Engagement Insights -->
    <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Influencer Engagement Insights</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Unique Influencers -->
            <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/10 dark:to-pink-900/10 rounded-xl">
                <div class="h-12 w-12 bg-gradient-to-br from-purple-500 to-pink-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 715.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 9 19.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $influencer_engagement['unique_influencers'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Unique Influencers</div>
            </div>

            <!-- Repeat Collaborators -->
            <div class="text-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/10 dark:to-emerald-900/10 rounded-xl">
                <div class="h-12 w-12 bg-gradient-to-br from-green-500 to-emerald-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $influencer_engagement['repeat_collaborators'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Repeat Collaborators</div>
            </div>

            <!-- Avg Applications per Campaign -->
            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-cyan-50 dark:from-blue-900/10 dark:to-cyan-900/10 rounded-xl">
                <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $influencer_engagement['avg_applications_per_campaign'] }}</div>
                <div class="text-sm text-gray-500 dark:text-gray-400">Avg Apps per Campaign</div>
            </div>
        </div>
    </div>
</div>
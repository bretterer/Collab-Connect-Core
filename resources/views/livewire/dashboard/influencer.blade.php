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
                                {{ Str::limit($application->campaign->project_name, 40) }}
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
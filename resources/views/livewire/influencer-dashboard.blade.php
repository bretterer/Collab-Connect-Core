
<div class="space-y-8">
    <!-- Professional Influencer Dashboard -->
<livewire:components.beta-notification />
    <!-- Enhanced Header -->
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <flux:heading size="xl" level="1" class="text-gray-900 dark:text-white">
                        Good {{ now()->format('A') === 'AM' ? 'morning' : 'afternoon' }}, {{ auth()->user()->name }}
                    </flux:heading>
                    <flux:badge variant="success" size="sm" class="ml-2">Creator</flux:badge>
                </div>
                <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                        <flux:icon name="calendar" class="w-4 h-4" />
                        {{ now()->format('l, F j, Y') }}
                    </span>
                    <span class="flex items-center gap-1">
                        <flux:icon name="sparkles" class="w-4 h-4" />
                        {{ $this->getInfluencerApplications()->count() }} applications submitted
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <flux:button href="{{ route('discover') }}" variant="ghost" icon="sparkles" class="text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
                    Discover Campaigns
                </flux:button>
                <flux:button href="{{ route('media-kit') }}" variant="primary" icon="document-text" class="shadow-lg shadow-purple-500/25">
                    Media Kit
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Enhanced Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <flux:card class="p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <flux:text size="sm" class="text-gray-500 dark:text-gray-400 font-medium">Applications</flux:text>
                    </div>
                    <div class="flex items-baseline gap-3">
                        <flux:text size="3xl" class="font-bold text-gray-900 dark:text-white">
                            {{ $this->getInfluencerApplications()->count() }}
                        </flux:text>
                        <div class="flex items-center gap-1 text-blue-600 dark:text-blue-400">
                            <flux:icon name="arrow-trending-up" class="w-4 h-4" />
                            <flux:text size="xs" class="font-medium">Total</flux:text>
                        </div>
                    </div>
                </div>
                <div class="h-12 w-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                    <flux:icon name="paper-airplane" class="text-white w-6 h-6" />
                </div>
            </div>
        </flux:card>

        @php
            $pendingCount = $this->getInfluencerApplications()->where('status', \App\Enums\CampaignApplicationStatus::PENDING)->count();
        @endphp

        <flux:card class="p-6 hover:shadow-lg transition-shadow duration-200 {{ $pendingCount > 0 ? 'ring-2 ring-amber-200 dark:ring-amber-800' : '' }}">
            <div class="flex items-center justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <flux:text size="sm" class="text-gray-500 dark:text-gray-400 font-medium">Pending Review</flux:text>
                        @if($pendingCount > 0)
                            <flux:badge size="sm" variant="warning" class="animate-pulse">
                                {{ $pendingCount }}
                            </flux:badge>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-3">
                        <flux:text size="3xl" class="font-bold text-gray-900 dark:text-white">
                            {{ $pendingCount }}
                        </flux:text>
                        @if($pendingCount > 0)
                            <flux:text size="xs" class="text-amber-600 dark:text-amber-400 font-medium">
                                Awaiting response
                            </flux:text>
                        @endif
                    </div>
                </div>
                <div class="h-12 w-12 bg-gradient-to-br from-amber-500 to-orange-600 rounded-xl flex items-center justify-center">
                    <flux:icon name="clock" class="text-white w-6 h-6" />
                </div>
            </div>
        </flux:card>

        @php
            $activeCount = $this->getActiveCampaigns()->count();
        @endphp

        <flux:card class="p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <flux:text size="sm" class="text-gray-500 dark:text-gray-400 font-medium">Active Work</flux:text>
                        @if($activeCount > 0)
                            <flux:badge size="sm" variant="success">Live</flux:badge>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-3">
                        <flux:text size="3xl" class="font-bold text-gray-900 dark:text-white">
                            {{ $activeCount }}
                        </flux:text>
                        @if($activeCount > 0)
                            <div class="flex items-center gap-1 text-green-600 dark:text-green-400">
                                <flux:icon name="check-circle" class="w-4 h-4" />
                                <flux:text size="xs" class="font-medium">In progress</flux:text>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="h-12 w-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <flux:icon name="check-circle" class="text-white w-6 h-6" />
                </div>
            </div>
        </flux:card>

        @php
            $approvedCount = $this->getInfluencerApplications()->where('status', \App\Enums\CampaignApplicationStatus::ACCEPTED)->count();
        @endphp

        <flux:card class="p-6 hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <flux:text size="sm" class="text-gray-500 dark:text-gray-400 font-medium">Approved</flux:text>
                        @if($approvedCount > 0)
                            <flux:badge size="sm" variant="success">Success</flux:badge>
                        @endif
                    </div>
                    <div class="flex items-baseline gap-3">
                        <flux:text size="3xl" class="font-bold text-gray-900 dark:text-white">
                            {{ $approvedCount }}
                        </flux:text>
                        <div class="flex items-center gap-1 text-purple-600 dark:text-purple-400">
                            <flux:icon name="trophy" class="w-4 h-4" />
                            <flux:text size="xs" class="font-medium">{{ $this->getInfluencerApplications()->count() > 0 ? round(($approvedCount / $this->getInfluencerApplications()->count()) * 100) : 0 }}% rate</flux:text>
                        </div>
                    </div>
                </div>
                <div class="h-12 w-12 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl flex items-center justify-center">
                    <flux:icon name="trophy" class="text-white w-6 h-6" />
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Enhanced Quick Actions -->
    <flux:card class="overflow-hidden">
        <div class="bg-gradient-to-r from-slate-50 to-gray-50 dark:from-slate-950/50 dark:to-gray-950/50 p-6 border-b border-gray-200 dark:border-gray-700">
            <flux:heading size="lg" class="text-gray-900 dark:text-white">Quick Actions</flux:heading>
            <flux:text size="sm" class="text-gray-600 dark:text-gray-400">Grow your influence</flux:text>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <flux:button href="{{ route('discover') }}" variant="ghost" class="h-auto p-4 flex flex-col items-center gap-3 hover:bg-pink-50 dark:hover:bg-pink-900/20 hover:border-pink-200 dark:hover:border-pink-700 group transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <flux:icon name="sparkles" class="w-5 h-5 text-white" />
                    </div>
                    <div class="text-center">
                        <flux:text class="font-medium text-gray-900 dark:text-white">Discover</flux:text>
                        <flux:text size="xs" class="text-gray-500 dark:text-gray-400">Find campaigns</flux:text>
                    </div>
                </flux:button>

                <flux:button href="{{ route('search') }}" variant="ghost" class="h-auto p-4 flex flex-col items-center gap-3 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-200 dark:hover:border-blue-700 group transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <flux:icon name="magnifying-glass" class="w-5 h-5 text-white" />
                    </div>
                    <div class="text-center">
                        <flux:text class="font-medium text-gray-900 dark:text-white">Search</flux:text>
                        <flux:text size="xs" class="text-gray-500 dark:text-gray-400">Find businesses</flux:text>
                    </div>
                </flux:button>

                <flux:button variant="ghost" class="h-auto p-4 flex flex-col items-center gap-3 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:border-emerald-200 dark:hover:border-emerald-700 group transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <flux:icon name="user" class="w-5 h-5 text-white" />
                    </div>
                    <div class="text-center">
                        <flux:text class="font-medium text-gray-900 dark:text-white">Profile</flux:text>
                        <flux:text size="xs" class="text-gray-500 dark:text-gray-400">Update profile</flux:text>
                    </div>
                </flux:button>

                <flux:button href="{{ route('media-kit') }}" variant="ghost" class="h-auto p-4 flex flex-col items-center gap-3 hover:bg-purple-50 dark:hover:bg-purple-900/20 hover:border-purple-200 dark:hover:border-purple-700 group transition-all">
                    <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                        <flux:icon name="document-text" class="w-5 h-5 text-white" />
                    </div>
                    <div class="text-center">
                        <flux:text class="font-medium text-gray-900 dark:text-white">Media Kit</flux:text>
                        <flux:text size="xs" class="text-gray-500 dark:text-gray-400">Download kit</flux:text>
                    </div>
                </flux:button>
            </div>
        </div>
    </flux:card>

    <!-- Enhanced Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Primary Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Recommended Campaigns -->
            <flux:card class="overflow-hidden">
                <div class="bg-gradient-to-r from-pink-50 to-purple-50 dark:from-pink-950/50 dark:to-purple-950/50 p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <flux:heading size="lg" class="text-gray-900 dark:text-white">Recommended For You</flux:heading>
                            <div class="flex items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="flex items-center gap-1">
                                    <flux:icon name="sparkles" class="w-4 h-4 text-pink-500" />
                                    Smart matching
                                </span>
                                <span class="flex items-center gap-1">
                                    <flux:icon name="heart" class="w-4 h-4 text-purple-500" />
                                    Personalized picks
                                </span>
                            </div>
                        </div>
                        <flux:button href="{{ route('discover') }}" variant="ghost" icon-trailing="arrow-right" class="text-pink-600 hover:text-pink-800 dark:text-pink-400 dark:hover:text-pink-200">
                            View All
                        </flux:button>
                    </div>
                </div>

                <div class="p-6">
                    @if($this->getRecommendedCampaigns()->count() > 0)
                        <div class="space-y-4">
                            @foreach($this->getRecommendedCampaigns() as $matchData)
                                @php
                                    $campaign = $matchData['campaign'];
                                    $matchScore = $matchData['match_score'];
                                    $matchReasons = $matchData['match_reasons'];
                                    $distanceDisplay = $matchData['distance_display'];
                                    $postedAgo = $matchData['posted_ago'];
                                @endphp

                                <div class="group relative bg-gradient-to-r from-white to-pink-50/30 dark:from-gray-800 dark:to-pink-900/10 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-pink-300 dark:hover:border-pink-600 transition-all duration-300">
                                    <div class="p-6">
                                        <div class="flex items-start justify-between mb-4">
                                            <div class="flex items-center gap-4">
                                                <flux:avatar
                                                    name="{{ $campaign->user->currentBusiness?->business_name ?? $campaign->user->name }}"
                                                    size="base"
                                                    class="flex-shrink-0 shadow-sm bg-gradient-to-br from-pink-500 to-purple-600"
                                                />
                                                <div class="space-y-2">
                                                    <div class="flex items-center gap-3">
                                                        <div class="flex items-center gap-1 px-3 py-1 bg-gradient-to-r from-pink-600 to-purple-600 text-white rounded-full">
                                                            <flux:icon name="sparkles" class="w-3 h-3" />
                                                            <flux:text size="xs" class="font-medium">{{ $matchScore }}% Match</flux:text>
                                                        </div>
                                                        <flux:text size="xs" class="text-gray-500 dark:text-gray-400">{{ $distanceDisplay }}</flux:text>
                                                        <flux:text size="xs" class="text-gray-500 dark:text-gray-400">{{ $postedAgo }}</flux:text>
                                                    </div>
                                                    <flux:heading size="sm" class="text-gray-900 dark:text-white">
                                                        {{ Str::limit($campaign->campaign_goal, 80) }}
                                                    </flux:heading>
                                                </div>
                                            </div>
                                        </div>

                                        <flux:text size="sm" class="text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                            {{ Str::limit($campaign->campaign_description ?? 'Campaign details will be provided upon acceptance.', 150) }}
                                        </flux:text>

                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-6 text-sm text-gray-600 dark:text-gray-400">
                                                <div class="flex items-center gap-1">
                                                    <flux:icon name="currency-dollar" class="w-4 h-4 text-green-500" />
                                                    <span class="font-medium">{{ $campaign->compensation_display }}</span>
                                                </div>
                                                <div class="flex items-center gap-1">
                                                    <flux:icon name="users" class="w-4 h-4 text-blue-500" />
                                                    <span>{{ $campaign->influencer_count }} spots</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <flux:button href="{{ route('campaigns.show', $campaign) }}" variant="ghost" size="sm">
                                                    View Details
                                                </flux:button>
                                                <flux:button variant="primary" size="sm" class="shadow-lg shadow-purple-500/25">
                                                    Apply Now
                                                </flux:button>
                                            </div>
                                        </div>

                                        @if(count($matchReasons) > 0)
                                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                                <div class="flex items-center gap-2 text-xs text-pink-600 dark:text-pink-400">
                                                    <flux:icon name="check-badge" class="w-3 h-3" />
                                                    <span class="font-medium">Perfect match:</span>
                                                    <span>{{ implode(', ', $matchReasons) }}</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Subtle accent line -->
                                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-pink-500 to-purple-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-20 h-20 bg-gradient-to-br from-pink-100 to-purple-100 dark:from-pink-900/30 dark:to-purple-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <flux:icon name="sparkles" class="w-10 h-10 text-pink-600 dark:text-pink-400" />
                            </div>
                            <flux:heading size="xl" class="mb-3 text-gray-900 dark:text-white">Discover Perfect Matches</flux:heading>
                            <flux:text class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">
                                We're learning about your preferences. Browse campaigns to help us understand what you're looking for.
                            </flux:text>
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <flux:button href="{{ route('discover') }}" variant="primary" icon="sparkles" class="shadow-lg shadow-pink-500/25">
                                    Discover Campaigns
                                </flux:button>
                                <flux:button href="{{ route('search') }}" variant="ghost" icon="magnifying-glass">
                                    Search Businesses
                                </flux:button>
                            </div>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>

        <!-- Enhanced Sidebar -->
        <div class="space-y-8">
            <!-- My Applications -->
            <flux:card class="overflow-hidden">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-950/50 dark:to-indigo-950/50 p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <flux:heading size="lg" class="text-gray-900 dark:text-white">My Applications</flux:heading>
                            <div class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                                <flux:icon name="paper-airplane" class="w-4 h-4 text-blue-500" />
                                <span>{{ $this->getInfluencerApplications()->count() }} total</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if($this->getInfluencerApplications()->count() > 0)
                        <div class="space-y-3">
                            @foreach($this->getInfluencerApplications()->take(4) as $application)
                                <div class="group relative bg-gradient-to-r from-white to-blue-50/30 dark:from-gray-800 dark:to-blue-900/10 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300">
                                    <!-- Status indicator -->
                                    <div class="absolute top-3 right-3">
                                        <div class="flex items-center gap-1 px-2 py-1 rounded-full
                                            {{ $application->status === \App\Enums\CampaignApplicationStatus::PENDING ? 'bg-amber-100 dark:bg-amber-900/30' :
                                               ($application->status === \App\Enums\CampaignApplicationStatus::ACCEPTED ? 'bg-green-100 dark:bg-green-900/30' :
                                                'bg-red-100 dark:bg-red-900/30') }}">
                                            <div class="w-1.5 h-1.5 rounded-full
                                                {{ $application->status === \App\Enums\CampaignApplicationStatus::PENDING ? 'bg-amber-500 animate-pulse' :
                                                   ($application->status === \App\Enums\CampaignApplicationStatus::ACCEPTED ? 'bg-green-500' :
                                                    'bg-red-500') }}"></div>
                                            <flux:text size="xs" class="font-medium
                                                {{ $application->status === \App\Enums\CampaignApplicationStatus::PENDING ? 'text-amber-700 dark:text-amber-300' :
                                                   ($application->status === \App\Enums\CampaignApplicationStatus::ACCEPTED ? 'text-green-700 dark:text-green-300' :
                                                    'text-red-700 dark:text-red-300') }}">
                                                {{ $application->status->label() }}
                                            </flux:text>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <div class="space-y-3 pr-20">
                                            <flux:heading size="sm" class="text-gray-900 dark:text-white font-medium">
                                                {{ Str::limit($application->campaign->campaign_goal, 50) }}
                                            </flux:heading>
                                            <flux:text size="xs" class="text-gray-600 dark:text-gray-400 line-clamp-1">
                                                {{ $application->campaign->business->name }}
                                            </flux:text>
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                                    <flux:icon name="currency-dollar" class="w-3 h-3" />
                                                    <span>{{ $application->campaign->compensation_display }}</span>
                                                </div>
                                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                                    <flux:icon name="clock" class="w-3 h-3" />
                                                    <span>{{ $application->submitted_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <flux:button
                                                href="{{ route('campaigns.show', $application->campaign) }}"
                                                variant="ghost"
                                                size="xs"
                                                class="w-full text-center bg-blue-50 dark:bg-blue-700/50 hover:bg-blue-100 dark:hover:bg-blue-700 font-medium"
                                            >
                                                View Campaign →
                                            </flux:button>
                                        </div>
                                    </div>

                                    <!-- Subtle accent line -->
                                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-blue-500 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <flux:icon name="paper-airplane" class="w-8 h-8 text-blue-600 dark:text-blue-400" />
                            </div>
                            <flux:heading size="sm" class="mb-2 text-gray-900 dark:text-white">No applications yet</flux:heading>
                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                                Applications you submit will appear here
                            </flux:text>
                        </div>
                    @endif
                </div>
            </flux:card>

            <!-- Active Work -->
            <flux:card class="overflow-hidden">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-950/50 dark:to-emerald-950/50 p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <flux:heading size="lg" class="text-gray-900 dark:text-white">Active Work</flux:heading>
                            <div class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400">
                                @if($this->getActiveCampaigns()->count() > 0)
                                    <flux:icon name="fire" class="w-4 h-4 text-green-500" />
                                    <span>{{ $this->getActiveCampaigns()->count() }} campaigns in progress</span>
                                @else
                                    <flux:icon name="check-circle" class="w-4 h-4 text-green-500" />
                                    <span>Ready for new projects</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if($this->getActiveCampaigns()->count() > 0)
                        <div class="space-y-3">
                            @foreach($this->getActiveCampaigns()->take(3) as $application)
                                <div class="group relative bg-gradient-to-r from-white to-green-50/30 dark:from-gray-800 dark:to-green-900/10 rounded-lg border border-green-200 dark:border-green-700 hover:shadow-lg hover:border-green-300 dark:hover:border-green-600 transition-all duration-300">
                                    <!-- Status indicator -->
                                    <div class="absolute top-3 right-3">
                                        <div class="flex items-center gap-1 px-2 py-1 bg-green-100 dark:bg-green-900/30 rounded-full">
                                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                                            <flux:text size="xs" class="font-medium text-green-700 dark:text-green-300">Active</flux:text>
                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <div class="space-y-3 pr-16">
                                            <flux:heading size="sm" class="text-gray-900 dark:text-white font-medium">
                                                {{ Str::limit($application->campaign->project_name, 45) }}
                                            </flux:heading>
                                            <flux:text size="xs" class="text-gray-600 dark:text-gray-400 line-clamp-1">
                                                {{ $application->campaign->business->name }}
                                            </flux:text>
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                                    <flux:icon name="currency-dollar" class="w-3 h-3" />
                                                    <span>{{ $application->campaign->compensation_display }}</span>
                                                </div>
                                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                                    <flux:icon name="calendar" class="w-3 h-3" />
                                                    <span>Due {{ $application->campaign->campaign_completion_date?->format('M j') ?? 'TBD' }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <flux:button
                                                href="{{ route('campaigns.show', $application->campaign) }}"
                                                variant="primary"
                                                size="xs"
                                                class="w-full text-center shadow-lg shadow-green-500/25"
                                            >
                                                Work on Campaign →
                                            </flux:button>
                                        </div>
                                    </div>

                                    <!-- Subtle accent line -->
                                    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-gradient-to-r from-green-500 to-emerald-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <div class="w-16 h-16 bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <flux:icon name="check-circle" class="w-8 h-8 text-green-600 dark:text-green-400" />
                            </div>
                            <flux:heading size="sm" class="mb-2 text-gray-900 dark:text-white">Ready for action!</flux:heading>
                            <flux:text size="sm" class="text-gray-500 dark:text-gray-400">
                                Active campaigns will appear here
                            </flux:text>
                        </div>
                    @endif
                </div>
            </flux:card>
        </div>
    </div>
</div>
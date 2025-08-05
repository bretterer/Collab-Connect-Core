<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Navigation -->
    <div class="mb-6">
        <a href="{{ route('dashboard') }}" class="flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    <!-- Application Header -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white font-bold text-xl">
                        {{ $application->user->initials() }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $application->user->name }}</h1>
                        <p class="text-gray-600 dark:text-gray-400">Applied {{ $application->submitted_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        {{ $application->status === \App\Enums\CampaignApplicationStatus::PENDING ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' : 
                           ($application->status === \App\Enums\CampaignApplicationStatus::ACCEPTED ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' : 
                            'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400') }}">
                        {{ $application->status->label() }}
                    </span>
                    @if($application->status === \App\Enums\CampaignApplicationStatus::PENDING)
                        <div class="flex space-x-2">
                            <button wire:click="acceptApplication" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors">
                                Accept Application
                            </button>
                            <button wire:click="declineApplication" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md transition-colors">
                                Decline Application
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Campaign Context -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg mb-6">
        <div class="px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Campaign Applied For</h2>
            <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <h3 class="font-medium text-gray-900 dark:text-white mb-2">{{ $application->campaign->campaign_goal }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $application->campaign->campaign_description }}</p>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Compensation:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $application->campaign->compensation_display }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Type:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $application->campaign->campaign_type?->label() }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Location:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $application->campaign->target_zip_code ?? 'Any' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Deadline:</span>
                        <span class="font-medium text-gray-900 dark:text-white ml-1">{{ $application->campaign->application_deadline?->format('M j, Y') ?? 'No deadline' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Application Message -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Application Message</h2>
                    <div class="prose dark:prose-invert max-w-none">
                        <p class="whitespace-pre-wrap text-gray-700 dark:text-gray-300">{{ $application->message }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Influencer Profile -->
        <div class="space-y-6">
            <!-- Key Stats -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Influencer Profile</h3>
                    @if($application->user->influencerProfile)
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-500 dark:text-gray-400">Followers:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ number_format($application->user->influencerProfile->follower_count ?? 0) }}</span>
                            </div>
                            @if($application->user->influencerProfile->primary_niche)
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Primary Niche:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $application->user->influencerProfile->primary_niche->label() }}</span>
                                </div>
                            @endif
                            @if($application->user->influencerProfile->engagement_rate)
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Engagement Rate:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $application->user->influencerProfile->engagement_rate }}%</span>
                                </div>
                            @endif
                            @if($application->user->influencerProfile->primary_zip_code)
                                <div class="flex justify-between">
                                    <span class="text-gray-500 dark:text-gray-400">Location:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $application->user->influencerProfile->primary_zip_code }}</span>
                                </div>
                            @endif
                            @if($application->user->influencerProfile->collaboration_goals)
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400 block mb-2">Goals:</span>
                                    <div class="flex flex-wrap gap-1">
                                        @if(is_array($application->user->influencerProfile->collaboration_goals))
                                            @foreach($application->user->influencerProfile->collaboration_goals as $goal)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/20 dark:text-purple-400">
                                                    {{ is_string($goal) ? $goal : (is_object($goal) ? $goal->label() : '') }}
                                                </span>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">No profile information available.</p>
                    @endif
                </div>
            </div>

            <!-- Social Media Accounts -->
            @if($application->user->socialMediaAccounts && $application->user->socialMediaAccounts->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Social Media</h3>
                        <div class="space-y-3">
                            @foreach($application->user->socialMediaAccounts as $account)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                            {{ $account->platform }}
                                        </span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $account->handle }}</span>
                                    </div>
                                    @if($account->follower_count)
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($account->follower_count) }} followers</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Content Categories -->
            @if($application->user->influencerProfile && $application->user->influencerProfile->content_categories)
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                    <div class="px-6 py-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Content Categories</h3>
                        <div class="flex flex-wrap gap-2">
                            @if(is_array($application->user->influencerProfile->content_categories))
                                @foreach($application->user->influencerProfile->content_categories as $category)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                        {{ $category }}
                                    </span>
                                @endforeach
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                                    {{ $application->user->influencerProfile->content_categories }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Bar (Fixed at bottom for pending applications) -->
    @if($application->status === \App\Enums\CampaignApplicationStatus::PENDING)
        <div class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-4 py-3 shadow-lg">
            <div class="max-w-4xl mx-auto flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white font-medium">
                        {{ $application->user->initials() }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $application->user->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $application->user->influencerProfile ? number_format($application->user->influencerProfile->follower_count ?? 0) . ' followers' : 'Application pending review' }}</p>
                    </div>
                </div>
                <div class="flex space-x-3">
                    <button wire:click="declineApplication" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 font-medium rounded-md transition-colors">
                        Decline
                    </button>
                    <button wire:click="acceptApplication" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-md transition-colors">
                        Accept Application
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
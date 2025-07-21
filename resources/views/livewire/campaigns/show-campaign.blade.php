<div>
    <div class="max-w-4xl mx-auto py-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Campaign Details</h1>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">View your campaign information</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button wire:click="backToCampaigns" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700">
                        Back to Campaigns
                    </button>
                    @if($campaign->isDraft())
                        <button wire:click="editCampaign" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700">
                            Continue Editing
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Campaign Status Badge -->
        <div class="mb-6">
            @if($campaign->isDraft())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                    Draft
                </span>
            @elseif($campaign->isPublished())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                    Published
                </span>
            @elseif($campaign->isScheduled())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                    Scheduled for {{ $campaign->scheduled_date->format('M j, Y') }}
                </span>
            @elseif($campaign->isArchived())
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                    Archived
                </span>
            @endif
        </div>

        <!-- Campaign Details Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Campaign Goal -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Campaign Goal</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $campaign->campaign_goal }}</p>
                </div>

                <!-- Campaign Description -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Description</h3>
                    <p class="text-gray-700 dark:text-gray-300">{{ $campaign->campaign_description }}</p>
                </div>

                <!-- Additional Requirements -->
                @if($campaign->additional_requirements)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Additional Requirements</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $campaign->additional_requirements }}</p>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Campaign Details -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Campaign Details</h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign Type</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $campaign->campaign_type }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Budget</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">${{ number_format($campaign->budget) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Influencers Needed</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $campaign->influencer_count }}</dd>
                        </div>
                        @if($campaign->target_zip_code)
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Zip Code</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $campaign->target_zip_code }}</dd>
                            </div>
                        @endif
                        @if($campaign->target_area)
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Area</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $campaign->target_area }}</dd>
                            </div>
                        @endif
                        @if($campaign->application_deadline)
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Application Deadline</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $campaign->application_deadline->format('M j, Y') }}</dd>
                            </div>
                        @endif
                        @if($campaign->campaign_completion_date)
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Completion Date</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $campaign->campaign_completion_date->format('M j, Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>

                <!-- Social Media Requirements -->
                @if($campaign->social_requirements && count($campaign->social_requirements) > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Social Media Requirements</h3>
                        <div class="space-y-2">
                            @foreach($campaign->social_requirements as $platform => $required)
                                @if($required)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($platform) }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Placement Requirements -->
                @if($campaign->placement_requirements && count($campaign->placement_requirements) > 0)
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Placement Requirements</h3>
                        <div class="space-y-2">
                            @foreach($campaign->placement_requirements as $placement => $required)
                                @if($required)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ ucfirst($placement) }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
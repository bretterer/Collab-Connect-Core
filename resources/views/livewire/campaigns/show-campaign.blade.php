<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    @if($isOwner)
                        <a href="{{ route('campaigns.index') }}" class="flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Back to Campaigns
                        </a>
                    @else
                        <a href="{{ route('discover') }}" class="flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Back to Discover
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Campaign Banner -->
        <div class="mb-8">
            <div class="relative h-64 bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 rounded-lg overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 bg-black bg-opacity-10"></div>

                <!-- Banner Content -->
                <div class="relative h-full flex items-center justify-center">
                    <div class="text-center text-white">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold mb-2">Campaign Opportunity</h2>
                        <p class="text-lg opacity-90">{{ $campaign->campaign_type->label() }}</p>
                    </div>
                </div>

                <!-- Campaign Status Badge -->
                <div class="absolute top-4 right-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white bg-opacity-90 text-gray-800 shadow-md">
                        {{ $campaign->status->label() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Hero Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-8 text-white">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h1 class="text-4xl font-bold mb-4">{{ $campaign->campaign_goal }}</h1>
                        <p class="text-xl opacity-90 mb-6">{{ $campaign->campaign_description }}</p>

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $campaign->influencer_count }}</div>
                                <div class="text-sm opacity-75">Influencers</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $campaign->campaign_type->label() }}</div>
                                <div class="text-sm opacity-75">Type</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $campaign->compensation?->compensation_display ?? 'Not set' }}</div>
                                <div class="text-sm opacity-75">Compensation</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $campaign->target_zip_code ?? 'Any' }}</div>
                                <div class="text-sm opacity-75">Location</div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="ml-8">
                        @if($isOwner)
                            <div class="flex items-center space-x-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->getApplicationsCount() }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">Total Applications</div>
                                </div>
                                @if($this->getPendingApplicationsCount() > 0)
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $this->getPendingApplicationsCount() }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">Pending Review</div>
                                    </div>
                                @endif
                            </div>
                        @else
                            @livewire('campaigns.apply-to-campaign', ['campaign' => $campaign])
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Details Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Campaign Details</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Campaign Type</h3>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $campaign->campaign_type->label() }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Compensation</h3>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $campaign->compensation?->compensation_display ?? 'Not set' }}
                                </p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Target Location</h3>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $campaign->target_zip_code ?? 'Anywhere' }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Application Deadline</h3>
                                <p class="text-lg font-medium {{ $campaign->application_deadline && $campaign->application_deadline->isPast() ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                                    {{ $campaign->application_deadline ? $campaign->application_deadline->format('M j, Y') : 'No deadline' }}
                                </p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Campaign Duration</h3>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $campaign->campaign_completion_date ? $campaign->campaign_completion_date->format('M j, Y') : 'Not specified' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ $campaign->status->label() }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Requirements Card -->
                @if($campaign->brief?->additional_requirements)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Requirements</h2>
                        <div class="prose dark:prose-invert max-w-none">
                            {!! nl2br(e($campaign->brief->additional_requirements)) !!}
                        </div>
                    </div>
                @endif

                <!-- Social Requirements Card -->
                @if(!empty($campaign->requirements?->social_requirements))
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Social Media Requirements</h2>
                        <div class="flex flex-wrap gap-3">
                            @if(is_array($campaign->requirements->social_requirements))
                                @foreach($campaign->requirements->social_requirements as $requirement)
                                    @if(is_string($requirement))
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ ucwords(str_replace('_', ' ', $requirement)) }}
                                        </span>
                                    @elseif(is_array($requirement))
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ implode(', ', $requirement) }}
                                        </span>
                                    @endif
                                @endforeach
                            @else
                                <span class="text-gray-500 dark:text-gray-400">No specific requirements</span>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Applications Section (for campaign owners) -->
                @if($isOwner)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Applications</h2>
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $this->getApplicationsCount() }} total
                                </span>
                                @if($this->getPendingApplicationsCount() > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                        {{ $this->getPendingApplicationsCount() }} pending
                                    </span>
                                @endif
                            </div>
                        </div>

                        @if($this->getApplicationsCount() > 0)
                            <div class="space-y-4">
                                @foreach($campaign->applications()->with(['user.influencerProfile'])->latest('submitted_at')->take(3)->get() as $application)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                    {{ $application->user->initials() }}
                                                </div>
                                                <div>
                                                    <h4 class="font-medium text-gray-900 dark:text-white">{{ $application->user->name }}</h4>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $application->submitted_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : ($application->status === 'accepted' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300') }}">
                                                {{ ucfirst($application->status) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ Str::limit($application->message, 100) }}</p>
                                        <div class="mt-3">
                                            <a href="{{ route('campaigns.applications', $campaign) }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                                View all applications →
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No applications yet</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Applications will appear here once influencers start applying.</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Business Info Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Business Information</h3>
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                            {{ substr($campaign->user->businessProfile?->business_name ?? $campaign->user->name, 0, 2) }}
                        </div>
                        <div class="ml-4">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $campaign->user->businessProfile?->business_name ?? $campaign->user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $campaign->user->businessProfile?->industry?->label() ?? 'Business' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        @if($isOwner)
                            Campaign Management
                        @else
                            Quick Actions
                        @endif
                    </h3>
                    <div class="space-y-3">
                        @if($isOwner)
                            <a href="{{ route('campaigns.edit', $campaign) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 text-center block">
                                Edit Campaign
                            </a>
                            @if($campaign->status === \App\Enums\CampaignStatus::PUBLISHED)
                                <button wire:click="unpublishCampaign" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                                    Unpublish Campaign
                                </button>
                            @endif
                            <button wire:click="archiveCampaign" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                                Archive Campaign
                            </button>
                            <a href="{{ route('dashboard') }}" class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-3 px-4 rounded-lg transition-colors duration-200 text-center block">
                                Back to Dashboard
                            </a>
                        @else
                            @livewire('campaigns.apply-to-campaign', ['campaign' => $campaign, 'buttonText' => 'Apply To Campaign'])
                            <a href="{{ route('discover') }}" class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-3 px-4 rounded-lg transition-colors duration-200 text-center block">
                                Back to Discover
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

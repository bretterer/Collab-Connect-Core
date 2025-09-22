<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.campaigns.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ Str::limit($campaign->campaign_goal, 60) }}</h1>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $campaign->status === \App\Enums\CampaignStatus::PUBLISHED ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                       ($campaign->status === \App\Enums\CampaignStatus::DRAFT ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' :
                        ($campaign->status === \App\Enums\CampaignStatus::SCHEDULED ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400' :
                         'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400')) }}">
                    {{ $campaign->status->label() }}
                </span>
            </div>
            <p class="text-gray-600 dark:text-gray-400 mt-2">
                Created {{ $campaign->created_at->format('F j, Y') }} by {{ $campaign->business->name }}
            </p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.campaigns.edit', $campaign) }}">
                <flux:button variant="outline" icon="pencil">Edit Campaign</flux:button>
            </a>
            <a href="{{ route('campaigns.show', $campaign) }}" target="_blank">
                <flux:button variant="ghost" icon="arrow-top-right-on-square">View Public</flux:button>
            </a>
        </div>
    </div>

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

    <!-- Campaign Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Campaign Details -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Campaign Details</h3>
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign Goal</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->campaign_goal }}</dd>
                        </div>
                        @if($campaign->campaign_description)
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->campaign_description }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign Type</dt>
                            @foreach($campaign->campaign_type as $campaignType)
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaignType->label() ?? 'Not specified' }}</dd>
                            @endforeach
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Influencers</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->influencer_count ?? 'Not specified' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Location</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->target_zip_code ?? 'Not specified' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Compensation Type</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->compensation_type?->label() ?? 'Not specified' }}</dd>
                        </div>
                        @if($campaign->application_deadline)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Application Deadline</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->application_deadline->format('F j, Y') }}</dd>
                            </div>
                        @endif
                        @if($campaign->campaign_completion_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Completion Date</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->campaign_completion_date->format('F j, Y') }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Step</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->current_step ?? 'Not specified' }} of 4</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                            <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->created_at->format('F j, Y g:i A') }}</dd>
                        </div>
                        @if($campaign->published_at)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Published</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->published_at->format('F j, Y g:i A') }}</dd>
                            </div>
                        @endif
                        @if($campaign->scheduled_date)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Scheduled</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $campaign->scheduled_date->format('F j, Y') }}</dd>
                            </div>
                        @endif
                    </dl>
                </div>
            </div>

            <!-- Business Owner -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Business Owner</h3>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-400 rounded-full flex items-center justify-center text-white text-lg font-medium">
                            {{ $campaign->business->owner->first()->initials() }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">
                                    {{ $campaign->business->name }}
                                </h4>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                                    {{ $campaign->business->owner->first()->account_type->label() }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $campaign->business->email }}</p>
                            @if($campaign->business->industry)
                                <p class="text-sm text-gray-600 dark:text-gray-300">{{ $campaign->business->industry ?? 'Industry not specified' }}</p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.users.show', $campaign->business->owner->first()->id) }}" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                                View Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Application Stats -->
            @php $stats = $this->getCampaignStats(); @endphp
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Application Statistics</h3>
                    <dl class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Applications</dt>
                            <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_applications'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Pending Review</dt>
                            <dd class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending_applications'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Accepted</dt>
                            <dd class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['accepted_applications'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Rejected</dt>
                            <dd class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['rejected_applications'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="w-full">
                            <flux:button variant="outline" class="w-full">Edit Campaign</flux:button>
                        </a>
                        <a href="{{ route('campaigns.show', $campaign) }}" target="_blank" class="w-full">
                            <flux:button variant="ghost" class="w-full">View Public Page</flux:button>
                        </a>
                        @if($stats['total_applications'] > 0)
                            <a href="{{ route('campaigns.applications', $campaign) }}" class="w-full">
                                <flux:button variant="ghost" class="w-full">View Applications</flux:button>
                            </a>
                        @endif
                        <a href="{{ route('admin.users.show', $campaign->business->owner->first()->id) }}" class="w-full">
                            <flux:button variant="ghost" class="w-full">View Business Owner</flux:button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Applications -->
    @if($stats['total_applications'] > 0)
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Applications</h3>
                    @if($stats['total_applications'] > 5)
                        <a href="{{ route('campaigns.applications', $campaign) }}" class="text-sm text-red-600 hover:text-red-800 dark:text-red-400 font-medium">
                            View All {{ $stats['total_applications'] }} Applications →
                        </a>
                    @endif
                </div>

                <div class="space-y-4">
                    @foreach($campaign->applications()->latest()->limit(5)->get() as $application)
                        <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded">
                            <div class="flex items-center space-x-3 flex-1 min-w-0">
                                <div class="w-8 h-8 bg-gradient-to-br from-pink-400 to-purple-400 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                    {{ $application->user->initials() }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $application->user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $application->submitted_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ $application->status === \App\Enums\CampaignApplicationStatus::PENDING ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400' :
                                       ($application->status === \App\Enums\CampaignApplicationStatus::ACCEPTED ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400' :
                                        'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400') }}">
                                    {{ $application->status->label() }}
                                </span>
                                <a href="{{ route('admin.users.show', $application->user) }}" class="text-red-600 hover:text-red-800 dark:text-red-400 text-xs font-medium">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

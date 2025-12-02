<div>
    @if(!auth()->user()->profile->subscribed('default'))
    <livewire:components.subscription-prompt
        variant="blue"
        heading="Create Campaigns with CollabConnect"
        description="Subscribe to a plan to unlock powerful campaign management features."
        :features="[
            'Create and manage unlimited campaigns',
            'Track campaign performance and analytics',
            'Collaborate with influencers seamlessly',
            'Access premium support and resources'
        ]"
    />
    @endif
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold mb-2">
                                My Campaigns 📊
                            </h1>
                            <p class="text-blue-100 text-lg">
                                Manage your campaigns and track their performance
                            </p>
                        </div>
                        @if(auth()->user()->profile->subscribed('default'))
                        <a href="{{ route('campaigns.create') }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Campaign
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button
                    wire:click="setActiveTab('drafts')"
                    class="px-3 py-2 font-medium text-sm rounded-md {{ $activeTab === 'drafts' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    Drafts ({{ $this->getDrafts()->count() }})
                </button>
                <button
                    wire:click="setActiveTab('scheduled')"
                    class="px-3 py-2 font-medium text-sm rounded-md {{ $activeTab === 'scheduled' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    Scheduled ({{ $this->getScheduled()->count() }})
                </button>
                <button
                    wire:click="setActiveTab('published')"
                    class="px-3 py-2 font-medium text-sm rounded-md {{ $activeTab === 'published' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    Published ({{ $this->getPublished()->count() }})
                </button>
                <button
                    wire:click="setActiveTab('in_progress')"
                    class="px-3 py-2 font-medium text-sm rounded-md {{ $activeTab === 'in_progress' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    In Progress ({{ $this->getInProgress()->count() }})
                </button>
                <button
                    wire:click="setActiveTab('completed')"
                    class="px-3 py-2 font-medium text-sm rounded-md {{ $activeTab === 'completed' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    Completed ({{ $this->getCompleted()->count() }})
                </button>
                <button
                    wire:click="setActiveTab('archived')"
                    class="px-3 py-2 font-medium text-sm rounded-md {{ $activeTab === 'archived' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}">
                    Archived ({{ $this->getArchived()->count() }})
                </button>
            </nav>
        </div>

        <!-- Campaigns List -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            @if($activeTab === 'drafts')
                @if($this->getDrafts()->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getDrafts() as $campaign)
                            <div class="p-6" wire:key="campaign-{{ $campaign->id }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                Draft
                                            </span>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ Str::limit($campaign->campaign_goal, 60) }}
                                            </h3>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            {{ Str::limit($campaign->campaign_description, 120) }}
                                        </p>
                                        <div class="mt-3 flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                                            <span>Compensation: {{ $campaign->compensation_display }}</span>
                                            <span>Influencers: {{ $campaign->influencer_count }}</span>
                                            <span>Step {{ $campaign->current_step }} of 4</span>
                                            <span>Updated {{ $campaign->updated_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('campaigns.edit', $campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            Continue Editing
                                        </a>
                                        <button
                                            wire:click="confirmArchive({{ $campaign->id }})"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            Archive
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No draft campaigns</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new campaign.</p>
                        <div class="mt-6">
                            <a href="{{ route('campaigns.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                Create Campaign
                            </a>
                        </div>
                    </div>
                @endif

            @elseif($activeTab === 'published')
                @if($this->getPublished()->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getPublished() as $campaign)
                            <div class="p-6" wire:key="campaign-{{ $campaign->id }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Published
                                            </span>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ Str::limit($campaign->campaign_goal, 60) }}
                                            </h3>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            {{ Str::limit($campaign->campaign_description, 120) }}
                                        </p>
                                        <div class="mt-3 flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                                            <span>Compensation: {{ $campaign->compensation_display }}</span>
                                            <span>Influencers: {{ $campaign->influencer_count }}</span>
                                            <span>Published {{ $campaign->published_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('campaigns.show', $campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            View Details
                                        </a>
                                        <button
                                            wire:click="startCampaign({{ $campaign->id }})"
                                            class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                                            Start Campaign
                                        </button>
                                        <button
                                            wire:click="confirmArchive({{ $campaign->id }})"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            Archive
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No published campaigns</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Publish a campaign to see it here.</p>
                    </div>
                @endif

            @elseif($activeTab === 'scheduled')
                @if($this->getScheduled()->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getScheduled() as $campaign)
                            <div class="p-6" wire:key="campaign-{{ $campaign->id }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                Scheduled
                                            </span>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ Str::limit($campaign->campaign_goal, 60) }}
                                            </h3>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            {{ Str::limit($campaign->campaign_description, 120) }}
                                        </p>
                                        <div class="mt-3 flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                                            <span>Compensation: {{ $campaign->compensation_display }}</span>
                                            <span>Influencers: {{ $campaign->influencer_count }}</span>
                                            <span>Scheduled for {{ $campaign->scheduled_date->format('M j, Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('campaigns.show', $campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            View Details
                                        </a>
                                        <button
                                            wire:click="confirmArchive({{ $campaign->id }})"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            Archive
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No scheduled campaigns</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Schedule a campaign to see it here.</p>
                    </div>
                @endif

            @elseif($activeTab === 'in_progress')
                @if($this->getInProgress()->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getInProgress() as $campaign)
                            <div class="p-6" wire:key="campaign-{{ $campaign->id }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                                In Progress
                                            </span>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ Str::limit($campaign->campaign_goal, 60) }}
                                            </h3>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            {{ Str::limit($campaign->campaign_description, 120) }}
                                        </p>
                                        <div class="mt-3 flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                                            <span>Compensation: {{ $campaign->compensation_display }}</span>
                                            <span class="text-blue-600 font-medium">{{ $campaign->collaborations->count() }} Active Collaborations</span>
                                            @if($campaign->started_at)
                                                <span>Started {{ $campaign->started_at->diffForHumans() }}</span>
                                            @endif
                                            @if($campaign->campaign_completion_date)
                                                <span>Ends {{ $campaign->campaign_completion_date->format('M j, Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('campaigns.show', $campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            View Details
                                        </a>
                                        <button
                                            wire:click="completeCampaign({{ $campaign->id }})"
                                            class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300">
                                            Complete Campaign
                                        </button>
                                        <button
                                            wire:click="confirmArchive({{ $campaign->id }})"
                                            class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                            Archive
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No In Progress campaigns</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start a campaign with influencers to see it here.</p>
                    </div>
                @endif

            @elseif($activeTab === 'completed')
                @if($this->getCompleted()->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getCompleted() as $campaign)
                            <div class="p-6" wire:key="campaign-{{ $campaign->id }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-lime-100 text-lime-800 dark:bg-lime-900 dark:text-lime-200">
                                                Completed
                                            </span>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ Str::limit($campaign->campaign_goal, 60) }}
                                            </h3>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            {{ Str::limit($campaign->campaign_description, 120) }}
                                        </p>
                                        <div class="mt-3 flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                                            <span>Compensation: {{ $campaign->compensation_display }}</span>
                                            <span>{{ $campaign->collaborations->count() }} Collaborations</span>
                                            @if($campaign->completed_at)
                                                <span>Completed {{ $campaign->completed_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('campaigns.show', $campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No completed campaigns</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Completed campaigns will appear here.</p>
                    </div>
                @endif

            @elseif($activeTab === 'archived')
                @if($this->getArchived()->count() > 0)
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($this->getArchived() as $campaign)
                            <div class="p-6" wire:key="campaign-{{ $campaign->id }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Archived
                                            </span>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                                {{ Str::limit($campaign->campaign_goal, 60) }}
                                            </h3>
                                        </div>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            {{ Str::limit($campaign->campaign_description, 120) }}
                                        </p>
                                        <div class="mt-3 flex items-center space-x-6 text-sm text-gray-500 dark:text-gray-400">
                                            <span>Compensation: {{ $campaign->compensation_display }}</span>
                                            <span>Influencers: {{ $campaign->influencer_count }}</span>
                                            <span>Archived {{ $campaign->updated_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('campaigns.show', $campaign) }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-14 0h14"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No archived campaigns</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Archived campaigns will appear here.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Archive Confirmation Modal -->
    <flux:modal wire:model="showArchiveModal" class="space-y-6">
        <div class="flex items-center gap-3">
            <flux:icon.exclamation-triangle class="size-6 text-red-500" />
            <flux:heading size="lg">Archive Campaign</flux:heading>
        </div>

        <flux:text>
            Are you sure you want to archive this campaign? This action will remove it from active campaigns but you can still view it in the archived section.
        </flux:text>

        <div class="flex gap-2 justify-end">
            <flux:button variant="ghost" wire:click="closeArchiveModal">Cancel</flux:button>
            <flux:button variant="danger" wire:click="archiveCampaign">Archive Campaign</flux:button>
        </div>
    </flux:modal>
</div>

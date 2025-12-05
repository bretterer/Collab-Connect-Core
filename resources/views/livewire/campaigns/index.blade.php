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
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">Campaigns</flux:heading>
                <flux:text class="mt-1">Create and manage your influencer marketing campaigns</flux:text>
            </div>
            @if(auth()->user()->profile->subscribed('default'))
                <flux:button variant="primary" icon="plus" href="{{ route('campaigns.create') }}">
                    Create Campaign
                </flux:button>
            @endif
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <flux:card class="!p-4">
                <flux:text size="sm" class="text-zinc-500">Active</flux:text>
                <flux:heading size="xl" class="!mt-1">{{ $this->getPublished()->count() + $this->getInProgress()->count() }}</flux:heading>
            </flux:card>
            <flux:card class="!p-4">
                <flux:text size="sm" class="text-zinc-500">Drafts</flux:text>
                <flux:heading size="xl" class="!mt-1">{{ $this->getDrafts()->count() }}</flux:heading>
            </flux:card>
            <flux:card class="!p-4">
                <flux:text size="sm" class="text-zinc-500">Completed</flux:text>
                <flux:heading size="xl" class="!mt-1">{{ $this->getCompleted()->count() }}</flux:heading>
            </flux:card>
            <flux:card class="!p-4">
                <flux:text size="sm" class="text-zinc-500">Total</flux:text>
                <flux:heading size="xl" class="!mt-1">{{ $this->getDrafts()->count() + $this->getPublished()->count() + $this->getScheduled()->count() + $this->getInProgress()->count() + $this->getCompleted()->count() + $this->getArchived()->count() }}</flux:heading>
            </flux:card>
        </div>

        <!-- Tabs and Content -->
        <flux:card class="!p-0 overflow-hidden">
            <flux:tab.group>
                <flux:tabs wire:model="activeTab" class="border-b border-zinc-200 dark:border-zinc-700 px-4">
                    <flux:tab name="drafts" icon="pencil-square">
                        Drafts
                        @if($this->getDrafts()->count() > 0)
                            <flux:badge size="sm" color="zinc" class="ml-1.5">{{ $this->getDrafts()->count() }}</flux:badge>
                        @endif
                    </flux:tab>
                    <flux:tab name="scheduled" icon="clock">
                        Scheduled
                        @if($this->getScheduled()->count() > 0)
                            <flux:badge size="sm" color="blue" class="ml-1.5">{{ $this->getScheduled()->count() }}</flux:badge>
                        @endif
                    </flux:tab>
                    <flux:tab name="published" icon="globe-alt">
                        Published
                        @if($this->getPublished()->count() > 0)
                            <flux:badge size="sm" color="green" class="ml-1.5">{{ $this->getPublished()->count() }}</flux:badge>
                        @endif
                    </flux:tab>
                    <flux:tab name="in_progress" icon="play">
                        In Progress
                        @if($this->getInProgress()->count() > 0)
                            <flux:badge size="sm" color="orange" class="ml-1.5">{{ $this->getInProgress()->count() }}</flux:badge>
                        @endif
                    </flux:tab>
                    <flux:tab name="completed" icon="check-circle">
                        Completed
                        @if($this->getCompleted()->count() > 0)
                            <flux:badge size="sm" color="lime" class="ml-1.5">{{ $this->getCompleted()->count() }}</flux:badge>
                        @endif
                    </flux:tab>
                    <flux:tab name="archived" icon="archive-box">
                        Archived
                        @if($this->getArchived()->count() > 0)
                            <flux:badge size="sm" color="zinc" class="ml-1.5">{{ $this->getArchived()->count() }}</flux:badge>
                        @endif
                    </flux:tab>
                </flux:tabs>

                <!-- Drafts Panel -->
                <flux:tab.panel name="drafts" class="p-4">
                    @if($this->getDrafts()->count() > 0)
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column class="w-full">Campaign</flux:table.column>
                                <flux:table.column class="whitespace-nowrap">Progress</flux:table.column>
                                <flux:table.column class="whitespace-nowrap">Updated</flux:table.column>
                                <flux:table.column class="w-px"></flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach($this->getDrafts() as $campaign)
                                    <flux:table.row wire:key="draft-{{ $campaign->id }}">
                                        <flux:table.cell>
                                            <div>
                                                <div class="font-medium text-zinc-900 dark:text-white">
                                                    {{ Str::limit($campaign->campaign_goal, 60) ?: 'Untitled Campaign' }}
                                                </div>
                                                <div class="text-sm text-zinc-500 mt-0.5">
                                                    {{ $campaign->compensation_display ?: 'No compensation set' }}
                                                </div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <div class="w-20 h-1.5 bg-zinc-200 dark:bg-zinc-700 rounded-full overflow-hidden">
                                                    <div class="h-full bg-blue-500 rounded-full" style="width: {{ ($campaign->current_step / 4) * 100 }}%"></div>
                                                </div>
                                                <span class="text-sm text-zinc-500">{{ $campaign->current_step }}/4</span>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap text-sm text-zinc-500">
                                            {{ $campaign->updated_at->diffForHumans() }}
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">
                                            <div class="flex items-center gap-1">
                                                <flux:button size="sm" href="{{ route('campaigns.edit', $campaign) }}">
                                                    Continue
                                                </flux:button>
                                                <flux:dropdown position="bottom" align="end">
                                                    <flux:button size="sm" variant="ghost" icon="ellipsis-vertical" />
                                                    <flux:menu>
                                                        <flux:menu.item icon="trash" variant="danger" wire:click="confirmArchive({{ $campaign->id }})">
                                                            Archive
                                                        </flux:menu.item>
                                                    </flux:menu>
                                                </flux:dropdown>
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    @else
                        <div class="py-16 text-center">
                            <flux:icon.pencil-square class="size-12 mx-auto text-zinc-300 dark:text-zinc-600" />
                            <flux:heading class="mt-4">No draft campaigns</flux:heading>
                            <flux:text class="mt-2 text-zinc-500">Get started by creating a new campaign.</flux:text>
                            @if(auth()->user()->profile->subscribed('default'))
                                <div class="mt-6">
                                    <flux:button variant="primary" icon="plus" href="{{ route('campaigns.create') }}">
                                        Create Campaign
                                    </flux:button>
                                </div>
                            @endif
                        </div>
                    @endif
                </flux:tab.panel>

                <!-- Scheduled Panel -->
                <flux:tab.panel name="scheduled" class="p-4">
                    @if($this->getScheduled()->count() > 0)
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column class="w-full">Campaign</flux:table.column>
                                <flux:table.column class="whitespace-nowrap">Influencers</flux:table.column>
                                <flux:table.column class="whitespace-nowrap">Scheduled</flux:table.column>
                                <flux:table.column class="w-px"></flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach($this->getScheduled() as $campaign)
                                    <flux:table.row wire:key="scheduled-{{ $campaign->id }}">
                                        <flux:table.cell>
                                            <div>
                                                <div class="font-medium text-zinc-900 dark:text-white">
                                                    {{ Str::limit($campaign->campaign_goal, 60) }}
                                                </div>
                                                <div class="text-sm text-zinc-500 mt-0.5">
                                                    {{ $campaign->compensation_display }}
                                                </div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap text-zinc-600 dark:text-zinc-400">
                                            {{ $campaign->influencer_count }}
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">
                                            <flux:badge color="blue" size="sm" icon="clock">
                                                {{ $campaign->scheduled_date->format('M j, Y') }}
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">
                                            <div class="flex items-center gap-1">
                                                <flux:button size="sm" href="{{ route('campaigns.show', $campaign) }}">
                                                    View
                                                </flux:button>
                                                <flux:dropdown position="bottom" align="end">
                                                    <flux:button size="sm" variant="ghost" icon="ellipsis-vertical" />
                                                    <flux:menu>
                                                        <flux:menu.item icon="pencil" href="{{ route('campaigns.edit', $campaign) }}">
                                                            Edit
                                                        </flux:menu.item>
                                                        <flux:menu.separator />
                                                        <flux:menu.item icon="archive-box" variant="danger" wire:click="confirmArchive({{ $campaign->id }})">
                                                            Archive
                                                        </flux:menu.item>
                                                    </flux:menu>
                                                </flux:dropdown>
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    @else
                        <div class="py-16 text-center">
                            <flux:icon.clock class="size-12 mx-auto text-zinc-300 dark:text-zinc-600" />
                            <flux:heading class="mt-4">No scheduled campaigns</flux:heading>
                            <flux:text class="mt-2 text-zinc-500">Schedule a campaign to see it here.</flux:text>
                        </div>
                    @endif
                </flux:tab.panel>

                <!-- Published Panel -->
                <flux:tab.panel name="published" class="p-4">
                    @if($this->getPublished()->count() > 0)
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column class="w-full">Campaign</flux:table.column>
                                <flux:table.column class="whitespace-nowrap">Applications</flux:table.column>
                                <flux:table.column class="whitespace-nowrap">Published</flux:table.column>
                                <flux:table.column class="w-px"></flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach($this->getPublished() as $campaign)
                                    <flux:table.row wire:key="published-{{ $campaign->id }}">
                                        <flux:table.cell>
                                            <div>
                                                <div class="font-medium text-zinc-900 dark:text-white">
                                                    {{ Str::limit($campaign->campaign_goal, 60) }}
                                                </div>
                                                <div class="text-sm text-zinc-500 mt-0.5">
                                                    {{ $campaign->compensation_display }}
                                                </div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">
                                            @php
                                                $pendingCount = $campaign->applications()->where('status', \App\Enums\CampaignApplicationStatus::PENDING)->count();
                                                $acceptedCount = $campaign->applications()->where('status', \App\Enums\CampaignApplicationStatus::ACCEPTED)->count();
                                            @endphp
                                            <div class="flex items-center gap-1.5">
                                                @if($pendingCount > 0)
                                                    <flux:badge color="amber" size="sm">{{ $pendingCount }} pending</flux:badge>
                                                @endif
                                                @if($acceptedCount > 0)
                                                    <flux:badge color="green" size="sm">{{ $acceptedCount }} accepted</flux:badge>
                                                @endif
                                                @if($pendingCount === 0 && $acceptedCount === 0)
                                                    <span class="text-sm text-zinc-500">No applications yet</span>
                                                @endif
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap text-sm text-zinc-500">
                                            {{ $campaign->published_at->diffForHumans() }}
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">
                                            <div class="flex items-center gap-1">
                                                <flux:button size="sm" variant="primary" wire:click="startCampaign({{ $campaign->id }})">
                                                    Start
                                                </flux:button>
                                                <flux:dropdown position="bottom" align="end">
                                                    <flux:button size="sm" variant="ghost" icon="ellipsis-vertical" />
                                                    <flux:menu>
                                                        <flux:menu.item icon="eye" href="{{ route('campaigns.show', $campaign) }}">
                                                            View Details
                                                        </flux:menu.item>
                                                        <flux:menu.item icon="users" href="{{ route('campaigns.applications', $campaign) }}">
                                                            Applications
                                                        </flux:menu.item>
                                                        <flux:menu.item icon="pencil" href="{{ route('campaigns.edit', $campaign) }}">
                                                            Edit
                                                        </flux:menu.item>
                                                        <flux:menu.separator />
                                                        <flux:menu.item icon="archive-box" variant="danger" wire:click="confirmArchive({{ $campaign->id }})">
                                                            Archive
                                                        </flux:menu.item>
                                                    </flux:menu>
                                                </flux:dropdown>
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    @else
                        <div class="py-16 text-center">
                            <flux:icon.globe-alt class="size-12 mx-auto text-zinc-300 dark:text-zinc-600" />
                            <flux:heading class="mt-4">No published campaigns</flux:heading>
                            <flux:text class="mt-2 text-zinc-500">Publish a campaign to start receiving applications.</flux:text>
                        </div>
                    @endif
                </flux:tab.panel>

                <!-- In Progress Panel -->
                <flux:tab.panel name="in_progress" class="p-4">
                    @if($this->getInProgress()->count() > 0)
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column class="w-full">Campaign</flux:table.column>
                                <flux:table.column class="whitespace-nowrap">Collaborations</flux:table.column>
                                <flux:table.column class="whitespace-nowrap">Timeline</flux:table.column>
                                <flux:table.column class="w-px"></flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach($this->getInProgress() as $campaign)
                                    <flux:table.row wire:key="in-progress-{{ $campaign->id }}">
                                        <flux:table.cell>
                                            <div>
                                                <div class="font-medium text-zinc-900 dark:text-white">
                                                    {{ Str::limit($campaign->campaign_goal, 60) }}
                                                </div>
                                                <div class="text-sm text-zinc-500 mt-0.5">
                                                    {{ $campaign->compensation_display }}
                                                </div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">
                                            <flux:badge color="blue" size="sm" icon="users">
                                                {{ $campaign->collaborations->count() }} active
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">
                                            <div class="text-sm text-zinc-500">
                                                @if($campaign->started_at)
                                                    Started {{ $campaign->started_at->diffForHumans() }}
                                                @endif
                                                @if($campaign->campaign_completion_date)
                                                    <span class="block">Ends {{ $campaign->campaign_completion_date->format('M j') }}</span>
                                                @endif
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">
                                            <div class="flex items-center gap-1">
                                                <flux:button size="sm" variant="primary" wire:click="completeCampaign({{ $campaign->id }})">
                                                    Complete
                                                </flux:button>
                                                <flux:dropdown position="bottom" align="end">
                                                    <flux:button size="sm" variant="ghost" icon="ellipsis-vertical" />
                                                    <flux:menu>
                                                        <flux:menu.item icon="eye" href="{{ route('campaigns.show', $campaign) }}">
                                                            View Details
                                                        </flux:menu.item>
                                                        <flux:menu.separator />
                                                        <flux:menu.item icon="archive-box" variant="danger" wire:click="confirmArchive({{ $campaign->id }})">
                                                            Archive
                                                        </flux:menu.item>
                                                    </flux:menu>
                                                </flux:dropdown>
                                            </div>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    @else
                        <div class="py-16 text-center">
                            <flux:icon.play class="size-12 mx-auto text-zinc-300 dark:text-zinc-600" />
                            <flux:heading class="mt-4">No campaigns in progress</flux:heading>
                            <flux:text class="mt-2 text-zinc-500">Start a published campaign to begin collaborating with influencers.</flux:text>
                        </div>
                    @endif
                </flux:tab.panel>

                <!-- Completed Panel -->
                <flux:tab.panel name="completed" class="p-4">
                    @if($this->getCompleted()->count() > 0)
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column class="w-full">Campaign</flux:table.column>
                                <flux:table.column class="whitespace-nowrap">Collaborations</flux:table.column>
                                <flux:table.column class="whitespace-nowrap">Completed</flux:table.column>
                                <flux:table.column class="w-px"></flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach($this->getCompleted() as $campaign)
                                    <flux:table.row wire:key="completed-{{ $campaign->id }}">
                                        <flux:table.cell>
                                            <div>
                                                <div class="font-medium text-zinc-900 dark:text-white">
                                                    {{ Str::limit($campaign->campaign_goal, 60) }}
                                                </div>
                                                <div class="text-sm text-zinc-500 mt-0.5">
                                                    {{ $campaign->compensation_display }}
                                                </div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">
                                            <flux:badge color="lime" size="sm" icon="check-circle">
                                                {{ $campaign->collaborations->count() }} completed
                                            </flux:badge>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap text-sm text-zinc-500">
                                            @if($campaign->completed_at)
                                                {{ $campaign->completed_at->diffForHumans() }}
                                            @endif
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">
                                            <flux:button size="sm" href="{{ route('campaigns.show', $campaign) }}">
                                                View
                                            </flux:button>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    @else
                        <div class="py-16 text-center">
                            <flux:icon.check-circle class="size-12 mx-auto text-zinc-300 dark:text-zinc-600" />
                            <flux:heading class="mt-4">No completed campaigns</flux:heading>
                            <flux:text class="mt-2 text-zinc-500">Completed campaigns will appear here.</flux:text>
                        </div>
                    @endif
                </flux:tab.panel>

                <!-- Archived Panel -->
                <flux:tab.panel name="archived" class="p-4">
                    @if($this->getArchived()->count() > 0)
                        <flux:table>
                            <flux:table.columns>
                                <flux:table.column class="w-full">Campaign</flux:table.column>
                                <flux:table.column class="whitespace-nowrap">Influencers</flux:table.column>
                                <flux:table.column class="whitespace-nowrap">Archived</flux:table.column>
                                <flux:table.column class="w-px"></flux:table.column>
                            </flux:table.columns>
                            <flux:table.rows>
                                @foreach($this->getArchived() as $campaign)
                                    <flux:table.row wire:key="archived-{{ $campaign->id }}">
                                        <flux:table.cell>
                                            <div>
                                                <div class="font-medium text-zinc-900 dark:text-white">
                                                    {{ Str::limit($campaign->campaign_goal, 60) }}
                                                </div>
                                                <div class="text-sm text-zinc-500 mt-0.5">
                                                    {{ $campaign->compensation_display }}
                                                </div>
                                            </div>
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap text-zinc-600 dark:text-zinc-400">
                                            {{ $campaign->influencer_count }}
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap text-sm text-zinc-500">
                                            {{ $campaign->updated_at->diffForHumans() }}
                                        </flux:table.cell>
                                        <flux:table.cell class="whitespace-nowrap">
                                            <flux:button size="sm" href="{{ route('campaigns.show', $campaign) }}">
                                                View
                                            </flux:button>
                                        </flux:table.cell>
                                    </flux:table.row>
                                @endforeach
                            </flux:table.rows>
                        </flux:table>
                    @else
                        <div class="py-16 text-center">
                            <flux:icon.archive-box class="size-12 mx-auto text-zinc-300 dark:text-zinc-600" />
                            <flux:heading class="mt-4">No archived campaigns</flux:heading>
                            <flux:text class="mt-2 text-zinc-500">Archived campaigns will appear here.</flux:text>
                        </div>
                    @endif
                </flux:tab.panel>
            </flux:tab.group>
        </flux:card>
    </div>

    <!-- Archive Confirmation Modal -->
    <flux:modal wire:model="showArchiveModal" class="space-y-6">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center size-10 rounded-full bg-red-100 dark:bg-red-900/30">
                <flux:icon.exclamation-triangle class="size-5 text-red-600 dark:text-red-400" />
            </div>
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

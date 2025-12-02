<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-gray-900 dark:text-gray-100 prose dark:prose-invert">
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
                    @elseif(auth()->check() && auth()->user()->isInfluencerAccount())
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

        @if(!auth()->user()->profile->subscribed('default'))
        @if(auth()->user()->account_type === App\Enums\AccountType::INFLUENCER)
        <!-- Subscription Prompt -->
        <livewire:components.subscription-prompt
            variant="purple"
            heading="Subscribe to Apply"
            description="Ready to collaborate? Subscribe to unlock the ability to apply for campaigns and start building meaningful partnerships with brands."
            :features="[
                'Apply to unlimited campaigns',
                'Direct messaging with brands',
                'Advanced search & filters',
                'Priority support'
            ]"
        />
        @elseif(auth()->user()->account_type === App\Enums\AccountType::BUSINESS)
        <!-- Subscription Prompt -->
        <livewire:components.subscription-prompt
            variant="blue"
            heading="Subscribe to Create Campaigns"
            description="Unlock the full potential of your brand by subscribing. Create and manage campaigns, connect with top influencers, and drive impactful collaborations."
            :features="[
                'Create unlimited campaigns',
                'Access to influencer applications',
                'Advanced analytics & reporting',
                'Priority support'
            ]"
        />
        @endif
        @else
        <!-- Hero Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg mb-8 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-8 text-white">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h1 class="text-4xl font-bold mb-4">{{ $campaign->project_name }}</h1>
                        @if($campaign->campaign_objective)
                        <p class="text-xl opacity-90 mb-6">{!! $campaign->campaign_objective !!}</p>
                        @endif

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $campaign->influencer_count }}</div>
                                <div class="text-sm opacity-75">Influencers</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">
                                    @if($campaign->campaign_type && count($campaign->campaign_type) > 0)
                                    {{ count($campaign->campaign_type) }} Type{{ count($campaign->campaign_type) > 1 ? 's' : '' }}
                                    @else
                                    Multiple
                                    @endif
                                </div>
                                <div class="text-sm opacity-75">Type</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $campaign->application_deadline->diffForHumans() }}</div>
                                <div class="text-sm opacity-75">Application Deadline</div>
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
                            @if($this->getPendingApplicationsCount() > 0 && $campaign->user_id === auth()->id())
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $this->getPendingApplicationsCount() }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Pending Review</div>
                            </div>
                            @endif
                        </div>
                        @elseif(auth()->check() && auth()->user()->isInfluencerAccount())
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
                <!-- Brand Information Section -->
                @if($campaign->brand_overview || $campaign->brand_essence || $campaign->brand_pillars)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">About the Brand</h2>

                    @if($campaign->brand_overview)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Brand Overview</h3>
                        <p class="text-gray-700 dark:text-gray-300">{!! $campaign->brand_overview !!}</p>
                    </div>
                    @endif

                    @if($campaign->brand_essence)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Brand Essence</h3>
                        <p class="text-gray-700 dark:text-gray-300 font-medium">{!! $campaign->brand_essence !!}</p>
                    </div>
                    @endif

                    @if($campaign->brand_pillars && is_array($campaign->brand_pillars))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Brand Pillars</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($campaign->brand_pillars as $pillar)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ ucwords(str_replace('_', ' ', $pillar)) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($campaign->brand_story)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Brand Story</h3>
                        <p class="text-gray-700 dark:text-gray-300">{!! $campaign->brand_story !!}</p>
                    </div>
                    @endif

                    @if($campaign->current_advertising_campaign)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Current Advertising Campaign</h3>
                        <p class="text-gray-700 dark:text-gray-300">{!! $campaign->current_advertising_campaign !!}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Campaign Briefing Section -->
                @if($campaign->key_insights || $campaign->fan_motivator || $campaign->creative_connection)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Campaign Briefing</h2>

                    @if($campaign->key_insights)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Key Insights</h3>
                        <p class="text-gray-700 dark:text-gray-300">{!! $campaign->key_insights !!}</p>
                    </div>
                    @endif

                    @if($campaign->fan_motivator)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Fan Motivator</h3>
                        <p class="text-gray-700 dark:text-gray-300">{!! $campaign->fan_motivator !!}</p>
                    </div>
                    @endif

                    @if($campaign->creative_connection)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Creative Connection</h3>
                        <p class="text-gray-700 dark:text-gray-300">{!! $campaign->creative_connection !!}</p>
                    </div>
                    @endif

                    @if($campaign->specific_products)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Specific Products to Include</h3>
                        <p class="text-gray-700 dark:text-gray-300">{!! $campaign->specific_products !!}</p>
                    </div>
                    @endif

                    @if($campaign->posting_restrictions)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Posting Restrictions</h3>
                        <p class="text-gray-700 dark:text-gray-300">{!! $campaign->posting_restrictions !!}</p>
                    </div>
                    @endif

                    @if($campaign->additional_considerations)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Additional Considerations</h3>
                        <p class="text-gray-700 dark:text-gray-300">{!! $campaign->additional_considerations !!}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Deliverables & Success Metrics Section -->
                @if($campaign->target_platforms || $campaign->deliverables || $campaign->success_metrics)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Deliverables & Success Metrics</h2>

                    @if($campaign->target_platforms && is_array($campaign->target_platforms))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Target Platforms</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($campaign->target_platforms as $platform)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                {{ ucwords($platform) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($campaign->deliverables && is_array($campaign->deliverables))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Deliverables</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($campaign->deliverables as $deliverable)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                {{ ucwords(str_replace('_', ' ', $deliverable)) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($campaign->success_metrics && is_array($campaign->success_metrics))
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Success Metrics</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($campaign->success_metrics as $metric)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200">
                                {{ ucwords(str_replace('_', ' ', $metric)) }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($campaign->timing_details)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Timing Details</h3>
                        <p class="text-gray-700 dark:text-gray-300">{!! $campaign->timing_details !!}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Campaign Overview Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Campaign Overview</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Campaign Type</h3>
                                <div class="text-lg font-medium text-gray-900 dark:text-white">
                                    @if($campaign->campaign_type && count($campaign->campaign_type) > 0)
                                    @foreach($campaign->campaign_type as $type)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-2 mb-1">
                                        {{ $type->label() }}
                                    </span>
                                    @endforeach
                                    @else
                                    <span class="text-gray-400">No types selected</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Target Location</h3>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $campaign->target_zip_code ?? 'Anywhere' }}{{ $campaign->target_area ? ' - ' . $campaign->target_area : '' }}</p>
                            </div>
                            @if($campaign->main_contact)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Main Contact</h3>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $campaign->main_contact }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Application Deadline</h3>
                                <p class="text-lg font-medium {{ $campaign->application_deadline && $campaign->application_deadline->isPast() ? 'text-red-600' : 'text-gray-900 dark:text-white' }}">
                                    {{ $campaign->application_deadline ? $campaign->application_deadline->format('M j, Y') : 'No deadline' }}
                                </p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Campaign Start</h3>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $campaign->campaign_start_date ? $campaign->campaign_start_date->format('M j, Y') : 'Not specified' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Campaign End</h3>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $campaign->campaign_completion_date ? $campaign->campaign_completion_date->format('M j, Y') : 'Not specified' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    @switch($campaign->status->value)
                                        @case('draft') bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @break
                                        @case('published') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @break
                                        @case('scheduled') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 @break
                                        @case('in_progress') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200 @break
                                        @case('completed') bg-lime-100 text-lime-800 dark:bg-lime-900 dark:text-lime-200 @break
                                        @case('archived') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @break
                                        @default bg-gray-100 text-gray-800
                                    @endswitch
                                ">
                                    {{ ucwords(str_replace('_', ' ', $campaign->status->value)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compensation Section -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Compensation Overview</h2>

                        <div class="space-y-4">
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Compensation Type</h3>
                                <div class="text-lg font-medium text-gray-900 dark:text-white">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-2 mb-1">
                                        {{ $campaign->compensation_type->label() }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Comptensation Details</h3>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">{!! $campaign->compensation_description ?? 'Not specified' !!}</p>
                            </div>
                        </div>

                </div>

                <!-- Additional Requirements Section -->
                @if($campaign->additional_requirements)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Additional Requirements</h2>
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {!! $campaign->additional_requirements !!}
                    </div>
                </div>
                @endif

                <!-- Content Guidelines Section -->
                @if($campaign->content_guidelines || $campaign->brand_guidelines)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Content Guidelines</h2>

                    @if($campaign->content_guidelines)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Content Guidelines</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $campaign->content_guidelines }}</p>
                    </div>
                    @endif

                    @if($campaign->brand_guidelines)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Brand Guidelines</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $campaign->brand_guidelines }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Applications Section (for campaign owners) -->
                @if($isOwner)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Applications</h2>
                        <div class="flex items-center gap-2 text-xs">
                            @if($this->getPendingApplicationsCount() > 0)
                            <span class="px-1.5 py-0.5 rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">{{ $this->getPendingApplicationsCount() }} pending</span>
                            @endif
                            <span class="text-gray-500 dark:text-gray-400">{{ $this->getApplicationsCount() }} total</span>
                        </div>
                    </div>

                    @if($this->getApplicationsCount() > 0)
                    <div class="space-y-1">
                        @foreach($this->getSortedApplications(5) as $application)
                        <div class="flex items-center justify-between py-1.5 px-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 -mx-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-white text-[10px] font-medium shrink-0">{{ $application->user->initials() }}</div>
                                <span class="text-sm text-gray-900 dark:text-white truncate">{{ $application->user->name }}</span>
                                <span class="text-xs text-gray-400">{{ $application->submitted_at->diffForHumans(short: true) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium
                                    @switch($application->status->value)
                                        @case('accepted') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @break
                                        @case('pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300 @break
                                        @case('contracted') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 @break
                                        @case('rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300
                                    @endswitch
                                ">{{ $application->status->label() }}</span>
                                <button wire:click="openChatWithUser({{ $application->user->id }})" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('campaigns.applications', $campaign) }}" wire:navigate class="text-xs text-blue-600 hover:text-blue-500 dark:text-blue-400">
                            {{ $this->getApplicationsCount() > 5 ? 'View all ' . $this->getApplicationsCount() . ' applications' : 'Manage applications' }} →
                        </a>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">No applications yet</p>
                    </div>
                    @endif
                </div>
                @endif

                <!-- Collaborations Section (for in-progress or completed campaigns) -->
                @if($isOwner && ($campaign->status === \App\Enums\CampaignStatus::IN_PROGRESS || $campaign->status === \App\Enums\CampaignStatus::COMPLETED))
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Collaborations</h2>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $this->getCollaborations()->count() }} collaborators</span>
                    </div>

                    @if($this->getCollaborations()->count() > 0)
                    <div class="space-y-1">
                        @foreach($this->getCollaborations() as $collaboration)
                        <div class="flex items-center justify-between py-1.5 px-2 rounded hover:bg-gray-50 dark:hover:bg-gray-700/50 -mx-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <div class="w-6 h-6 bg-purple-600 rounded-full flex items-center justify-center text-white text-[10px] font-medium shrink-0">{{ $collaboration->influencer->initials() }}</div>
                                <span class="text-sm text-gray-900 dark:text-white truncate">{{ $collaboration->influencer->name }}</span>
                                <span class="text-xs text-gray-400">{{ $collaboration->started_at->diffForHumans(short: true) }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium
                                    @switch($collaboration->status->value)
                                        @case('active') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 @break
                                        @case('completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300 @break
                                        @case('cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 @break
                                    @endswitch
                                ">{{ $collaboration->status->label() }}</span>
                                @if($collaboration->deliverables_submitted_at)
                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">Delivered</span>
                                @endif
                                @if($collaboration->isActive())
                                <flux:modal.trigger :name="'complete-collaboration-'.$collaboration->id">
                                    <button class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded text-white bg-green-600 hover:bg-green-700">
                                        <svg class="w-3 h-3 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Complete
                                    </button>
                                </flux:modal.trigger>
                                <flux:modal :name="'complete-collaboration-'.$collaboration->id" class="min-w-[22rem]">
                                    <div class="space-y-6">
                                        <div>
                                            <flux:heading size="lg">Complete collaboration?</flux:heading>
                                            <flux:text class="mt-2">
                                                You're about to mark the collaboration with <strong>{{ $collaboration->influencer->name }}</strong> as complete.<br><br>
                                                This will start a 15-day review period where both parties can leave reviews.
                                            </flux:text>
                                        </div>
                                        <div class="flex gap-2">
                                            <flux:spacer />
                                            <flux:modal.close>
                                                <flux:button variant="ghost">Cancel</flux:button>
                                            </flux:modal.close>
                                            <flux:button wire:click="completeCollaboration({{ $collaboration->id }})" variant="primary">Complete</flux:button>
                                        </div>
                                    </div>
                                </flux:modal>
                                @endif
                                @if($collaboration->isCompleted() && $collaboration->review_status)
                                    @if($collaboration->canSubmitReview())
                                    <a href="{{ route('collaborations.review', $collaboration) }}" wire:navigate class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        Review{{ $collaboration->daysRemainingForReview() !== null ? ' ('.$collaboration->daysRemainingForReview().'d)' : '' }}
                                    </a>
                                    @elseif($collaboration->areReviewsVisible())
                                    <a href="{{ route('collaborations.reviews', $collaboration) }}" wire:navigate class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-medium rounded text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600">
                                        <svg class="w-3 h-3 mr-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        View
                                    </a>
                                    @elseif($collaboration->review_status->value === 'open')
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">Awaiting</span>
                                    @endif
                                @endif
                                <button wire:click="openChatWithUser({{ $collaboration->influencer->id }})" class="text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">No collaborations yet</p>
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
                            {{ substr($campaign->business->name ?? $campaign->user->name, 0, 2) }}
                        </div>
                        <div class="ml-4">
                            <p class="font-medium text-gray-900 dark:text-white">{{ $campaign->business->name ?? $campaign->user->name }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $campaign->business->industry?->label() ?? 'Business' }}</p>
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
                        {{-- Edit is only available for draft/scheduled campaigns --}}
                        @if($campaign->status->isEditable())
                        <a href="{{ route('campaigns.edit', $campaign) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 text-center block">
                            Edit Campaign
                        </a>
                        @endif

                        {{-- Start Campaign - only for published campaigns with accepted applications --}}
                        @if($campaign->status === \App\Enums\CampaignStatus::PUBLISHED)
                            @if($this->getAcceptedApplicationsCount() > 0)
                            <button wire:click="startCampaign" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                                Start Campaign ({{ $this->getAcceptedApplicationsCount() }} accepted)
                            </button>
                            @else
                            <div class="w-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-medium py-3 px-4 rounded-lg text-center text-sm">
                                Accept applications to start
                            </div>
                            @endif
                        <button wire:click="unpublishCampaign" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                            Unpublish Campaign
                        </button>
                        @endif

                        {{-- Complete Campaign - only for in-progress campaigns --}}
                        @if($campaign->status === \App\Enums\CampaignStatus::IN_PROGRESS)
                        <button wire:click="completeCampaign" class="w-full bg-lime-600 hover:bg-lime-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                            Complete Campaign
                        </button>
                        @endif

                        {{-- Publish - for draft campaigns --}}
                        @if($campaign->status === \App\Enums\CampaignStatus::DRAFT)
                        <button wire:click="publishCampaign" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                            Publish Campaign Now
                        </button>
                        @endif

                        {{-- Archive - for published or in_progress campaigns --}}
                        @if($campaign->status === \App\Enums\CampaignStatus::PUBLISHED || $campaign->status === \App\Enums\CampaignStatus::IN_PROGRESS)
                        <button wire:click="archiveCampaign" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                            Archive Campaign
                        </button>
                        @endif

                        <a href="{{ route('dashboard') }}" class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-3 px-4 rounded-lg transition-colors duration-200 text-center block">
                            Back to Dashboard
                        </a>
                        @elseif(auth()->check() && auth()->user()->isInfluencerAccount())
                        <button wire:click="applyToCampaign" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                            Apply to Campaign
                        </button>
                        <a href="{{ route('discover') }}" class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-3 px-4 rounded-lg transition-colors duration-200 text-center block">
                            Back to Discover
                        </a>
                        @else
                        <a href="{{ route('campaigns.index') }}" class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium py-3 px-4 rounded-lg transition-colors duration-200 text-center block">
                            Campaigns
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
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

        <!-- Campaign Banner -->
        <div class="mb-8">
            <div class="relative h-64 bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 rounded-lg overflow-hidden">
                <div class="absolute inset-0 bg-black bg-opacity-10"></div>
                <div class="relative h-full flex items-center justify-center">
                    <div class="text-center text-white">
                        <div class="mb-4">
                            <svg class="w-16 h-16 mx-auto opacity-75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold mb-2">{{ $campaign->project_name ?: 'Campaign Opportunity' }}</h2>
                        <p class="text-lg opacity-90">
                            @if(is_array($campaign->campaign_type) && count($campaign->campaign_type) > 0)
                                {{ collect($campaign->campaign_type)->map(fn($type) => \App\Enums\CampaignType::from($type)->label())->join(', ') }}
                            @else
                                Multiple Campaign Types
                            @endif
                        </p>
                    </div>
                </div>
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
                        <h1 class="text-4xl font-bold mb-4">{{ $campaign->project_name }}</h1>
                        @if($campaign->campaign_objective)
                            <p class="text-xl opacity-90 mb-6">{{ $campaign->campaign_objective }}</p>
                        @endif

                        <!-- Quick Stats -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold">{{ $campaign->influencer_count }}</div>
                                <div class="text-sm opacity-75">Influencers</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold">
                                    @if(is_array($campaign->campaign_type) && count($campaign->campaign_type) > 0)
                                        {{ count($campaign->campaign_type) }} Type{{ count($campaign->campaign_type) > 1 ? 's' : '' }}
                                    @else
                                        Multiple
                                    @endif
                                </div>
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
                                <p class="text-gray-700 dark:text-gray-300">{{ $campaign->brand_overview }}</p>
                            </div>
                        @endif

                        @if($campaign->brand_essence)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Brand Essence</h3>
                                <p class="text-gray-700 dark:text-gray-300 font-medium">{{ $campaign->brand_essence }}</p>
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
                                <p class="text-gray-700 dark:text-gray-300">{{ $campaign->brand_story }}</p>
                            </div>
                        @endif

                        @if($campaign->current_advertising_campaign)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Current Advertising Campaign</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ $campaign->current_advertising_campaign }}</p>
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
                                <p class="text-gray-700 dark:text-gray-300">{{ $campaign->key_insights }}</p>
                            </div>
                        @endif

                        @if($campaign->fan_motivator)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Fan Motivator</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ $campaign->fan_motivator }}</p>
                            </div>
                        @endif

                        @if($campaign->creative_connection)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Creative Connection</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ $campaign->creative_connection }}</p>
                            </div>
                        @endif

                        @if($campaign->specific_products)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Specific Products to Include</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ $campaign->specific_products }}</p>
                            </div>
                        @endif

                        @if($campaign->posting_restrictions)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Posting Restrictions</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ $campaign->posting_restrictions }}</p>
                            </div>
                        @endif

                        @if($campaign->additional_considerations)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Additional Considerations</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ $campaign->additional_considerations }}</p>
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
                                <p class="text-gray-700 dark:text-gray-300">{{ $campaign->timing_details }}</p>
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
                                    @if(is_array($campaign->campaign_type) && count($campaign->campaign_type) > 0)
                                        @foreach($campaign->campaign_type as $type)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-2 mb-1">
                                                {{ \App\Enums\CampaignType::from($type)->label() }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-gray-400">No types selected</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Compensation</h3>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $campaign->getCompensationDisplayAttribute() }}</p>
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
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Campaign Completion</h3>
                                <p class="text-lg font-medium text-gray-900 dark:text-white">{{ $campaign->campaign_completion_date ? $campaign->campaign_completion_date->format('M j, Y') : 'Not specified' }}</p>
                            </div>
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Status</h3>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    {{ ucwords(str_replace('_', ' ', $campaign->status->value)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Requirements Section -->
                @if($campaign->additional_requirements)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Additional Requirements</h2>
                        <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                            @if(is_array($campaign->additional_requirements))
                                <div class="space-y-3">
                                    @foreach($campaign->additional_requirements as $key => $value)
                                        <div class="flex items-start space-x-3">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></div>
                                            <div>
                                                <span class="font-medium text-gray-900 dark:text-white">{{ ucwords(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="text-gray-600 dark:text-gray-400 ml-2">{{ ucwords(str_replace('_', ' ', $value)) }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                {!! nl2br(e($campaign->additional_requirements)) !!}
                            @endif
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
                                @foreach($campaign->applications()->with(['user.influencer'])->latest('submitted_at')->take(3)->get() as $application)
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
                                            <div class="flex items-center space-x-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $application->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' : ($application->status === 'accepted' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300') }}">
                                                    {{ $application->status->label() }}
                                                </span>
                                                <!-- Chat Button -->
                                                <button wire:click="openChatWithUser({{ $application->user->id }})" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                                    <svg fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 0 1-.825-.242m9.345-8.334a2.126 2.126 0 0 0-.476-.095 48.64 48.64 0 0 0-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0 0 11.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2">{{ Str::limit($application->message, 100) }}</p>

                                    </div>
                                @endforeach
                                <div class="mt-3">
                                            <a href="{{ route('campaigns.applications', $campaign) }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                                View all applications →
                                            </a>
                                        </div>
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
                            <a href="{{ route('campaigns.edit', $campaign) }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200 text-center block">
                                Edit Campaign
                            </a>
                            @if($campaign->status === \App\Enums\CampaignStatus::PUBLISHED)
                                <button wire:click="unpublishCampaign" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                                    Unpublish Campaign
                                </button>
                            @endif
                            @if($campaign->status === \App\Enums\CampaignStatus::ARCHIVED || $campaign->status === \App\Enums\CampaignStatus::DRAFT)
                                <button wire:click="publishCampaign" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-4 rounded-lg transition-colors duration-200">
                                    Publish Campaign Now
                                </button>
                            @elseif($campaign->status === \App\Enums\CampaignStatus::PUBLISHED)
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
    </div>
</div>
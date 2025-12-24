{{-- Step 4: Review & Publish --}}
<div class="space-y-8">
    <flux:heading size="xl">Review & Publish</flux:heading>

    {{-- Subscription Limit Info --}}
    <livewire:components.subscription-limit-info
        wire:key="limit-info-{{ $publishAction }}"
        limit-key="{{ \App\Subscription\SubscriptionMetadataSchema::CAMPAIGNS_PUBLISHED_LIMIT }}"
        action-text="Publishing this campaign"
        credit-name="campaign publish credit"
        :is-scheduled="$publishAction === 'schedule'"
    />

    {{-- Campaign Overview --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Left Column: Campaign Basics --}}
        <div class="space-y-4 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-blue-100 dark:bg-blue-500/20 rounded-lg">
                    <flux:icon.document-text class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="font-medium text-zinc-700 dark:text-zinc-300">Campaign Basics</span>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">Project Name</span>
                    <span class="text-zinc-900 dark:text-white font-medium">{{ $projectName ?: 'Not specified' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">Target Location</span>
                    <span class="text-zinc-900 dark:text-white font-medium">{{ $targetZipCode }}</span>
                </div>
                <div>
                    <span class="text-zinc-500 dark:text-zinc-400 block mb-1">Campaign Goal</span>
                    <p class="text-zinc-900 dark:text-white">{{ $campaignGoal }}</p>
                </div>
                <div>
                    <span class="text-zinc-500 dark:text-zinc-400 block mb-1">Campaign Types</span>
                    <div class="flex flex-wrap gap-1">
                        @if(is_array($campaignType) && count($campaignType) > 0)
                            @foreach($campaignType as $type)
                                <flux:badge size="sm" variant="outline">
                                    {{ is_object($type) ? $type->label() : \App\Enums\CampaignType::from($type)->label() }}
                                </flux:badge>
                            @endforeach
                        @else
                            <span class="text-zinc-400">None selected</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Timeline & Compensation --}}
        <div class="space-y-4 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center gap-2 mb-2">
                <div class="p-1.5 bg-amber-100 dark:bg-amber-500/20 rounded-lg">
                    <flux:icon.calendar class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                </div>
                <span class="font-medium text-zinc-700 dark:text-zinc-300">Timeline & Compensation</span>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">Influencers Needed</span>
                    <span class="text-zinc-900 dark:text-white font-medium">{{ $influencerCount }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">Application Deadline</span>
                    <span class="text-zinc-900 dark:text-white font-medium">{{ $applicationDeadline ? \Carbon\Carbon::parse($applicationDeadline)->format('M j, Y') : 'Not set' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">Campaign Start</span>
                    <span class="text-zinc-900 dark:text-white font-medium">{{ $campaignStartDate ? \Carbon\Carbon::parse($campaignStartDate)->format('M j, Y') : 'Not set' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">Campaign End</span>
                    <span class="text-zinc-900 dark:text-white font-medium">{{ $campaignCompletionDate ? \Carbon\Carbon::parse($campaignCompletionDate)->format('M j, Y') : 'Not set' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">Compensation</span>
                    <flux:badge size="sm" color="amber">{{ \App\Enums\CompensationType::from($compensationType)->label() }}</flux:badge>
                </div>
            </div>
        </div>
    </div>

    {{-- Deliverables Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        {{-- Platforms --}}
        <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center gap-2 mb-3">
                <div class="p-1 bg-blue-100 dark:bg-blue-500/20 rounded">
                    <flux:icon.device-phone-mobile class="w-3 h-3 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Platforms</span>
            </div>
            <div class="flex flex-wrap gap-1">
                @if(count($targetPlatforms) > 0)
                    @php $platformOptions = collect($this->getTargetPlatformOptions())->keyBy('value'); @endphp
                    @foreach($targetPlatforms as $platform)
                        <flux:badge size="sm" variant="outline">{{ $platformOptions->get($platform)['label'] ?? $platform }}</flux:badge>
                    @endforeach
                @else
                    <span class="text-sm text-zinc-400">None selected</span>
                @endif
            </div>
        </div>

        {{-- Deliverables --}}
        <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center gap-2 mb-3">
                <div class="p-1 bg-green-100 dark:bg-green-500/20 rounded">
                    <flux:icon.photo class="w-3 h-3 text-green-600 dark:text-green-400" />
                </div>
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Deliverables</span>
            </div>
            <div class="flex flex-wrap gap-1">
                @if(count($deliverables) > 0)
                    @php $deliverableOptions = collect($this->getDeliverableTypeOptions())->keyBy('value'); @endphp
                    @foreach($deliverables as $deliverable)
                        <flux:badge size="sm" variant="outline">{{ $deliverableOptions->get($deliverable)['label'] ?? $deliverable }}</flux:badge>
                    @endforeach
                @else
                    <span class="text-sm text-zinc-400">None selected</span>
                @endif
            </div>
        </div>

        {{-- Metrics --}}
        <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center gap-2 mb-3">
                <div class="p-1 bg-purple-100 dark:bg-purple-500/20 rounded">
                    <flux:icon.chart-bar class="w-3 h-3 text-purple-600 dark:text-purple-400" />
                </div>
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Success Metrics</span>
            </div>
            <div class="flex flex-wrap gap-1">
                @if(count($successMetrics) > 0)
                    @php $metricOptions = collect($this->getSuccessMetricOptions())->keyBy('value'); @endphp
                    @foreach($successMetrics as $metric)
                        <flux:badge size="sm" variant="outline">{{ $metricOptions->get($metric)['label'] ?? $metric }}</flux:badge>
                    @endforeach
                @else
                    <span class="text-sm text-zinc-400">None selected</span>
                @endif
            </div>
        </div>
    </div>

    {{-- Briefing Details (Collapsible) --}}
    @if($campaignObjective || $keyInsights || $fanMotivator || $brandOverview)
        <flux:accordion class="border border-zinc-200 dark:border-zinc-700 rounded-lg">
            <flux:accordion.item>
                <flux:accordion.heading class="px-4 py-3 bg-zinc-50 dark:bg-zinc-800/50">
                    <div class="flex items-center gap-2">
                        <flux:icon.document-magnifying-glass class="w-4 h-4 text-zinc-500" />
                        <span class="font-medium text-zinc-700 dark:text-zinc-300">Campaign Briefing Details</span>
                    </div>
                </flux:accordion.heading>
                <flux:accordion.content>
                    <div class="p-4 space-y-4">
                        @if($campaignObjective)
                            <div>
                                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Campaign Objective</span>
                                <div class="mt-1 text-zinc-900 dark:text-white prose prose-sm max-w-none">{!! $campaignObjective !!}</div>
                            </div>
                        @endif
                        @if($keyInsights)
                            <div>
                                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Key Insights</span>
                                <div class="mt-1 text-zinc-900 dark:text-white prose prose-sm max-w-none">{!! $keyInsights !!}</div>
                            </div>
                        @endif
                        @if($fanMotivator)
                            <div>
                                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Fan Motivator</span>
                                <div class="mt-1 text-zinc-900 dark:text-white prose prose-sm max-w-none">{!! $fanMotivator !!}</div>
                            </div>
                        @endif
                        @if($brandOverview)
                            <div>
                                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Brand Overview</span>
                                <div class="mt-1 text-zinc-900 dark:text-white prose prose-sm max-w-none">{!! $brandOverview !!}</div>
                            </div>
                        @endif
                        @if($compensationDescription)
                            <div>
                                <span class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Compensation Details</span>
                                <div class="mt-1 text-zinc-900 dark:text-white prose prose-sm max-w-none">{!! $compensationDescription !!}</div>
                            </div>
                        @endif
                    </div>
                </flux:accordion.content>
            </flux:accordion.item>
        </flux:accordion>
    @endif

    <flux:separator />

    {{-- Publish Options --}}
    <div>
        <div class="flex items-center gap-2 mb-4">
            <div class="p-1.5 bg-green-100 dark:bg-green-500/20 rounded-lg">
                <flux:icon.rocket-launch class="w-4 h-4 text-green-600 dark:text-green-400" />
            </div>
            <flux:label class="!mb-0">Publish Action</flux:label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <label class="flex items-center gap-3 p-4 rounded-lg cursor-pointer border-2 transition-colors
                {{ $publishAction === 'publish' ? 'border-green-500 bg-green-50 dark:bg-green-500/10' : 'border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                <input type="radio" wire:model.live="publishAction" value="publish" class="text-green-600" />
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <flux:icon.bolt class="w-4 h-4 {{ $publishAction === 'publish' ? 'text-green-600' : 'text-zinc-400' }}" />
                        <span class="font-medium text-zinc-900 dark:text-white">Publish Now</span>
                    </div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Make your campaign live immediately</p>
                </div>
            </label>

            <label class="flex items-center gap-3 p-4 rounded-lg cursor-pointer border-2 transition-colors
                {{ $publishAction === 'schedule' ? 'border-blue-500 bg-blue-50 dark:bg-blue-500/10' : 'border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-800' }}">
                <input type="radio" wire:model.live="publishAction" value="schedule" class="text-blue-600" />
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <flux:icon.clock class="w-4 h-4 {{ $publishAction === 'schedule' ? 'text-blue-600' : 'text-zinc-400' }}" />
                        <span class="font-medium text-zinc-900 dark:text-white">Schedule for Later</span>
                    </div>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Set a specific date to publish</p>
                </div>
            </label>
        </div>

        @if($publishAction === 'schedule')
            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-500/10 rounded-lg border border-blue-200 dark:border-blue-500/30">
                <flux:field>
                    <flux:label>Schedule Date</flux:label>
                    <flux:input type="date" wire:model="scheduledDate" min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                    <flux:error name="scheduledDate" />
                </flux:field>
            </div>
        @endif
    </div>
</div>

<flux:modal wire:model.self="showModal" name="invite-influencer-modal" class="max-w-4xl">
    <div class="flex flex-col h-full min-h-0">
        <div class="flex-1 space-y-8">
        <!-- Clean Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                    <flux:icon.user-plus class="w-6 h-6 text-white" />
                </div>
                <div>
                    <flux:heading size="xl" class="text-gray-900 dark:text-white font-semibold">
                        Invite {{ $influencerName }}
                    </flux:heading>
                    <flux:text variant="muted" size="sm">
                        Send a collaboration invitation
                    </flux:text>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pb-5">
            <!-- Left Column - Form Fields -->
            <div class="space-y-6">
                <!-- Campaign Selection -->
                <flux:field>
                    <flux:label class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Campaign
                    </flux:label>
                    <flux:select wire:model.live="selectedCampaign" placeholder="Choose a campaign">
                        @forelse($this->campaigns as $campaign)
                            <option value="{{ $campaign->id }}">{{ $campaign->campaign_goal }}</option>
                        @empty
                            <option value="">No campaigns available</option>
                        @endforelse
                    </flux:select>
                    <flux:text variant="muted" size="sm" class="mt-1">
                        Select one of your active campaigns
                    </flux:text>
                </flux:field>

                <!-- Personal Message -->
                <flux:field>
                    <flux:label class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Personal Message
                    </flux:label>
                    <flux:textarea
                        wire:model="message"
                        placeholder="Hi {{ $influencerName }}! I love your content and think you'd be perfect for this campaign. Your authentic style and engaged audience align perfectly with what we're looking for..."
                        rows="8"
                        class="resize-none"
                    ></flux:textarea>
                    <flux:text variant="muted" size="sm" class="mt-1">
                        Personalized messages have much higher response rates
                    </flux:text>
                </flux:field>
            </div>

            <!-- Right Column - Campaign Preview -->
            <div class="lg:pl-8 lg:border-l lg:border-gray-200 lg:dark:border-gray-700">
                @if($this->selectedCampaignData)
                    <div class="space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <flux:icon.eye class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                            </div>
                            <flux:heading size="lg" class="text-gray-900 dark:text-white">
                                Campaign Details
                            </flux:heading>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                            <!-- Campaign Goal -->
                            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                                <div class="flex items-start gap-4">
                                    <div class="w-10 h-10 bg-blue-50 dark:bg-blue-900/50 rounded-lg flex items-center justify-center shrink-0">
                                        <flux:icon.flag class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <flux:text variant="muted" size="sm" class="uppercase tracking-wide font-semibold mb-1">
                                            Campaign Goal
                                        </flux:text>
                                        <flux:text class="font-semibold text-gray-900 dark:text-white text-lg">
                                            {{ $this->selectedCampaignData->campaign_goal }}
                                        </flux:text>
                                    </div>
                                </div>
                            </div>

                            <!-- Campaign Info Grid -->
                            <div class="p-6 space-y-6">
                                <!-- Compensation -->
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-900/50 rounded-lg flex items-center justify-center shrink-0">
                                        <flux:icon.currency-dollar class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                                    </div>
                                    <div>
                                        <flux:text variant="muted" size="sm" class="uppercase tracking-wide font-semibold">
                                            Compensation
                                        </flux:text>
                                        <flux:text class="font-medium text-gray-900 dark:text-white">
                                            {{ ucfirst(str_replace('_', ' ', $this->selectedCampaignData->compensation_type->value)) }}
                                        </flux:text>
                                    </div>
                                </div>

                                @if($this->selectedCampaignData->target_platforms)
                                    <!-- Platforms -->
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-purple-50 dark:bg-purple-900/50 rounded-lg flex items-center justify-center shrink-0">
                                            <flux:icon.device-phone-mobile class="w-5 h-5 text-purple-600 dark:text-purple-400" />
                                        </div>
                                        <div>
                                            <flux:text variant="muted" size="sm" class="uppercase tracking-wide font-semibold">
                                                Target Platforms
                                            </flux:text>
                                            <div class="flex flex-wrap gap-2 mt-1">
                                                @foreach($this->selectedCampaignData->target_platforms as $platform)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-200">
                                                        {{ ucfirst($platform) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($this->selectedCampaignData->end_date)
                                    <!-- End Date -->
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/50 rounded-lg flex items-center justify-center shrink-0">
                                            <flux:icon.calendar class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                                        </div>
                                        <div>
                                            <flux:text variant="muted" size="sm" class="uppercase tracking-wide font-semibold">
                                                End Date
                                            </flux:text>
                                            <flux:text class="font-medium text-gray-900 dark:text-white">
                                                {{ $this->selectedCampaignData->end_date->format('F j, Y') }}
                                                <span class="text-sm text-gray-500 dark:text-gray-400 block">
                                                    {{ $this->selectedCampaignData->end_date->diffForHumans() }}
                                                </span>
                                            </flux:text>
                                        </div>
                                    </div>
                                @endif
                            </div>
                    </div>
                @else
                    <div class="flex items-center justify-center h-64">
                        <div class="text-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <flux:icon.document class="w-8 h-8 text-gray-400" />
                            </div>
                            <flux:text variant="muted" class="text-center">
                                Select a campaign to view details
                            </flux:text>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        </div>

        <!-- Footer Actions - Fixed at bottom -->
        <div class="flex-shrink-0 pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon.information-circle class="w-4 h-4 text-blue-500" />
                    <flux:text variant="muted" size="sm">
                        Personalized invites have 85% higher acceptance rates
                    </flux:text>
                </div>

                <div class="flex items-center gap-3">
                    <flux:button variant="ghost" wire:click="closeModal">
                        Cancel
                    </flux:button>

                    <flux:button icon="paper-airplane" variant="primary" wire:click="sendInvite" class="px-6">
                        Send Invitation
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</flux:modal>

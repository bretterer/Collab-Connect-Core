<flux:modal wire:model.self="showModal" name="invite-influencer-modal" class="w-full max-w-4xl mx-4 sm:mx-auto overflow-visible">
    <div class="flex flex-col h-full overflow-visible">
        <div class="flex-1 space-y-6 sm:space-y-8 pt-2 overflow-visible">
        <!-- Clean Header -->
        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 sm:pb-6">
            <div class="flex items-center gap-3 sm:gap-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                    <flux:icon.user-plus class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                </div>
                <div class="min-w-0 flex-1">
                    <flux:heading size="xl" class="text-gray-900 dark:text-white font-semibold truncate">
                        Invite {{ $influencerName }}
                    </flux:heading>
                    <flux:text variant="muted" size="sm" class="hidden sm:block">
                        Send a collaboration invitation
                    </flux:text>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 pb-5">
            <!-- Left Column - Form Fields -->
            <div class="space-y-5 sm:space-y-6">
                <!-- Campaign Selection -->
                <flux:field class="relative">
                    <flux:label class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                        Campaign
                    </flux:label>
                    <flux:select wire:model.live="selectedCampaign" placeholder="Choose a campaign">
                        @forelse($this->campaigns as $campaign)
                            <flux:select.option value="{{ $campaign->id }}">{{ $campaign->project_name }}</flux:select.option>
                        @empty
                            <flux:select.option value="">No campaigns available</flux:select.option>
                        @endforelse
                    </flux:select>
                    <flux:text variant="muted" size="sm" class="mt-1.5 block">
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
                        rows="6"
                        class="resize-none text-sm sm:text-base"
                    ></flux:textarea>
                    <flux:text variant="muted" size="sm" class="mt-1.5 block">
                        Personalized messages have much higher response rates
                    </flux:text>
                </flux:field>
            </div>

            <!-- Right Column - Campaign Preview -->
            <div class="lg:pl-8 lg:border-l lg:border-gray-200 lg:dark:border-gray-700 mt-2 lg:mt-0">
                @if($this->selectedCampaignData)
                    <div class="space-y-4 sm:space-y-6">
                        <div class="flex items-center gap-3">
                            <div class="w-7 h-7 sm:w-8 sm:h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center flex-shrink-0">
                                <flux:icon.eye class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-blue-600 dark:text-blue-400" />
                            </div>
                            <flux:heading size="lg" class="text-gray-900 dark:text-white text-base sm:text-lg">
                                Campaign Details
                            </flux:heading>
                        </div>

                        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
                            <!-- Campaign Goal -->
                            <div class="p-4 sm:p-6 border-b border-gray-100 dark:border-gray-700">
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-blue-50 dark:bg-blue-900/50 rounded-lg flex items-center justify-center shrink-0">
                                        <flux:icon.flag class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <flux:text variant="muted" size="sm" class="uppercase tracking-wide font-semibold mb-1 text-xs sm:text-sm">
                                            Campaign Goal
                                        </flux:text>
                                        <flux:text class="font-semibold text-gray-900 dark:text-white text-base sm:text-lg break-words">
                                            {{ $this->selectedCampaignData->campaign_goal }}
                                        </flux:text>
                                    </div>
                                </div>
                            </div>

                            <!-- Campaign Info Grid -->
                            <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                                <!-- Compensation -->
                                <div class="flex items-start gap-3 sm:gap-4">
                                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-emerald-50 dark:bg-emerald-900/50 rounded-lg flex items-center justify-center shrink-0">
                                        <flux:icon.currency-dollar class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600 dark:text-emerald-400" />
                                    </div>
                                    <div class="min-w-0">
                                        <flux:text variant="muted" size="sm" class="uppercase tracking-wide font-semibold text-xs sm:text-sm">
                                            Compensation
                                        </flux:text>
                                        <flux:text class="font-medium text-gray-900 dark:text-white text-sm sm:text-base break-words">
                                            {{ ucfirst(str_replace('_', ' ', $this->selectedCampaignData->compensation_type->value)) }}
                                        </flux:text>
                                    </div>
                                </div>

                                @if($this->selectedCampaignData->target_platforms)
                                    <!-- Platforms -->
                                    <div class="flex items-start gap-3 sm:gap-4">
                                        <div class="w-9 h-9 sm:w-10 sm:h-10 bg-purple-50 dark:bg-purple-900/50 rounded-lg flex items-center justify-center shrink-0">
                                            <flux:icon.device-phone-mobile class="w-4 h-4 sm:w-5 sm:h-5 text-purple-600 dark:text-purple-400" />
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <flux:text variant="muted" size="sm" class="uppercase tracking-wide font-semibold text-xs sm:text-sm">
                                                Target Platforms
                                            </flux:text>
                                            <div class="flex flex-wrap gap-1.5 sm:gap-2 mt-1.5">
                                                @foreach($this->selectedCampaignData->target_platforms as $platform)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/50 text-purple-800 dark:text-purple-200">
                                                        {{ ucfirst($platform) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if($this->selectedCampaignData->end_date)
                                    <!-- End Date -->
                                    <div class="flex items-start gap-3 sm:gap-4">
                                        <div class="w-9 h-9 sm:w-10 sm:h-10 bg-orange-50 dark:bg-orange-900/50 rounded-lg flex items-center justify-center shrink-0">
                                            <flux:icon.calendar class="w-4 h-4 sm:w-5 sm:h-5 text-orange-600 dark:text-orange-400" />
                                        </div>
                                        <div class="min-w-0">
                                            <flux:text variant="muted" size="sm" class="uppercase tracking-wide font-semibold text-xs sm:text-sm">
                                                End Date
                                            </flux:text>
                                            <flux:text class="font-medium text-gray-900 dark:text-white text-sm sm:text-base">
                                                {{ $this->selectedCampaignData->end_date->format('M j, Y') }}
                                                <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 block mt-0.5">
                                                    {{ $this->selectedCampaignData->end_date->diffForHumans() }}
                                                </span>
                                            </flux:text>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-center py-12 sm:py-16 lg:h-64">
                        <div class="text-center px-4">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center mx-auto mb-3 sm:mb-4">
                                <flux:icon.document class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400 dark:text-gray-500" />
                            </div>
                            <flux:text variant="muted" class="text-center text-sm sm:text-base">
                                Select a campaign to view details
                            </flux:text>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        </div>

        <!-- Footer Actions - Fixed at bottom -->
        <div class="flex-shrink-0 pt-4 sm:pt-6 border-t border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">
                <div class="flex items-center gap-2 order-2 sm:order-1">
                    <flux:icon.information-circle class="w-4 h-4 text-blue-500 flex-shrink-0" />
                    <flux:text variant="muted" size="sm" class="text-xs sm:text-sm">
                        Personalized invites have 85% higher acceptance rates
                    </flux:text>
                </div>

                <div class="flex items-center gap-2 sm:gap-3 order-1 sm:order-2">
                    <flux:button variant="ghost" wire:click="closeModal" class="flex-1 sm:flex-none">
                        Cancel
                    </flux:button>

                    <flux:button icon="paper-airplane" variant="primary" wire:click="sendInvite" class="flex-1 sm:flex-none sm:px-6">
                        Send Invitation
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
</flux:modal>

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
    @else
    <!-- Auto-save indicator -->
    @if($hasUnsavedChanges)
        <div class="fixed bottom-4 right-4 z-50">
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg px-4 py-2 shadow-lg">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span class="text-sm text-yellow-800 dark:text-yellow-200">Saving...</span>
                </div>
            </div>
        </div>
    @elseif($lastSavedAt)
        <div class="fixed bottom-4 right-4 z-50"
             x-data="{ show: true }"
             x-init="setTimeout(() => show = false, 2500)"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 transform translate-y-2 scale-95">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg px-4 py-2 shadow-lg">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-green-800 dark:text-green-200">Saved</span>
                </div>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6 text-white">
                    <h1 class="text-3xl font-bold mb-2">
                        {{ $this->isEditing() ? 'Edit Campaign' : 'Create a New Campaign' }} 🚀
                    </h1>
                    <p class="text-blue-100 text-lg">
                        Step {{ $currentStep }} of {{ $this->getTotalSteps() }}: {{ $this->getWizardSteps()[$currentStep] }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="mb-8">
            <!-- Step Indicators -->
            <div class="flex items-center justify-between mb-4">
                                @foreach($this->getWizardSteps() as $stepNumber => $stepName)
                    <div class="flex items-center flex-1">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-200 {{ $stepNumber < $currentStep ? 'bg-green-500 border-green-500 text-white' : ($stepNumber === $currentStep ? 'bg-blue-600 border-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500') }} {{ $stepNumber <= $currentStep ? 'cursor-pointer hover:scale-110' : 'cursor-default' }}"
                             @if($stepNumber <= $currentStep) wire:click="goToStep({{ $stepNumber }})" @endif>
                            @if($stepNumber < $currentStep)
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <span class="text-sm font-semibold">{{ $stepNumber }}</span>
                            @endif
                        </div>

                        <!-- Step Label -->
                        <div class="ml-3 flex-1">
                            <div class="text-sm font-medium {{ $stepNumber <= $currentStep ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400' }} {{ $stepNumber <= $currentStep ? 'cursor-pointer' : 'cursor-default' }}"
                                 @if($stepNumber <= $currentStep) wire:click="goToStep({{ $stepNumber }})" @endif>
                                {{ $stepName }}
                            </div>
                        </div>

                        <!-- Connector Line -->
                        @if($stepNumber < $this->getTotalSteps())
                            <div class="flex-1 h-0.5 mx-4 {{ $stepNumber < $currentStep ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-600' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500 ease-out"
                     style="width: {{ ($currentStep / $this->getTotalSteps()) * 100 }}%"></div>
            </div>

            <!-- Progress Text -->
            <div class="mt-2 text-center">
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    Step {{ $currentStep }} of {{ $this->getTotalSteps() }} completed
                </span>
            </div>
        </div>

        <!-- Campaign Creation Form -->
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                @if($currentStep === 1)
                    <!-- Step 1: Campaign Goal & Type -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Campaign Goal & Type</h2>

                            <div class="space-y-4">
                                <!-- Project Name -->
                                <div>
                                    <flux:label>Project Name</flux:label>
                                    <flux:input
                                        type="text"
                                        wire:model="projectName"
                                        placeholder="e.g., Summer Coffee Campaign"
                                        class="w-full" />
                                    @error('projectName') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Campaign Goal Statement -->
                                <div>
                                    <flux:label>I'm a business and I want to...</flux:label>
                                    <flux:input
                                        type="text"
                                        wire:model="campaignGoal"
                                        placeholder="e.g., increase brand awareness, promote new product launch, drive foot traffic"
                                        class="w-full"
                                        required />
                                    @error('campaignGoal') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Campaign Type Selection -->
                                <div>
                                    <flux:label>Campaign Type (Select all that apply)</flux:label>
                                    <div class="mt-3 space-y-2">
                                        @foreach($this->getCampaignTypeOptions() as $type)
                                            <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ collect($campaignType ?? [])->contains(function($item) use ($type) { return is_object($item) ? $item->value === $type['value'] : $item === $type['value']; }) ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                                <input
                                                    type="checkbox"
                                                    wire:model="campaignType"
                                                    value="{{ $type['value'] }}"
                                                    class="text-blue-600 rounded" />
                                                <div class="flex-1">
                                                    <span class="text-gray-900 dark:text-white font-medium">{{ $type['label'] }}</span>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        @if($type['value'] == 'sponsored_posts')
                                                            Sponsored social media posts featuring your brand
                                                        @elseif($type['value'] == 'product_reviews')
                                                            Product or service reviews and testimonials
                                                        @elseif($type['value'] == 'event_coverage')
                                                            Live event coverage and real-time posting
                                                        @elseif($type['value'] == 'giveaways')
                                                            Contests and giveaway campaigns
                                                        @elseif($type['value'] == 'brand_partnerships')
                                                            Long-term brand partnership collaborations
                                                        @elseif($type['value'] == 'seasonal_content')
                                                            Seasonal and holiday themed content
                                                        @elseif($type['value'] == 'behind_scenes')
                                                            Behind-the-scenes content creation
                                                        @elseif($type['value'] == 'user_generated')
                                                            User-generated content campaigns
                                                        @else
                                                            Custom collaboration with flexible requirements
                                                        @endif
                                                    </p>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('campaignType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Target Location -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <flux:input
                                        type="text"
                                        wire:model="targetZipCode"
                                        label="Target Zip Code"
                                        placeholder="45066"
                                        required />
                                    @error('targetZipCode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 2)
                    <!-- Step 2: Brand Information -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Brand Information</h2>

                            <div class="space-y-4">
                                <!-- Brand Overview -->
                                <div>
                                    <flux:label>Brand Overview</flux:label>
                                    <flux:editor
                                        wire:model="brandOverview"
                                        placeholder="Provide a brief overview of your brand, including what you do, your mission, and key differentiators..."
                                        required />
                                    @error('brandOverview') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>



                                <!-- Current Advertising Campaign -->
                                <div>
                                    <flux:label>Current Advertising Campaign (Optional)</flux:label>
                                    <textarea
                                        wire:model="currentAdvertisingCampaign"
                                        rows="3"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="Describe any current advertising campaigns or marketing initiatives..."></textarea>
                                </div>

                                <!-- Brand Story -->
                                <div>
                                    <flux:label>Brand Story (Optional)</flux:label>
                                    <flux:editor
                                        wire:model="brandStory"
                                        placeholder="Share your brand's story, history, or founding principles..." />
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 3)
                    <!-- Step 3: Campaign Briefing -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Campaign Briefing</h2>

                            <div class="space-y-4">
                                <!-- Campaign Objective -->
                                <div>
                                    <flux:label>Campaign Objective</flux:label>
                                    <flux:editor
                                        wire:model="campaignObjective"
                                        placeholder="What is the main objective for creators? e.g., Show how {company} makes any moment/day/routine better"
                                        required />
                                    @error('campaignObjective') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Key Insights -->
                                <div>
                                    <flux:label>Key Insights</flux:label>
                                    <flux:editor
                                        wire:model="keyInsights"
                                        placeholder="What are the key insights about your target audience or market? e.g., People are feeling more isolated and looking for connection..."
                                        required />
                                    @error('keyInsights') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Fan Motivator -->
                                <div>
                                    <flux:label>Fan Motivator</flux:label>
                                    <flux:editor
                                        wire:model="fanMotivator"
                                        placeholder="What motivates your fans/customers? e.g., Every time I go to {company} I know I'm going to leave feeling better..."
                                        required />
                                    @error('fanMotivator') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Creative Connection -->
                                <div>
                                    <flux:label>Creative Connection</flux:label>
                                    <flux:editor
                                        wire:model="creativeConnection"
                                        placeholder="How should creators connect with your brand? Provide examples and creative direction..."
                                        required />
                                    @error('creativeConnection') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Specific Products -->
                                <div>
                                    <flux:label>Specific Products to Include (Optional)</flux:label>
                                    <flux:editor
                                        wire:model="specificProducts"
                                        placeholder="List specific products or services you want influencers to feature..." />
                                </div>

                                <!-- Posting Restrictions -->
                                <div>
                                    <flux:label>Posting Restrictions (Optional)</flux:label>
                                    <flux:editor
                                        wire:model="postingRestrictions"
                                        placeholder="e.g., Nothing political, polarizing or divisive, no offensive language..." />
                                </div>

                                <!-- Additional Considerations -->
                                <div>
                                    <flux:label>Additional Considerations (Optional)</flux:label>
                                    <flux:editor
                                        wire:model="additionalConsiderations"
                                        placeholder="Any additional creative considerations, tone guidelines, or special requirements..." />
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 4)
                    <!-- Step 4: Deliverables & Metrics -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Deliverables & Success Metrics</h2>

                            <div class="space-y-6">
                                <!-- Target Platforms -->
                                <div>
                                    <flux:label>Target Platforms (Select all that apply)</flux:label>
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($this->getTargetPlatformOptions() as $platform)
                                            <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ in_array($platform['value'], $targetPlatforms) ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                                <input
                                                    type="checkbox"
                                                    wire:model="targetPlatforms"
                                                    value="{{ $platform['value'] }}"
                                                    class="text-blue-600" />
                                                <span class="text-gray-900 dark:text-white">{{ $platform['label'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('targetPlatforms') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Deliverables -->
                                <div>
                                    <flux:label>Deliverables (Select all that apply)</flux:label>
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($this->getDeliverableTypeOptions() as $deliverable)
                                            <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ in_array($deliverable['value'], $deliverables) ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                                <input
                                                    type="checkbox"
                                                    wire:model="deliverables"
                                                    value="{{ $deliverable['value'] }}"
                                                    class="text-green-600" />
                                                <div class="flex-1">
                                                    <span class="text-gray-900 dark:text-white">{{ $deliverable['label'] }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('deliverables') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Success Metrics -->
                                <div>
                                    <flux:label>Success Metrics (Select all that apply)</flux:label>
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($this->getSuccessMetricOptions() as $metric)
                                            <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ in_array($metric['value'], $successMetrics) ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                                <input
                                                    type="checkbox"
                                                    wire:model="successMetrics"
                                                    value="{{ $metric['value'] }}"
                                                    class="text-purple-600" />
                                                <div class="flex-1">
                                                    <span class="text-gray-900 dark:text-white">{{ $metric['label'] }}</span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('successMetrics') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Timing Details -->
                                <div>
                                    <flux:label>Timing Details (Optional)</flux:label>
                                    <flux:editor
                                        wire:model="timingDetails"
                                        placeholder="Campaign timeline, posting schedule, or specific timing requirements..." />
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 5)
                    <!-- Step 5: Campaign Settings -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Campaign Settings</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Compensation Type -->
                                <div class="space-y-4">
                                    <div>
                                        <flux:label>Compensation Type</flux:label>
                                        <div class="mt-3 space-y-2">
                                            @foreach($this->getCompensationTypeOptions() as $option)
                                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ $compensationType === $option['value'] ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                                    <input
                                                        type="radio"
                                                        wire:model="compensationType"
                                                        value="{{ $option['value'] }}"
                                                        class="text-blue-600" />
                                                    <div class="flex-1">
                                                        <span class="text-gray-900 dark:text-white font-medium">{{ $option['label'] }}</span>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            @php
                                                                $compensationTypeEnum = \App\Enums\CompensationType::from($option['value']);
                                                            @endphp
                                                            {{ $compensationTypeEnum->description() }}
                                                        </p>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error('compensationType') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <!-- Timeline & Details -->
                                <div class="space-y-4">
                                    <flux:input
                                        type="date"
                                        wire:model="applicationDeadline"
                                        label="Application Deadline" />
                                    @error('applicationDeadline') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                                    <flux:input
                                        type="date"
                                        wire:model="campaignCompletionDate"
                                        label="Campaign Completion Date" />
                                    @error('campaignCompletionDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                                    <!-- Compensation Details -->
                                    <div>
                                        <flux:label>Compensation Details</flux:label>
                                        <flux:editor
                                            wire:model="compensationDescription"
                                            placeholder="Describe your compensation offer in detail..." />
                                        @error('compensationDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>

                                    <flux:input
                                        type="number"
                                        wire:model="influencerCount"
                                        label="Number of Influencers Needed"
                                        placeholder="3"
                                        min="1"
                                        max="50" />
                                    @error('influencerCount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                                    <flux:input
                                        type="number"
                                        wire:model="exclusivityPeriod"
                                        label="Requested Exclusivity Period (in days)"
                                        placeholder="5"
                                        min="0"
                                         />
                                    @error('exclusivityPeriod') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Additional Requirements -->
                            <div class="mt-6">
                                <flux:label>Additional Requirements or Notes (Optional)</flux:label>
                                <flux:editor
                                    wire:model="additionalRequirements"
                                    placeholder="Any specific requirements, brand guidelines, content restrictions, or additional information for influencers..." />
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 6)
                    <!-- Step 6: Review & Publish -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Review & Publish</h2>

                            <!-- Campaign Overview -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Campaign Overview</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Project Name:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $projectName ?: 'Not specified' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Main Contact:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $mainContact ?: 'Not specified' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign Goal:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $campaignGoal }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign Type:</span>
                                            <div class="text-gray-900 dark:text-white">
                                                @if(is_array($campaignType) && count($campaignType) > 0)
                                                    @foreach($campaignType as $type)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 mr-2 mb-1">
                                                            {{ is_object($type) ? $type->label() : \App\Enums\CampaignType::from($type)->label() }}
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-gray-400">No types selected</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Location:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $targetZipCode }}{{ $targetArea ? ' - ' . $targetArea : '' }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Influencers Needed:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $influencerCount }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Application Deadline:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $applicationDeadline ? \Carbon\Carbon::parse($applicationDeadline)->format('M j, Y') : 'Not set' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign Completion:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $campaignCompletionDate ? \Carbon\Carbon::parse($campaignCompletionDate)->format('M j, Y') : 'Not set' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Compensation Type:</span>
                                            <p class="text-gray-900 dark:text-white">{{ \App\Enums\CompensationType::from($compensationType)->label() }}</p>
                                        </div>
                                        @if($compensationType === 'monetary')
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Compensation Amount:</span>
                                            <p class="text-gray-900 dark:text-white">${{ number_format($compensationAmount) }}</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Brand Information -->
                            @if($brandOverview || $currentAdvertisingCampaign || $brandStory)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Brand Information</h3>

                                @if($brandOverview)
                                <div class="mb-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Brand Overview:</span>
                                    <div class="mt-1 text-gray-900 dark:text-white prose prose-sm max-w-none">
                                        {!! $brandOverview !!}
                                    </div>
                                </div>
                                @endif

                                @if($currentAdvertisingCampaign)
                                <div class="mb-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Advertising Campaign:</span>
                                    <div class="mt-1 text-gray-900 dark:text-white prose prose-sm max-w-none">
                                        {!! $currentAdvertisingCampaign !!}
                                    </div>
                                </div>
                                @endif

                                @if($brandStory)
                                <div class="mb-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Brand Story:</span>
                                    <div class="mt-1 text-gray-900 dark:text-white prose prose-sm max-w-none">
                                        {!! $brandStory !!}
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Campaign Briefing -->
                            @if($campaignObjective || $keyInsights || $fanMotivator || $creativeConnection || $specificProducts || $postingRestrictions || $additionalConsiderations)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Campaign Briefing</h3>

                                @if($campaignObjective)
                                <div class="mb-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign Objective:</span>
                                    <div class="mt-1 text-gray-900 dark:text-white prose prose-sm max-w-none">
                                        {!! $campaignObjective !!}
                                    </div>
                                </div>
                                @endif

                                @if($keyInsights)
                                <div class="mb-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Key Insights:</span>
                                    <div class="mt-1 text-gray-900 dark:text-white prose prose-sm max-w-none">
                                        {!! $keyInsights !!}
                                    </div>
                                </div>
                                @endif

                                @if($fanMotivator)
                                <div class="mb-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Fan Motivator:</span>
                                    <div class="mt-1 text-gray-900 dark:text-white prose prose-sm max-w-none">
                                        {!! $fanMotivator !!}
                                    </div>
                                </div>
                                @endif

                                @if($creativeConnection)
                                <div class="mb-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Creative Connection:</span>
                                    <div class="mt-1 text-gray-900 dark:text-white prose prose-sm max-w-none">
                                        {!! $creativeConnection !!}
                                    </div>
                                </div>
                                @endif

                                @if($specificProducts)
                                <div class="mb-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Specific Products to Include:</span>
                                    <div class="mt-1 text-gray-900 dark:text-white prose prose-sm max-w-none">
                                        {!! $specificProducts !!}
                                    </div>
                                </div>
                                @endif

                                @if($postingRestrictions)
                                <div class="mb-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Posting Restrictions:</span>
                                    <div class="mt-1 text-gray-900 dark:text-white prose prose-sm max-w-none">
                                        {!! $postingRestrictions !!}
                                    </div>
                                </div>
                                @endif

                                @if($additionalConsiderations)
                                <div class="mb-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Additional Considerations:</span>
                                    <div class="mt-1 text-gray-900 dark:text-white prose prose-sm max-w-none">
                                        {!! $additionalConsiderations !!}
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Deliverables & Metrics -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Deliverables & Metrics</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Platforms:</span>
                                        <p class="text-gray-900 dark:text-white mt-1">
                                            @if(count($targetPlatforms) > 0)
                                                @php
                                                    $platformOptions = collect($this->getTargetPlatformOptions())->keyBy('value');
                                                    $selectedPlatforms = collect($targetPlatforms)->map(function($platform) use ($platformOptions) {
                                                        return $platformOptions->get($platform)['label'] ?? $platform;
                                                    })->join(', ');
                                                @endphp
                                                {{ $selectedPlatforms }}
                                            @else
                                                None selected
                                            @endif
                                        </p>
                                    </div>

                                    <div>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Success Metrics:</span>
                                        <p class="text-gray-900 dark:text-white mt-1">
                                            @if(count($successMetrics) > 0)
                                                @php
                                                    $metricOptions = collect($this->getSuccessMetricOptions())->keyBy('value');
                                                    $selectedMetrics = collect($successMetrics)->map(function($metric) use ($metricOptions) {
                                                        return $metricOptions->get($metric)['label'] ?? $metric;
                                                    })->join(', ');
                                                @endphp
                                                {{ $selectedMetrics }}
                                            @else
                                                None selected
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if(count($deliverables) > 0)
                                <div class="mt-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Deliverables:</span>
                                    <div class="mt-2 space-y-2">
                                        @php
                                            $deliverableOptions = collect($this->getDeliverableTypeOptions())->keyBy('value');
                                        @endphp
                                        @foreach($deliverables as $deliverable)
                                            <div class="flex items-center space-x-2">
                                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                                <span class="text-gray-900 dark:text-white">{{ $deliverableOptions->get($deliverable)['label'] ?? $deliverable }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if($timingDetails)
                                <div class="mt-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Timing Details:</span>
                                    <div class="mt-1 text-gray-900 dark:text-white prose prose-sm max-w-none">
                                        {!! $timingDetails !!}
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Compensation Details -->
                            @if($compensationDescription)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Compensation Details</h3>
                                <div class="text-gray-900 dark:text-white prose prose-sm max-w-none">
                                    {!! $compensationDescription !!}
                                </div>
                            </div>
                            @endif

                            <!-- Additional Requirements -->
                            @if($additionalRequirements)
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Additional Requirements</h3>
                                <div class="text-gray-900 dark:text-white prose prose-sm max-w-none">
                                    {!! $additionalRequirements !!}
                                </div>
                            </div>
                            @endif

                            <!-- Publish Options -->
                            <div>
                                <flux:label>Publish Action</flux:label>
                                <div class="mt-3 space-y-2">
                                    <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ $publishAction === 'publish' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                        <input
                                            type="radio"
                                            wire:model.live="publishAction"
                                            value="publish"
                                            class="text-blue-600" />
                                        <div class="flex-1">
                                            <span class="text-gray-900 dark:text-white font-medium">Publish Now</span>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Make your campaign live immediately</p>
                                        </div>
                                    </label>

                                    <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ $publishAction === 'schedule' ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                        <input
                                            type="radio"
                                            wire:model.live="publishAction"
                                            value="schedule"
                                            class="text-blue-600" />
                                        <div class="flex-1">
                                            <span class="text-gray-900 dark:text-white font-medium">Schedule for Later</span>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Set a specific date to publish</p>
                                        </div>
                                    </label>
                                </div>

                                @if($publishAction === 'schedule')
                                    <div class="mt-4">
                                        <flux:input
                                            type="date"
                                            wire:model="scheduledDate"
                                            label="Schedule Date"
                                            min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                                        @error('scheduledDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Navigation Buttons -->
                <div class="flex justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
                    <!-- Left side - Previous/Back -->
                    @if($currentStep > 1)
                        <flux:button
                            type="button"
                            variant="outline"
                            wire:click="previousStep"
                            icon="arrow-left">
                            Previous
                        </flux:button>
                    @else
                        <a href="{{ route('dashboard') }}">
                            <flux:button variant="outline" type="button" icon="arrow-left">
                                Back to Dashboard
                            </flux:button>
                        </a>
                    @endif

                    <!-- Right side - Action buttons -->
                    <div class="flex space-x-3">
                        <!-- Unschedule Campaign (for scheduled campaigns) -->
                        @if($this->isEditing() && $campaignId)
                            @php
                                $campaign = \App\Models\Campaign::find($campaignId);
                            @endphp
                            @if($campaign && $campaign->isScheduled())
                                <flux:button variant="outline" wire:click="unscheduleCampaign" icon="x-mark" class="text-orange-600 border-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/20">
                                    Unschedule
                                </flux:button>
                            @endif
                        @endif

                        <!-- Save Draft -->
                        <flux:button variant="outline" wire:click="saveDraft" icon="document-text">
                            Save Draft
                        </flux:button>

                        <!-- Save and Exit -->
                        <flux:button variant="outline" wire:click="saveAndExit" icon="arrow-right">
                            Save & Exit
                        </flux:button>

                        <!-- Continue/Next/Publish -->
                        @if($currentStep < $this->getTotalSteps())
                            <flux:button variant="primary" wire:click="nextStep" icon-right="arrow-right">
                                Continue
                            </flux:button>
                        @else
                            <flux:button variant="primary" wire:click="publishCampaign" icon="plus">
                                {{ $publishAction === 'publish' ? 'Publish Campaign' : 'Schedule Campaign' }}
                            </flux:button>
                        @endif
                    </div>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <div class="text-sm text-red-600 dark:text-red-400">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
                                                    @endif
    <!-- Beforeunload warning -->
    <script>
        window.addEventListener('beforeunload', function (e) {
            @if($hasUnsavedChanges)
                e.preventDefault();
                e.returnValue = '';
            @endif
        });

        // Handle URL updates
        document.addEventListener('livewire:init', () => {
            Livewire.on('url-updated', (event) => {
                const url = event.detail.url;
                window.history.pushState({}, '', url);
            });
        });
    </script>
</div>
</div>
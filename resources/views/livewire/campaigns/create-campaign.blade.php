<div>
    <!-- Auto-save indicator -->
    @if($hasUnsavedChanges)
        <div class="fixed top-4 right-4 z-50">
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
        <div class="fixed top-4 right-4 z-50">
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg px-4 py-2 shadow-lg">
                <div class="flex items-center space-x-2">
                    <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="text-sm text-green-800 dark:text-green-200">Saved at {{ $lastSavedAt }}</span>
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
                                    <flux:label>Campaign Type</flux:label>
                                    <div class="mt-3 space-y-2">
                                        @foreach($this->getCampaignTypeOptions() as $value => $label)
                                            <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ $campaignType === $value ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                                <input
                                                    type="radio"
                                                    wire:model="campaignType"
                                                    value="{{ $value }}"
                                                    class="text-blue-600" />
                                                <div class="flex-1">
                                                    <span class="text-gray-900 dark:text-white font-medium">{{ $label }}</span>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                                        @if($value === 'user_generated_content')
                                                            Influencers create original content featuring your brand
                                                        @elseif($value === 'social_post')
                                                            Dedicated social media posts about your business
                                                        @elseif($value === 'product_placement')
                                                            Natural integration of your products into their content
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
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <flux:input
                                        type="text"
                                        wire:model="targetZipCode"
                                        label="Target Zip Code"
                                        placeholder="45066"
                                        required />
                                    @error('targetZipCode') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

                                    <flux:input
                                        type="text"
                                        wire:model="targetArea"
                                        label="Target Area (Optional)"
                                        placeholder="Downtown Cincinnati" />
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 2)
                    <!-- Step 2: Campaign Details -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Campaign Details</h2>

                            <div class="space-y-4">
                                <!-- Brief Description -->
                                <div>
                                    <flux:label>Brief Description of Campaign</flux:label>
                                    <textarea
                                        wire:model="campaignDescription"
                                        rows="4"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="e.g., Get a coffee drink out in the areas of zipcode 45066. We want local food influencers to visit our new location, try our seasonal drinks, and share their authentic experience with their followers."
                                        required></textarea>
                                    @error('campaignDescription') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Provide a clear description of what you want influencers to do and what the campaign entails.
                                    </p>
                                </div>

                                <!-- Social Post Requirements -->
                                <div>
                                    <flux:label>Social Post Requirements (Check all that apply)</flux:label>
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($this->getSocialRequirementsOptions() as $value => $label)
                                            <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ in_array($value, $socialRequirements) ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                                <input
                                                    type="checkbox"
                                                    wire:model="socialRequirements"
                                                    value="{{ $value }}"
                                                    class="text-blue-600" />
                                                <span class="text-gray-900 dark:text-white">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('socialRequirements') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <!-- Product Placement Requirements -->
                                <div>
                                    <flux:label>Product Placement Requirements (Check all that apply)</flux:label>
                                    <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($this->getPlacementRequirementsOptions() as $value => $label)
                                            <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ in_array($value, $placementRequirements) ? 'border-green-500 bg-green-50 dark:bg-green-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                                <input
                                                    type="checkbox"
                                                    wire:model="placementRequirements"
                                                    value="{{ $value }}"
                                                    class="text-green-600" />
                                                <span class="text-gray-900 dark:text-white">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 3)
                    <!-- Step 3: Campaign Settings -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Campaign Settings</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Compensation Type -->
                                <div class="space-y-4">
                                    <div>
                                        <flux:label>Compensation Type</flux:label>
                                        <div class="mt-3 space-y-2">
                                            @foreach($this->getCompensationTypeOptions() as $value => $label)
                                                <label class="flex items-center space-x-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 {{ $compensationType === $value ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }}">
                                                    <input
                                                        type="radio"
                                                        wire:model="compensationType"
                                                        value="{{ $value }}"
                                                        class="text-blue-600" />
                                                    <div class="flex-1">
                                                        <span class="text-gray-900 dark:text-white font-medium">{{ $label }}</span>
                                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                                            @php
                                                                $compensationTypeEnum = \App\Enums\CompensationType::from($value);
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
                                        <textarea
                                            wire:model="compensationDescription"
                                            rows="3"
                                            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                            placeholder="Describe your compensation offer in detail..."></textarea>
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
                                </div>
                            </div>

                            <!-- Additional Requirements -->
                            <div class="mt-6">
                                <flux:label>Additional Requirements or Notes (Optional)</flux:label>
                                <textarea
                                    wire:model="additionalRequirements"
                                    rows="3"
                                    class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                    placeholder="Any specific requirements, brand guidelines, content restrictions, or additional information for influencers..."></textarea>
                            </div>
                        </div>
                    </div>

                @elseif($currentStep === 4)
                    <!-- Step 4: Review & Publish -->
                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Review & Publish</h2>

                            <!-- Campaign Summary -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-6">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Campaign Summary</h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign Goal:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $campaignGoal }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Campaign Type:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $this->getCampaignTypeOptions()[$campaignType] ?? '' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Target Location:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $targetZipCode }}{{ $targetArea ? ' - ' . $targetArea : '' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Compensation:</span>
                                            <p class="text-gray-900 dark:text-white">
                                                {{ $compensationDescription ?: \App\Enums\CompensationType::from($compensationType)->label() }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="space-y-3">
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Influencers Needed:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $influencerCount }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Application Deadline:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $applicationDeadline ? \Carbon\Carbon::parse($applicationDeadline)->format('M j, Y') : '' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Completion Date:</span>
                                            <p class="text-gray-900 dark:text-white">{{ $campaignCompletionDate ? \Carbon\Carbon::parse($campaignCompletionDate)->format('M j, Y') : '' }}</p>
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Social Requirements:</span>
                                            <p class="text-gray-900 dark:text-white">{{ count($socialRequirements) }} selected</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Description:</span>
                                    <p class="text-gray-900 dark:text-white mt-1">{{ $campaignDescription }}</p>
                                </div>
                            </div>

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

    <!-- Beforeunload warning -->
    <script>
        window.addEventListener('beforeunload', function (e) {
            @if($hasUnsavedChanges)
                e.preventDefault();
                e.returnValue = '';
            @endif
        });
    </script>
</div>
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
        <div class="flex items-center justify-between mb-8">
            <div>
                <flux:heading size="xl">{{ $this->isEditing() ? 'Edit Campaign' : 'Create Campaign' }}</flux:heading>
                <flux:text class="mt-1">Step {{ $currentStep }} of {{ $this->getTotalSteps() }}: {{ $this->getWizardSteps()[$currentStep] }}</flux:text>
            </div>
        </div>

        <!-- Defaults Applied Notice -->
        @if($hasAppliedDefaults && $currentStep <= 2)
            <flux:callout icon="sparkles" class="mb-6 dark:bg-zinc-800 dark:text-zinc-300">
                <div class="flex items-center justify-between">
                    <span>Brand defaults applied from your business profile settings.</span>
                    <flux:link href="{{ route('business.settings') }}" wire:navigate class="text-sm">Manage defaults</flux:link>
                </div>
            </flux:callout>
        @endif

        <!-- Progress Bar -->
        @php
            $stepsWithErrors = $this->getStepsWithErrors();
        @endphp
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                @foreach($this->getWizardSteps() as $stepNumber => $stepName)
                    @php
                        $hasErrors = in_array($stepNumber, $stepsWithErrors);
                        $isCompleted = $stepNumber < $currentStep && !$hasErrors;
                        $isCurrent = $stepNumber === $currentStep;
                    @endphp
                    <div class="flex items-center flex-1">
                        <div class="relative flex items-center justify-center w-10 h-10 rounded-full border-2 transition-all duration-200 cursor-pointer hover:scale-110
                            {{ $isCompleted ? 'bg-green-500 border-green-500 text-white' : '' }}
                            {{ $isCurrent ? 'bg-blue-600 border-blue-600 text-white' : '' }}
                            {{ !$isCompleted && !$isCurrent ? 'bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500' : '' }}
                            {{ $hasErrors && !$isCurrent ? 'border-amber-500' : '' }}"
                             wire:click="goToStep({{ $stepNumber }})">
                            @if($isCompleted)
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <span class="text-sm font-semibold">{{ $stepNumber }}</span>
                            @endif
                            {{-- Warning indicator for steps with errors --}}
                            @if($hasErrors && !$isCurrent)
                                <div class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-amber-500 text-white text-xs font-bold rounded-full">
                                    !
                                </div>
                            @endif
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-sm font-medium cursor-pointer {{ $isCurrent ? 'text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300' }}"
                                 wire:click="goToStep({{ $stepNumber }})">
                                {{ $stepName }}
                                @if($hasErrors && !$isCurrent)
                                    <span class="text-amber-500 ml-1" title="Missing required fields">*</span>
                                @endif
                            </div>
                        </div>
                        @if($stepNumber < $this->getTotalSteps())
                            <div class="flex-1 h-0.5 mx-4 {{ $isCompleted ? 'bg-green-500' : 'bg-gray-200 dark:bg-gray-600' }}"></div>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-500 ease-out"
                     style="width: {{ ($currentStep / $this->getTotalSteps()) * 100 }}%"></div>
            </div>
        </div>

        <!-- Campaign Creation Form -->
        <flux:card class="!p-6">
            {{-- Show validation issues for current step --}}
            @php
                $currentStepErrors = $this->getStepErrors($currentStep);
            @endphp
            @if(!empty($currentStepErrors))
                <flux:callout variant="warning" icon="exclamation-triangle" class="mb-6">
                    <flux:callout.heading>Missing required fields</flux:callout.heading>
                    <flux:callout.text>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            @foreach($currentStepErrors as $field => $messages)
                                @foreach($messages as $message)
                                    <li>{{ $message }}</li>
                                @endforeach
                            @endforeach
                        </ul>
                    </flux:callout.text>
                </flux:callout>
            @endif

            @if($currentStep === 1)
                @include('livewire.campaigns.partials.step1')
            @elseif($currentStep === 2)
                @include('livewire.campaigns.partials.step2')
            @elseif($currentStep === 3)
                @include('livewire.campaigns.partials.step3')
            @elseif($currentStep === 4)
                @include('livewire.campaigns.partials.step4')
            @endif

            <!-- Navigation Buttons -->
            <div class="flex justify-between pt-6 mt-6 border-t border-gray-200 dark:border-gray-700">
                @if($currentStep > 1)
                    <flux:button variant="ghost" wire:click="previousStep" icon="arrow-left">
                        Previous
                    </flux:button>
                @else
                    <flux:button variant="ghost" href="{{ route('dashboard') }}" icon="arrow-left">
                        Back to Dashboard
                    </flux:button>
                @endif

                <div class="flex space-x-3">
                    @if($this->isEditing() && $campaignId)
                        @php $campaign = \App\Models\Campaign::find($campaignId); @endphp
                        @if($campaign && $campaign->isScheduled())
                            <flux:button variant="ghost" wire:click="unscheduleCampaign" icon="x-mark">
                                Unschedule
                            </flux:button>
                        @endif
                    @endif

                    <flux:button variant="ghost" wire:click="saveAndExit" icon="arrow-right-start-on-rectangle">
                        Save & Exit
                    </flux:button>

                    @if($currentStep < $this->getTotalSteps())
                        <flux:button variant="primary" wire:click="nextStep" icon-trailing="arrow-right">
                            Continue
                        </flux:button>
                    @else
                        <flux:button variant="primary" wire:click="publishCampaign" icon="rocket-launch">
                            {{ $publishAction === 'publish' ? 'Publish Campaign' : 'Schedule Campaign' }}
                        </flux:button>
                    @endif
                </div>
            </div>

        </flux:card>
    </div>
    @endif

    <script>
        window.addEventListener('beforeunload', function (e) {
            @if($hasUnsavedChanges)
                e.preventDefault();
                e.returnValue = '';
            @endif
        });

        document.addEventListener('livewire:init', () => {
            Livewire.on('url-updated', (event) => {
                const url = event.detail.url;
                window.history.pushState({}, '', url);
            });
        });
    </script>
</div>

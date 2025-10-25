<!-- Navigation Buttons -->
<div class="flex justify-between items-center mt-10 pt-8 border-t border-gray-200 dark:border-gray-600">
    @if ($currentStep > 1)
    <flux:button
        variant="ghost"
        :disabled="$currentStep === 1"
        wire:click="previousStep"
        class="flex items-center space-x-2"
        icon="chevron-left"
    >
        <span>Previous</span>
    </flux:button>
    @else
    <div></div>
    @endif

    <div class="flex items-center space-x-4">
        @if($currentStep < count($steps))
            <flux:button class="px-8 bg-gradient-to-r from-blue-600 to-cyan-600 hover:from-blue-700 hover:to-cyan-700" wire:click="nextStep" :disabled="$isNavigationDisabled">
                <span class="flex items-center space-x-2">
                    <span>Continue</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </span>
            </flux:button>
        @else
            <flux:button class="px-8 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700" wire:click="completeOnboarding">
                <span class="flex items-center space-x-2">
                    <span>Complete Setup</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </span>
            </flux:button>
        @endif
    </div>
</div>
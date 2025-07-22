<!-- Navigation Buttons -->
<div class="flex justify-between pt-6 border-t border-gray-200 dark:border-gray-700">
    @if($currentStep > 1)
        <flux:button
            type="button"
            variant="ghost"
            wire:click="previousStep">
            ← Previous
        </flux:button>
    @else
        <div></div>
    @endif

    @if($currentStep < $this->getTotalSteps())
        <flux:button
            type="button"
            variant="primary"
            wire:click="nextStep">
            Next →
        </flux:button>
    @else
        <flux:button
            type="button"
            variant="primary"
            wire:click="completeOnboarding">
            Complete Setup
        </flux:button>
    @endif
</div>
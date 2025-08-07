<?php

namespace App\Livewire\Traits;

trait HasWizardSteps
{
    public int $currentStep = 1;

    public function getTotalSteps(): int
    {
        return 4;
    }

    /**
     * Advance to the next step after validation
     */
    public function nextStep(): void
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->getTotalSteps()) {
            $this->currentStep++;
        }
    }

    /**
     * Go back to the previous step
     */
    public function previousStep(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    /**
     * Get the current step progress as a percentage
     */
    public function getStepProgress(): float
    {
        return ($this->currentStep / $this->getTotalSteps()) * 100;
    }

    /**
     * Check if we're on the first step
     */
    public function isFirstStep(): bool
    {
        return $this->currentStep === 1;
    }

    /**
     * Check if we're on the last step
     */
    public function isLastStep(): bool
    {
        return $this->currentStep === $this->getTotalSteps();
    }

    /**
     * Validate the current step - must be implemented by the using class
     */
    abstract protected function validateCurrentStep(): void;

    /**
     * Complete the wizard process - must be implemented by the using class
     */
    abstract public function completeOnboarding(): void;
}

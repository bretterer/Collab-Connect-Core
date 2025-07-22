<?php

namespace App\Livewire\Onboarding;

use App\Enums\CampaignType;
use App\Enums\CollaborationGoal;
use App\Enums\Niche;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\HasWizardSteps;
use App\Services\ProfileService;
use App\Services\ValidationService;
use Livewire\Attributes\Layout;

#[Layout('layouts.auth')]
class BusinessOnboarding extends BaseComponent
{
    use HasWizardSteps;

    // Step 1: Business Information
    public string $businessName = '';

    public string $industry = '';

    public array $websites = [''];

    public string $primaryZipCode = '';

    public int $locationCount = 1;

    public bool $isFranchise = false;

    public bool $isNationalBrand = false;

    // Step 2: Contact & Billing
    public string $contactName = '';

    public string $contactEmail = '';

    public bool $requestCustomQuote = false;

    // Step 3: Collaboration Goals
    public array $collaborationGoals = [];

    public array $campaignTypes = [];

    public function getTotalSteps(): int
    {
        return 3;
    }

    protected function getCollaborationGoalOptions(): array
    {
        return CollaborationGoal::forBusinesses();
    }

    protected function getCampaignTypeOptions(): array
    {
        return CampaignType::forBusinesses();
    }

    protected function getNicheOptions(): array
    {
        return Niche::forBusinesses();
    }

    public function mount()
    {
        $user = $this->getAuthenticatedUser();
        $this->contactName = $user->name ?? '';
        $this->contactEmail = $user->email ?? '';
    }

    public function addWebsite()
    {
        $this->addToArray('websites', '');
    }

    public function removeWebsite(int $index)
    {
        $this->removeFromArray('websites', $index, 1);
    }



    public function updated($propertyName)
    {
        // Reset national brand flag if location count is less than 30
        if ($propertyName === 'locationCount' && $this->locationCount < 30) {
            $this->isNationalBrand = false;
        }
    }

    protected function validateCurrentStep(): void
    {
        $rules = ValidationService::getStepRules('business', $this->currentStep);
        $messages = ValidationService::getStepMessages('business', $this->currentStep);
        $this->validate($rules, $messages);
    }

    public function completeOnboarding(): void
    {
        $this->validateCurrentStep();

        $user = $this->getAuthenticatedUser();

        // Create business profile using service
        ProfileService::createBusinessProfile($user, [
            'businessName' => $this->businessName,
            'industry' => $this->industry,
            'websites' => $this->websites,
            'primaryZipCode' => $this->primaryZipCode,
            'locationCount' => $this->locationCount,
            'isFranchise' => $this->isFranchise,
            'isNationalBrand' => $this->isNationalBrand,
            'contactName' => $this->contactName,
            'contactEmail' => $this->contactEmail,
            'collaborationGoals' => $this->collaborationGoals,
            'campaignTypes' => $this->campaignTypes,

        ]);

        $this->flashAndRedirect('Welcome to CollabConnect! Your business profile has been created successfully.', 'dashboard');
    }

    public function render()
    {
        return view('livewire.onboarding.business-onboarding', [
            'collaborationGoalOptions' => $this->getCollaborationGoalOptions(),
            'campaignTypeOptions' => $this->getCampaignTypeOptions(),
            'nicheOptions' => $this->getNicheOptions(),
        ]);
    }
}

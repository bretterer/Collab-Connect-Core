<?php

namespace App\Livewire\Onboarding;

use App\Enums\CampaignType;
use App\Enums\CollaborationGoal;
use App\Enums\Niche;
use App\Enums\SubscriptionPlan;
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

    public string $subscriptionPlan = '';

    public bool $requestCustomQuote = false;

    // Step 3: Collaboration Goals
    public array $collaborationGoals = [];

    public array $campaignTypes = [];

    // Step 4: Team Setup
    public array $teamMembers = [
        ['name' => '', 'email' => ''],
    ];

    protected function getSubscriptionPlanOptions(): array
    {
        return $this->getEnumOptions(SubscriptionPlan::class, 'forBusinesses');
    }

    protected function getCollaborationGoalOptions(): array
    {
        return $this->getEnumOptions(CollaborationGoal::class, 'forBusinesses');
    }

    protected function getCampaignTypeOptions(): array
    {
        return $this->getEnumOptions(CampaignType::class);
    }

    protected function getNicheOptions(): array
    {
        return $this->getEnumOptions(Niche::class);
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

    public function addTeamMember()
    {
        $this->addToArray('teamMembers', ['name' => '', 'email' => '']);
    }

    public function removeTeamMember(int $index)
    {
        $this->removeFromArray('teamMembers', $index, 1);
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
        $this->validate($rules);
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
            'subscriptionPlan' => $this->subscriptionPlan,
            'collaborationGoals' => $this->collaborationGoals,
            'campaignTypes' => $this->campaignTypes,
            'teamMembers' => $this->teamMembers,
        ]);

        $this->flashAndRedirect('Welcome to CollabConnect! Your business profile has been created successfully.', 'dashboard');
    }

    public function render()
    {
        return view('livewire.onboarding.business-onboarding', [
            'subscriptionPlanOptions' => $this->getSubscriptionPlanOptions(),
            'collaborationGoalOptions' => $this->getCollaborationGoalOptions(),
            'campaignTypeOptions' => $this->getCampaignTypeOptions(),
            'nicheOptions' => $this->getNicheOptions(),
        ]);
    }
}

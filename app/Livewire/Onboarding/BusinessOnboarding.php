<?php

namespace App\Livewire\Onboarding;

use App\Enums\CampaignType;
use App\Enums\CollaborationGoal;
use App\Enums\Niche;
use App\Enums\SubscriptionPlan;
use App\Livewire\Traits\HasWizardSteps;
use App\Models\BusinessProfile;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class BusinessOnboarding extends Component
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
        return SubscriptionPlan::toOptions(SubscriptionPlan::forBusinesses());
    }

    protected function getCollaborationGoalOptions(): array
    {
        return CollaborationGoal::toOptions(CollaborationGoal::forBusinesses());
    }

    protected function getCampaignTypeOptions(): array
    {
        return CampaignType::toOptions();
    }

    protected function getNicheOptions(): array
    {
        return Niche::toOptions();
    }

    public function mount()
    {
        $user = $this->getAuthenticatedUser();
        $this->contactName = $user->name ?? '';
        $this->contactEmail = $user->email ?? '';
    }

    public function addWebsite()
    {
        $this->websites[] = '';
    }

    public function removeWebsite(int $index)
    {
        if (count($this->websites) > 1) {
            unset($this->websites[$index]);
            $this->websites = array_values($this->websites);
        }
    }

    public function addTeamMember()
    {
        $this->teamMembers[] = ['name' => '', 'email' => ''];
    }

    public function removeTeamMember(int $index)
    {
        if (count($this->teamMembers) > 1) {
            unset($this->teamMembers[$index]);
            $this->teamMembers = array_values($this->teamMembers);
        }
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
        $rules = match ($this->currentStep) {
            1 => [
                'businessName' => 'required|string|max:255',
                'industry' => 'required|string|max:255',
                'primaryZipCode' => 'required|string|max:10',
                'locationCount' => 'required|integer|min:1',
                'websites.*' => 'nullable|url',
            ],
            2 => [
                'contactName' => 'required|string|max:255',
                'contactEmail' => 'required|email|max:255',
                'subscriptionPlan' => 'required|in:'.implode(',', array_keys($this->getSubscriptionPlanOptions())),
            ],
            3 => [
                'collaborationGoals' => 'required|array|min:1',
                'collaborationGoals.*' => 'in:'.implode(',', array_keys($this->getCollaborationGoalOptions())),
                'campaignTypes' => 'required|array|min:1',
                'campaignTypes.*' => 'in:'.implode(',', array_keys($this->getCampaignTypeOptions())),
            ],
            4 => [
                'teamMembers.*.name' => 'required|string|max:255',
                'teamMembers.*.email' => 'required|email|max:255',
            ],
        };

        $this->validate($rules);
    }

    public function completeOnboarding(): void
    {
        $this->validateCurrentStep();

        $user = $this->getAuthenticatedUser();

        // Filter out empty websites
        $filteredWebsites = array_filter($this->websites, fn ($website) => ! empty($website));

        // Create business profile
        BusinessProfile::create([
            'user_id' => $user->id,
            'business_name' => $this->businessName,
            'industry' => $this->industry,
            'websites' => $filteredWebsites,
            'primary_zip_code' => $this->primaryZipCode,
            'location_count' => $this->locationCount,
            'is_franchise' => $this->isFranchise,
            'is_national_brand' => $this->isNationalBrand,
            'contact_name' => $this->contactName,
            'contact_email' => $this->contactEmail,
            'subscription_plan' => $this->subscriptionPlan,
            'collaboration_goals' => $this->collaborationGoals,
            'campaign_types' => $this->campaignTypes,
            'team_members' => $this->teamMembers,
            'onboarding_completed' => true,
        ]);

        session()->flash('message', 'Welcome to CollabConnect! Your business profile has been created successfully.');
        $this->redirect(route('dashboard'), navigate: true);
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

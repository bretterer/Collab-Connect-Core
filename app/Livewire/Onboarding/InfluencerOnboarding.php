<?php

namespace App\Livewire\Onboarding;

use App\Enums\CollaborationGoal;
use App\Enums\Niche;
use App\Enums\SocialPlatform;
use App\Enums\SubscriptionPlan;
use App\Livewire\BaseComponent;
use App\Livewire\Traits\HasWizardSteps;
use App\Services\ProfileService;
use App\Services\ValidationService;
use Livewire\Attributes\Layout;

#[Layout('layouts.auth')]
class InfluencerOnboarding extends BaseComponent
{
    use HasWizardSteps;

    // Step 1: Profile Information
    public string $creatorName = '';

    public string $primaryNiche = '';

    public string $primaryZipCode = '';

    // Step 2: Social Media Connections
    public array $socialMediaAccounts = [
        ['platform' => 'instagram', 'username' => '', 'follower_count' => 0, 'is_primary' => false],
    ];

    // Step 3: Media Kit & Portfolio
    public string $mediaKitUrl = '';

    public bool $hasMediaKit = false;

    public bool $wantMediaKitBuilder = false;

    // Step 4: Collaboration Preferences & Pricing
    public array $collaborationPreferences = [];

    public array $preferredBrands = [];

    public string $subscriptionPlan = '';

    protected function getNicheOptions(): array
    {
        return $this->getEnumOptions(Niche::class);
    }

    protected function getPlatformOptions(): array
    {
        return $this->getEnumOptions(SocialPlatform::class);
    }

    protected function getCollaborationPreferenceOptions(): array
    {
        return $this->getEnumOptions(CollaborationGoal::class, 'forInfluencers');
    }

    protected function getSubscriptionPlanOptions(): array
    {
        return $this->getEnumOptions(SubscriptionPlan::class, 'forInfluencers');
    }

    public function mount()
    {
        $user = $this->getAuthenticatedUser();
        $this->creatorName = $user->name;
    }

    public function addSocialMediaAccount()
    {
        $this->addToArray('socialMediaAccounts', [
            'platform' => 'instagram',
            'username' => '',
            'follower_count' => 0,
            'is_primary' => false,
        ]);
    }

    public function removeSocialMediaAccount($index)
    {
        $this->removeFromArray('socialMediaAccounts', $index, 1);
    }

    public function setPrimaryAccount($index)
    {
        foreach ($this->socialMediaAccounts as $key => $account) {
            $this->socialMediaAccounts[$key]['is_primary'] = ($key === $index);
        }
    }

    protected function validateCurrentStep(): void
    {
        $rules = ValidationService::getStepRules('influencer', $this->currentStep);

        // Handle conditional validation for media kit URL
        if ($this->currentStep === 3 && $this->hasMediaKit) {
            $rules['mediaKitUrl'] = 'required|url';
        }

        $this->validate($rules);
    }

    public function completeOnboarding(): void
    {
        $this->validateCurrentStep();

        $user = $this->getAuthenticatedUser();

        // Create influencer profile using service
        ProfileService::createInfluencerProfile($user, [
            'creatorName' => $this->creatorName,
            'primaryNiche' => $this->primaryNiche,
            'primaryZipCode' => $this->primaryZipCode,
            'mediaKitUrl' => $this->mediaKitUrl,
            'hasMediaKit' => $this->hasMediaKit,
            'collaborationPreferences' => $this->collaborationPreferences,
            'preferredBrands' => $this->preferredBrands,
            'subscriptionPlan' => $this->subscriptionPlan,
        ]);

        // Create social media accounts using service
        ProfileService::createSocialMediaAccounts($user, $this->socialMediaAccounts);

        $this->flashAndRedirect('Welcome to CollabConnect! Your influencer profile has been created successfully.', 'dashboard');
    }



    public function render()
    {
        return view('livewire.onboarding.influencer-onboarding', [
            'nicheOptions' => $this->getNicheOptions(),
            'platformOptions' => $this->getPlatformOptions(),
            'collaborationPreferenceOptions' => $this->getCollaborationPreferenceOptions(),
            'subscriptionPlanOptions' => $this->getSubscriptionPlanOptions(),
        ]);
    }
}

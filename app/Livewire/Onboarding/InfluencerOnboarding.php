<?php

namespace App\Livewire\Onboarding;

use App\Enums\Niche;
use App\Enums\SocialPlatform;
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
        ['platform' => '', 'username' => '', 'follower_count' => null, 'is_primary' => false],
    ];

    public function getTotalSteps(): int
    {
        return 2;
    }

    protected function getNicheOptions(): array
    {
        return Niche::forInfluencers();
    }

    protected function getPlatformOptions(): array
    {
        return SocialPlatform::forInfluencers();
    }

    public function mount()
    {
        $user = $this->getAuthenticatedUser();
        $this->creatorName = $user->name;
    }

    public function addSocialMediaAccount()
    {
        $this->addToArray('socialMediaAccounts', [
            'platform' => '',
            'username' => '',
            'follower_count' => null,
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

    public function removePrimaryAccount($index)
    {
        $this->socialMediaAccounts[$index]['is_primary'] = false;
    }

    protected function validateCurrentStep(): void
    {
        $rules = ValidationService::getStepRules('influencer', $this->currentStep);
        $messages = ValidationService::getStepMessages('influencer', $this->currentStep);
        $this->validate($rules, $messages);
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
        ]);
    }
}

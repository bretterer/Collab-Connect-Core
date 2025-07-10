<?php

namespace App\Livewire\Onboarding;

use App\Enums\CollaborationGoal;
use App\Enums\Niche;
use App\Enums\SocialPlatform;
use App\Enums\SubscriptionPlan;
use App\Livewire\Traits\HasWizardSteps;
use App\Models\InfluencerProfile;
use App\Models\SocialMediaAccount;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class InfluencerOnboarding extends Component
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
        return Niche::toOptions();
    }

    protected function getPlatformOptions(): array
    {
        return SocialPlatform::toOptions();
    }

    protected function getCollaborationPreferenceOptions(): array
    {
        return CollaborationGoal::toOptions(CollaborationGoal::forInfluencers());
    }

    protected function getSubscriptionPlanOptions(): array
    {
        return SubscriptionPlan::toOptions(SubscriptionPlan::forInfluencers());
    }

    public function mount()
    {
        $user = $this->getAuthenticatedUser();
        $this->creatorName = $user->name;
    }

    public function addSocialMediaAccount()
    {
        $this->socialMediaAccounts[] = [
            'platform' => 'instagram',
            'username' => '',
            'follower_count' => 0,
            'is_primary' => false,
        ];
    }

    public function removeSocialMediaAccount($index)
    {
        if (count($this->socialMediaAccounts) > 1) {
            unset($this->socialMediaAccounts[$index]);
            $this->socialMediaAccounts = array_values($this->socialMediaAccounts);
        }
    }

    public function setPrimaryAccount($index)
    {
        foreach ($this->socialMediaAccounts as $key => $account) {
            $this->socialMediaAccounts[$key]['is_primary'] = ($key === $index);
        }
    }

    protected function validateCurrentStep(): void
    {
        $rules = match ($this->currentStep) {
            1 => [
                'creatorName' => 'required|string|max:255',
                'primaryNiche' => 'required|in:'.implode(',', array_keys($this->getNicheOptions())),
                'primaryZipCode' => 'required|string|max:10',
            ],
            2 => [
                'socialMediaAccounts.*.platform' => 'required|in:'.implode(',', array_keys($this->getPlatformOptions())),
                'socialMediaAccounts.*.username' => 'required|string|max:255',
                'socialMediaAccounts.*.follower_count' => 'required|integer|min:0',
            ],
            3 => [
                'mediaKitUrl' => $this->hasMediaKit ? 'required|url' : 'nullable|url',
            ],
            4 => [
                'collaborationPreferences' => 'required|array|min:1',
                'subscriptionPlan' => 'required|in:'.implode(',', array_keys($this->getSubscriptionPlanOptions())),
            ],
        };

        $this->validate($rules);
    }

    public function completeOnboarding(): void
    {
        $this->validateCurrentStep();

        $user = $this->getAuthenticatedUser();

        // Create influencer profile
        $influencerProfile = InfluencerProfile::create([
            'user_id' => $user->id,
            'creator_name' => $this->creatorName,
            'primary_niche' => $this->primaryNiche,
            'primary_zip_code' => $this->primaryZipCode,
            'media_kit_url' => $this->mediaKitUrl,
            'has_media_kit' => $this->hasMediaKit,
            'collaboration_preferences' => $this->collaborationPreferences,
            'preferred_brands' => $this->preferredBrands,
            'subscription_plan' => $this->subscriptionPlan,
            'onboarding_completed' => true,
        ]);

        // Create social media accounts
        foreach ($this->socialMediaAccounts as $account) {
            if (! empty($account['username'])) {
                SocialMediaAccount::create([
                    'user_id' => $user->id,
                    'platform' => $account['platform'],
                    'username' => $account['username'],
                    'url' => $this->generateSocialMediaUrl($account['platform'], $account['username']),
                    'follower_count' => $account['follower_count'],
                    'is_primary' => $account['is_primary'] ?? false,
                ]);
            }
        }

        session()->flash('message', 'Welcome to CollabConnect! Your influencer profile has been created successfully.');
        $this->redirect(route('dashboard'), navigate: true);
    }

    private function generateSocialMediaUrl(string $platform, string $username): string
    {
        $platformEnum = SocialPlatform::from($platform);

        return $platformEnum->generateUrl($username);
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

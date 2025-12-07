<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Auth;
use Laravel\Pennant\Feature;
use Livewire\Component;

class FeatureGate extends Component
{
    public ?string $feature = null;

    public bool $requiresSubscription = false;

    public string $gateType = 'coming_soon';

    public string $title = 'Coming Soon';

    public string $description = '';

    public array $features = [];

    public ?string $icon = 'rocket-launch';

    public ?string $expectedDate = null;

    public bool $showNotifyButton = false;

    public string $subscriptionHeading = 'Upgrade Required';

    public string $subscriptionDescription = 'This feature requires an active subscription.';

    public array $subscriptionFeatures = [];

    public string $subscriptionButtonText = 'View Subscription Plans';

    public string $subscriptionVariant = 'purple';

    public ?string $forceState = null;

    public function mount(
        ?string $feature = null,
        bool $requiresSubscription = false,
        ?string $title = null,
        ?string $description = null,
        ?array $features = null,
        ?string $icon = null,
        ?string $expectedDate = null,
        bool $showNotifyButton = false,
        ?string $subscriptionHeading = null,
        ?string $subscriptionDescription = null,
        ?array $subscriptionFeatures = null,
        ?string $subscriptionButtonText = null,
        ?string $subscriptionVariant = null,
        ?string $forceState = null
    ): void {
        $this->feature = $feature;
        $this->requiresSubscription = $requiresSubscription;
        $this->forceState = $forceState;

        if ($title) {
            $this->title = $title;
        }
        if ($description) {
            $this->description = $description;
        }
        if ($features) {
            $this->features = $features;
        }
        if ($icon) {
            $this->icon = $icon;
        }
        if ($expectedDate) {
            $this->expectedDate = $expectedDate;
        }
        $this->showNotifyButton = $showNotifyButton;

        if ($subscriptionHeading) {
            $this->subscriptionHeading = $subscriptionHeading;
        }
        if ($subscriptionDescription) {
            $this->subscriptionDescription = $subscriptionDescription;
        }
        if ($subscriptionFeatures) {
            $this->subscriptionFeatures = $subscriptionFeatures;
        }
        if ($subscriptionButtonText) {
            $this->subscriptionButtonText = $subscriptionButtonText;
        }
        if ($subscriptionVariant) {
            $this->subscriptionVariant = $subscriptionVariant;
        }

        $this->determineGateType();
    }

    protected function determineGateType(): void
    {
        if ($this->forceState && in_array($this->forceState, ['coming_soon', 'subscription_required', 'accessible'])) {
            $this->gateType = $this->forceState;

            return;
        }

        if ($this->feature && ! $this->isFeatureActive()) {
            $this->gateType = 'coming_soon';

            return;
        }

        if ($this->requiresSubscription && ! $this->userHasSubscription()) {
            $this->gateType = 'subscription_required';

            return;
        }

        $this->gateType = 'accessible';
    }

    protected function isFeatureActive(): bool
    {
        if (! $this->feature) {
            return true;
        }

        $user = Auth::user();

        if ($user) {
            return Feature::for($user)->active($this->feature);
        }

        return Feature::active($this->feature);
    }

    protected function userHasSubscription(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isBusinessAccount()) {
            $business = $user->currentBusiness;

            return $business && $business->subscribed();
        }

        if ($user->isInfluencerAccount()) {
            $influencer = $user->influencer;

            return $influencer && $influencer->subscribed();
        }

        return false;
    }

    public function isAccessible(): bool
    {
        return $this->gateType === 'accessible';
    }

    public function isComingSoon(): bool
    {
        return $this->gateType === 'coming_soon';
    }

    public function isSubscriptionRequired(): bool
    {
        return $this->gateType === 'subscription_required';
    }

    public function render()
    {
        return view('livewire.components.feature-gate');
    }
}

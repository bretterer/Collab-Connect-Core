<?php

namespace App\Livewire\Admin;

use App\Settings\PromotionSettings;
use App\Settings\RegistrationMarkets;
use App\Settings\SubscriptionSettings;
use Flux\Flux;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Settings extends Component
{
    public string $activeTab = 'subscription';

    // Subscription Settings
    public int $trialPeriodDays = 14;

    // Registration Settings
    public bool $marketsEnabled = false;

    // Promotion Settings
    public int $profilePromotionDays = 7;

    public function mount(SubscriptionSettings $subscriptionSettings, RegistrationMarkets $registrationMarkets, PromotionSettings $promotionSettings): void
    {
        $this->trialPeriodDays = $subscriptionSettings->trialPeriodDays;
        $this->marketsEnabled = $registrationMarkets->enabled;
        $this->profilePromotionDays = $promotionSettings->profilePromotionDays;
    }

    public function saveSubscriptionSettings(SubscriptionSettings $settings): void
    {
        $this->validate([
            'trialPeriodDays' => ['required', 'integer', 'min:0', 'max:365'],
        ]);

        $settings->trialPeriodDays = $this->trialPeriodDays;
        $settings->save();

        Flux::toast(
            heading: 'Settings Saved',
            text: 'Subscription settings have been updated.',
            variant: 'success',
        );
    }

    public function saveRegistrationSettings(RegistrationMarkets $settings): void
    {
        $settings->enabled = $this->marketsEnabled;
        $settings->save();

        Flux::toast(
            heading: 'Settings Saved',
            text: 'Registration settings have been updated.',
            variant: 'success',
        );
    }

    public function savePromotionSettings(PromotionSettings $settings): void
    {
        $this->validate([
            'profilePromotionDays' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $settings->profilePromotionDays = $this->profilePromotionDays;
        $settings->save();

        Flux::toast(
            heading: 'Settings Saved',
            text: 'Promotion settings have been updated.',
            variant: 'success',
        );
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}

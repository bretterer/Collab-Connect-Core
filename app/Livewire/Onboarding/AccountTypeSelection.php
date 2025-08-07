<?php

namespace App\Livewire\Onboarding;

use App\Enums\AccountType;
use App\Livewire\BaseComponent;
use App\Services\ValidationService;
use Livewire\Attributes\Layout;

#[Layout('layouts.auth')]
class AccountTypeSelection extends BaseComponent
{
    public string $selectedAccountType = '';

    public function selectAccountType(string $accountType): void
    {
        $this->selectedAccountType = $accountType;
    }

    public function continue(): void
    {
        $this->validate(ValidationService::accountTypeRules());

        $user = $this->getAuthenticatedUser();

        if ($this->selectedAccountType === 'business') {
            $user->update(['account_type' => AccountType::BUSINESS]);
            $this->safeRedirect('onboarding.business');
        } else {
            $user->update(['account_type' => AccountType::INFLUENCER]);
            $this->safeRedirect('onboarding.influencer');
        }
    }

    public function render()
    {
        return view('livewire.onboarding.account-type-selection');
    }
}

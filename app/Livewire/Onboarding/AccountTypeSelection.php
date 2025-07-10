<?php

namespace App\Livewire\Onboarding;

use App\Enums\AccountType;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class AccountTypeSelection extends Component
{
    public string $selectedAccountType = '';

    public function selectAccountType(string $accountType): void
    {
        $this->selectedAccountType = $accountType;
    }

    public function continue(): void
    {
        $this->validate([
            'selectedAccountType' => ['required', 'in:business,influencer'],
        ]);

        $user = auth()->user();

        if ($this->selectedAccountType === 'business') {
            $user->update(['account_type' => AccountType::BUSINESS]);
            $this->redirect(route('onboarding.business'), navigate: true);
        } else {
            $user->update(['account_type' => AccountType::INFLUENCER]);
            $this->redirect(route('onboarding.influencer'), navigate: true);
        }
    }

    public function render()
    {
        return view('livewire.onboarding.account-type-selection');
    }
}

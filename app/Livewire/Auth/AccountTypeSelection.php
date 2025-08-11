<?php

namespace App\Livewire\Auth;

use App\Enums\AccountType;
use App\Services\ValidationService;
use Illuminate\Auth\Events\Registered;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class AccountTypeSelection extends Component
{
    public ?AccountType $selectedAccountType = null;

    public function selectAccountType(AccountType $accountType): void
    {
        $this->selectedAccountType = $accountType;
    }

    public function continue(): void
    {
        $this->validate(ValidationService::accountTypeRules());

        $user = auth()->user;

        try {
            $user->setAccountType($this->selectedAccountType);
        } catch (\Exception $e) {
            $this->addError('account_type', 'Failed to set account type. Please try again.');

            return;
        }

        event(new Registered($user));

    }

    public function render()
    {
        return view('livewire.auth.account-type-selection');
    }
}

<?php

namespace App\Livewire\Admin\Users\Tabs;

use App\Enums\AccountType;
use App\Models\User;
use Flux\Flux;
use Livewire\Component;

class AccountTab extends Component
{
    public User $user;

    public string $name;

    public string $email;

    public int $accountType;

    public bool $allowAdminAccess = false;

    public function mount(User $user): void
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->accountType = $user->account_type->value;
        $this->allowAdminAccess = $user->access_admin;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$this->user->id,
            'accountType' => 'required|'.AccountType::validationRule(),
        ];
    }

    public function save(): mixed
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'account_type' => AccountType::from($this->accountType),
            'access_admin' => $this->allowAdminAccess,
        ]);

        Flux::toast('User updated successfully.');

        return redirect()->route('admin.users.show', $this->user);
    }

    public function getAccountTypeOptions(): array
    {
        return collect(AccountType::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.users.tabs.account-tab');
    }
}

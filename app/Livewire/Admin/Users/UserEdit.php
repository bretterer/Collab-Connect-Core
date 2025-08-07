<?php

namespace App\Livewire\Admin\Users;

use App\Enums\AccountType;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserEdit extends Component
{
    public User $user;
    public string $name;
    public string $email;
    public AccountType $accountType;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->accountType = $user->account_type;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $this->user->id,
            'accountType' => 'required|in:' . implode(',', array_map(fn($case) => $case->value, AccountType::cases())),
        ];
    }

    public function save()
    {
        $this->validate();

        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
            'account_type' => $this->accountType,
        ]);

        session()->flash('message', 'User updated successfully.');

        return redirect()->route('admin.users.show', $this->user);
    }

    public function getAccountTypeOptions()
    {
        return collect(AccountType::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.users.user-edit');
    }
}

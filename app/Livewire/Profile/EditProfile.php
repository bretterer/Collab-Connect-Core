<?php

namespace App\Livewire\Profile;

use App\Livewire\BaseComponent;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')]
class EditProfile extends BaseComponent
{
    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|email|max:255')]
    public $email = '';

    #[Validate('nullable|string')]
    public $current_password = '';

    #[Validate('nullable|string|min:8|confirmed')]
    public $password = '';

    #[Validate('nullable|string')]
    public $password_confirmation = '';

    public function mount()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function resetOnboarding(): void
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        if ($user->isBusinessAccount()) {
            $user->currentBusiness->onboarding_complete = false;
            $user->currentBusiness->save();
        } elseif ($user->isInfluencerAccount()) {
            $user->influencer->onboarding_complete = false;
            $user->influencer->save();
        }

        $this->redirect(route('dashboard'));
    }

    public function updateProfile()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
        ];

        // Add password validation rules if password change is requested
        if (! empty($this->password) || ! empty($this->current_password)) {
            $rules['current_password'] = 'required|string';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $this->validate($rules);

        // If password change is requested, verify current password
        if (! empty($this->password)) {
            if (! Hash::check($this->current_password, $user->password)) {
                $this->addError('current_password', 'The current password is incorrect.');

                return;
            }
        }

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if (! empty($this->password)) {
            $userData['password'] = Hash::make($this->password);
        }

        $user->update($userData);

        $this->flashSuccess('Profile updated successfully!');
        $this->current_password = '';
        $this->password = '';
        $this->password_confirmation = '';
    }

    public function render()
    {
        /** @var User $user */
        $user = $this->getAuthenticatedUser();

        return view('livewire.profile.edit-profile', [
            'user' => $user,
        ]);
    }
}

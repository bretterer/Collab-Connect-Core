<?php

namespace App\Livewire;

use App\Models\User;
use App\Enums\AccountType;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ViewProfile extends Component
{
    public User $user;

    public function mount($username)
    {
        // Find user by business username, influencer username, or ID
        $this->user = User::whereHas('businesses', function ($query) use ($username) {
            $query->where('username', $username);
        })
        ->orWhereHas('influencer', function ($query) use ($username) {
            $query->where('username', $username);
        })
        ->orWhere('id', $username)
        ->with(['businesses', 'influencer'])
        ->firstOrFail();
    }

    public function render()
    {
        // Determine which profile view to show based on account type
        if ($this->user->account_type === AccountType::INFLUENCER) {
            return view('livewire.profiles.influencer-profile', [
                'user' => $this->user
            ]);
        } elseif ($this->user->account_type === AccountType::BUSINESS) {
            return view('livewire.profiles.business-profile', [
                'user' => $this->user
            ]);
        }

        abort(404, 'Profile not found');
    }
}

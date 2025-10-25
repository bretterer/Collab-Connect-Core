<?php

namespace App\Livewire;

use App\Enums\AccountType;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ViewInfluencerProfile extends Component
{
    public User $user;

    public function mount($username)
    {
        // Find user by  influencer username, or ID
        $this->user = User::WhereHas('influencer', function ($query) use ($username) {
            $query->where('username', $username);
        })
            ->orWhere('id', $username)
            ->with(['influencer'])
            ->firstOrFail();
    }

    public function render()
    {
        // Determine which profile view to show based on account type
        if ($this->user->account_type === AccountType::INFLUENCER) {
            return view('livewire.profiles.influencer-profile', [
                'user' => $this->user,
            ]);
        }
        abort(404, 'Profile not found');
    }
}

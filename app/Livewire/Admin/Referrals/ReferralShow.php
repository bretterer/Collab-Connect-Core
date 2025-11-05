<?php

namespace App\Livewire\Admin\Referrals;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ReferralShow extends Component
{
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user->load('referralEnrollment.referrals.referred', 'referralEnrollment.payouts', 'referralEnrollment.percentageHistory.changedBy');
    }

    public function render()
    {
        return view('livewire.admin.referrals.referral-show');
    }
}

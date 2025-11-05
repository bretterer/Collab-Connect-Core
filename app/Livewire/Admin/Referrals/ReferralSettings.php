<?php

namespace App\Livewire\Admin\Referrals;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ReferralSettings extends Component
{
    public function render()
    {
        return view('livewire.admin.referrals.referral-settings');
    }
}

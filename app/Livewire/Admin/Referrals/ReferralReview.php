<?php

namespace App\Livewire\Admin\Referrals;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ReferralReview extends Component
{
    public int $selectedMonth;

    public int $selectedYear;

    public function mount()
    {
        $this->selectedMonth = now()->month;
        $this->selectedYear = now()->year;
    }

    public function render()
    {
        return view('livewire.admin.referrals.referral-review');
    }
}

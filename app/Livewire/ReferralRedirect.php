<?php

namespace App\Livewire;

use App\Models\ReferralEnrollment;
use Livewire\Component;

class ReferralRedirect extends Component
{
    public string $code;

    public function mount(string $code)
    {
        $referralEnrollment = ReferralEnrollment::query()
            ->where('code', $code)
            ->first();

        if ($referralEnrollment->disabled_at !== null) {
            return redirect()->route('home');
        }

        cookie()->queue('referral_code', $referralEnrollment->code, 60 * 24 * 30);

        return redirect()->route('home');
    }
}

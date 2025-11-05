<?php

namespace App\Listeners;

use App\Enums\ReferralStatus;
use App\Events\UserRegisteredWithReferral;
use App\Models\Referral;

class LogUserRegistrationReferral
{
    /**
     * Handle the event.
     */
    public function handle(UserRegisteredWithReferral $event): void
    {
        Referral::create([
            'referrer_user_id' => $event->referralEnrollment->user_id,
            'referred_user_id' => $event->user->id,
            'referral_code_used' => $event->referralEnrollment->code,
            'status' => ReferralStatus::PENDING,
            'base_referral_percentage' => $event->referralEnrollment->defaultPercentage(),
            'promotional_referral_percentage_id' => $event->referralEnrollment->currentPromotionalPercentage()?->id ?? null,
        ]);
    }
}

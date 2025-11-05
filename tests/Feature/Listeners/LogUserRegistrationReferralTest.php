<?php

namespace Tests\Feature\Listeners;

use App\Events\UserRegisteredWithReferral;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LogUserRegistrationReferralTest extends TestCase
{
    #[Test]
    public function records_referral_from_listener(): void
    {
        $referrer = User::factory()->influencer()->withProfile()->subscribed()->create();
        $referralEnrollment = \App\Models\ReferralEnrollment::factory()->create([
            'user_id' => $referrer->id,
        ]);

        $referred = User::factory()->influencer()->create();

        $referral = new UserRegisteredWithReferral($referred, $referralEnrollment);

        $listener = new \App\Listeners\LogUserRegistrationReferral;
        $listener->handle($referral);

        $this->assertDatabaseHas('referrals', [
            'referrer_user_id' => $referrer->id,
            'referred_user_id' => $referred->id,
            'referral_code_used' => $referralEnrollment->code,
            'status' => \App\Enums\ReferralStatus::PENDING,
            'base_referral_percentage' => $referralEnrollment->defaultPercentage(),
            'promotional_referral_percentage_id' => null,
        ]);
    }
}

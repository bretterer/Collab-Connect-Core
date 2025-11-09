<?php

namespace App\Features;

use App\Models\User;

/**
 * @description Allow users to participate in the referral program and earn commissions from referred users.
 */
class ReferralProgram
{
    public string $title = 'Referral Program';

    public string $key = 'referral-program';

    public string $description = 'Allow users to participate in the referral program and earn commissions from referred users.';

    /**
     * Resolve the feature's initial value.
     */
    public function resolve(User $user): mixed
    {
        return false;
    }
}

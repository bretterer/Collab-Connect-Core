<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invite extends Model
{
    public function referralCode(): HasOne
    {
        return $this->hasOne(ReferralCode::class, 'email', 'email');
    }
}

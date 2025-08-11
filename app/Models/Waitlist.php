<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Model;

class Waitlist extends Model
{
    protected $fillable = [
        'name',
        'email',
        'user_type',
        'referral_code',
        'business_name',
        'follower_count',
        'invited_at',
        'invite_token',
        'registered_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'registered_at' => 'datetime',
        'account_type' => AccountType::class,
    ];

    public function referralCode()
    {
        return $this->hasOne(ReferralCode::class, 'email', 'email');
    }
}

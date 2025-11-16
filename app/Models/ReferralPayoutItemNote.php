<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralPayoutItemNote extends Model
{
    protected $fillable = [
        'referral_payout_item_id',
        'user_id',
        'note',
    ];

    public function payoutItem(): BelongsTo
    {
        return $this->belongsTo(ReferralPayoutItem::class, 'referral_payout_item_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

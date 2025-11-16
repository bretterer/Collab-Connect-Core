<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralPayoutItem extends Model
{
    /** @use HasFactory<\Database\Factories\ReferralPayoutItemFactory> */
    use HasFactory;

    protected $fillable = [
        'referral_payout_id',
        'referral_enrollment_id',
        'referral_id',
        'referral_percentage_history_id',
        'subscription_amount',
        'referral_percentage',
        'amount',
        'currency',
        'scheduled_payout_date',
        'status',
        'calculated_at',
    ];

    protected $casts = [
        'subscription_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'scheduled_payout_date' => 'date',
        'calculated_at' => 'datetime',
        'status' => \App\Enums\PayoutStatus::class,
    ];

    public function referralPayout(): BelongsTo
    {
        return $this->belongsTo(ReferralPayout::class);
    }

    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    public function referralPercentageHistory(): BelongsTo
    {
        return $this->belongsTo(ReferralPercentageHistory::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(ReferralEnrollment::class, 'referral_enrollment_id');
    }

    public function notes()
    {
        return $this->hasMany(ReferralPayoutItemNote::class, 'referral_payout_item_id');
    }
}

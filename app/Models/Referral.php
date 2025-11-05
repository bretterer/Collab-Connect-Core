<?php

namespace App\Models;

use App\Enums\ReferralStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Referral extends Model
{
    use HasFactory;

    protected $fillable = [
        'referrer_user_id',
        'referred_user_id',
        'referral_code_used',
        'status',
        'converted_at',
        'churned_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReferralStatus::class,
            'converted_at' => 'datetime',
            'churned_at' => 'datetime',
        ];
    }

    /**
     * Get the user who made the referral.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referrer_user_id');
    }

    /**
     * Get the user who was referred.
     */
    public function referred(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_user_id');
    }

    /**
     * Get the referral enrollment of the referrer.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(ReferralEnrollment::class, 'referrer_user_id', 'user_id');
    }

    /**
     * Check if referral is active.
     */
    public function isActive(): bool
    {
        return $this->status === ReferralStatus::ACTIVE;
    }

    /**
     * Check if referral is pending.
     */
    public function isPending(): bool
    {
        return $this->status === ReferralStatus::PENDING;
    }

    /**
     * Mark referral as converted (became paying customer).
     */
    public function markAsConverted(): void
    {
        $this->update([
            'status' => ReferralStatus::ACTIVE,
            'converted_at' => now(),
        ]);
    }

    /**
     * Mark referral as churned (cancelled subscription).
     */
    public function markAsChurned(): void
    {
        $this->update([
            'status' => ReferralStatus::CHURNED,
            'churned_at' => now(),
        ]);
    }

    /**
     * Scope to only active referrals.
     */
    public function scopeActive($query)
    {
        return $query->where('status', ReferralStatus::ACTIVE);
    }

    /**
     * Scope to only pending referrals.
     */
    public function scopePending($query)
    {
        return $query->where('status', ReferralStatus::PENDING);
    }

    /**
     * Scope to referrals for a specific referrer.
     */
    public function scopeForReferrer($query, $userId)
    {
        return $query->where('referrer_user_id', $userId);
    }
}

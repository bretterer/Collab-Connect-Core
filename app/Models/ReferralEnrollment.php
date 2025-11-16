<?php

namespace App\Models;

use App\Enums\PayoutStatus;
use App\Enums\PercentageChangeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferralEnrollment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'user_id',
        'enrolled_at',
        'disabled_at',
        'paypal_email',
        'paypal_verified',
        'paypal_connected_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'disabled_at' => 'datetime',
            'paypal_verified' => 'boolean',
            'paypal_connected_at' => 'datetime',
        ];
    }

    /**
     * Get the user that owns the referral enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if PayPal account is connected.
     */
    public function hasPayPalConnected(): bool
    {
        return ! empty($this->paypal_email);
    }

    /**
     * Check if PayPal account needs verification.
     */
    public function needsPayPalVerification(): bool
    {
        return ! empty($this->paypal_email) && ! $this->paypal_verified;
    }

    /**
     * Disconnect PayPal account.
     */
    public function disconnectPayPal(): void
    {
        $this->update([
            'paypal_email' => null,
            'paypal_verified' => false,
            'paypal_connected_at' => null,
        ]);
    }

    /**
     * Get all referrals made by this enrolled user.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_user_id', 'user_id');
    }

    /**
     * Get all payouts for this enrollment.
     */
    public function payouts(): HasMany
    {
        return $this->hasMany(ReferralPayout::class);
    }

    /**
     * Get all payout items for this enrollment.
     */
    public function payoutItems(): HasMany
    {
        return $this->hasMany(ReferralPayoutItem::class);
    }

    /**
     * Get percentage change history.
     */
    public function percentageHistory(): HasMany
    {
        return $this->hasMany(ReferralPercentageHistory::class);
    }

    public function defaultPercentage(): int
    {
        $enrollmentPercentage = $this->percentageHistory()
            ->where('change_type', PercentageChangeType::ENROLLMENT)
            ->latest()
            ->first();

        return $enrollmentPercentage ? $enrollmentPercentage->new_percentage : 0;
    }

    public function currentPromotionalPercentage(): ?ReferralPercentageHistory
    {
        $latestChange = $this->percentageHistory()
            ->where('change_type', '!=', PercentageChangeType::ENROLLMENT)
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();

        return $latestChange;
    }

    public function currentReferralPercentage(): int
    {
        $latestChange = $this->percentageHistory()
            ->latest('id')
            ->first();

        return $latestChange ? $latestChange->new_percentage : 0;
    }

    public function promotionalReferralPercentage()
    {
        $latestChange = $this->percentageHistory()
            ->latest('id')
            ->first();

        if ($latestChange) {

            return $latestChange ? $latestChange->new_percentage : null;
        }
    }

    public function promotionalPercentageExpiresAt(): ?\DateTimeInterface
    {
        $latestChange = $this->percentageHistory()
            ->latest('id')
            ->first();

        return match ($latestChange->change_type) {
            PercentageChangeType::TEMPORARY_DATE => $latestChange->expires_at,
            PercentageChangeType::TEMPORARY_MONTHS => now()->addMonths($latestChange->months_remaining ?? 0),
            default => null,
        };
    }

    /**
     * Get count of active referrals.
     */
    public function getActiveReferralCount(): int
    {
        return $this->referrals()->active()->count();
    }

    /**
     * Get pending payout amount for current month.
     * This checks both unprocessed payout items (DRAFT/PENDING) and pending payouts.
     *
     * @return float Total pending payout amount
     */
    public function getPendingPayoutAmount(): float
    {
        // Get unprocessed payout items (items not yet aggregated into a payout)
        $itemsAmount = (float) $this->payoutItems()
            ->whereNull('referral_payout_id') // Not yet associated with a payout
            ->whereIn('status', [PayoutStatus::DRAFT, PayoutStatus::PENDING])
            ->sum('amount');

        // Get pending payouts that have been created but not yet processed
        $payoutsAmount = (float) $this->payouts()
            ->where('status', PayoutStatus::PENDING)
            ->sum('amount');

        return $itemsAmount + $payoutsAmount;
    }

    /**
     * Get pending payout for current month (legacy method - consider using getPendingPayoutAmount).
     * This now only returns pending payouts, not unprocessed items.
     * Use hasPendingPayouts() to check for both items and payouts.
     */
    public function getPendingPayout(): ?ReferralPayout
    {
        return $this->payouts()
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->where('status', PayoutStatus::PENDING)
            ->first();
    }

    /**
     * Check if enrollment has any pending payouts (items or payouts).
     */
    public function hasPendingPayouts(): bool
    {
        $hasUnprocessedItems = $this->payoutItems()
            ->whereNull('referral_payout_id')
            ->whereIn('status', [PayoutStatus::DRAFT, PayoutStatus::PENDING])
            ->exists();

        $hasPendingPayouts = $this->payouts()
            ->where('status', PayoutStatus::PENDING)
            ->exists();

        return $hasUnprocessedItems || $hasPendingPayouts;
    }

    /**
     * Get lifetime earnings.
     */
    public function getLifetimeEarnings(): float
    {
        return (float) $this->payouts()
            ->where('status', PayoutStatus::PAID)
            ->sum('amount');
    }
}

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
        'current_percentage',
        'paypal_email',
        'paypal_payer_id',
        'paypal_verified',
        'paypal_connected_at',
        'paypal_metadata',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'paypal_verified' => 'boolean',
            'paypal_connected_at' => 'datetime',
            'paypal_metadata' => 'array',
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
        return ! empty($this->paypal_email) && $this->paypal_verified;
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
            ->latest()
            ->first();

        return $latestChange;
    }

    public function currentReferralPercentage(): int
    {
        $latestChange = $this->percentageHistory()
            ->orderBy('created_at')
            ->first();

        return $latestChange ? $latestChange->new_percentage : 0;
    }

    public function promotionalReferralPercentage()
    {
        $latestChange = $this->percentageHistory()
            ->orderBy('created_at')
            ->first();

        if ($latestChange) {

            return $latestChange ? $latestChange->new_percentage : null;
        }
    }

    public function promotionalPercentageExpiresAt(): ?\DateTimeInterface
    {
        $latestChange = $this->percentageHistory()
            ->orderBy('created_at')
            ->first();

        return match ($latestChange->change_type) {
            PercentageChangeType::TEMPORARY_DATE => $latestChange->expires_at,
            PercentageChangeType::TEMPORARY_MONTHS => now()->addMonths($latestChange->months ?? 0),
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
     * Get pending payout for current month.
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
     * Get lifetime earnings.
     */
    public function getLifetimeEarnings(): float
    {
        return (float) $this->payouts()
            ->where('status', PayoutStatus::PAID)
            ->sum('amount');
    }
}

<?php

namespace App\Models;

use App\Enums\PayoutStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralPayout extends Model
{
    use HasFactory;

    protected $fillable = [
        'referral_enrollment_id',
        'amount',
        'currency',
        'status',
        'month',
        'year',
        'referral_count',
        'paypal_batch_id',
        'paypal_payout_item_id',
        'paypal_transaction_id',
        'approved_by_user_id',
        'approved_at',
        'processed_at',
        'paid_at',
        'failed_at',
        'failure_reason',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => PayoutStatus::class,
            'amount' => 'decimal:2',
            'month' => 'integer',
            'year' => 'integer',
            'referral_count' => 'integer',
            'approved_at' => 'datetime',
            'processed_at' => 'datetime',
            'paid_at' => 'datetime',
            'failed_at' => 'datetime',
        ];
    }

    /**
     * Get the enrollment this payout belongs to.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(ReferralEnrollment::class, 'referral_enrollment_id');
    }

    /**
     * Get the admin user who approved this payout.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    /**
     * Approve the payout.
     */
    public function approve(User $admin): void
    {
        $this->update([
            'status' => PayoutStatus::APPROVED,
            'approved_by_user_id' => $admin->id,
            'approved_at' => now(),
        ]);
    }

    /**
     * Mark payout as processing.
     */
    public function process(): void
    {
        $this->update([
            'status' => PayoutStatus::PROCESSING,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark payout as paid.
     */
    public function markAsPaid(string $transactionId): void
    {
        $this->update([
            'status' => PayoutStatus::PAID,
            'paypal_transaction_id' => $transactionId,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payout as failed.
     */
    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => PayoutStatus::FAILED,
            'failure_reason' => $reason,
            'failed_at' => now(),
        ]);
    }

    /**
     * Scope to only pending payouts.
     */
    public function scopePending($query)
    {
        return $query->where('status', PayoutStatus::PENDING);
    }

    /**
     * Scope to approved payouts.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', PayoutStatus::APPROVED);
    }

    /**
     * Scope to payouts for a specific month and year.
     */
    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->where('month', $month)->where('year', $year);
    }

    /**
     * Get formatted month/year string.
     */
    public function getMonthYearAttribute(): string
    {
        return now()->month($this->month)->year($this->year)->format('F Y');
    }
}

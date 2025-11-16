<?php

namespace App\Models;

use App\Enums\PercentageChangeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferralPercentageHistory extends Model
{
    use HasFactory;

    protected $table = 'referral_percentage_history';

    protected $fillable = [
        'referral_enrollment_id',
        'old_percentage',
        'new_percentage',
        'change_type',
        'expires_at',
        'months_remaining',
        'reason',
        'changed_by_user_id',
        'processed_at',
    ];

    protected function casts(): array
    {
        return [
            'change_type' => PercentageChangeType::class,
            'old_percentage' => 'integer',
            'new_percentage' => 'integer',
            'expires_at' => 'datetime',
            'months_remaining' => 'integer',
            'processed_at' => 'datetime',
        ];
    }

    /**
     * Get the enrollment this history belongs to.
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(ReferralEnrollment::class, 'referral_enrollment_id');
    }

    /**
     * Get the admin user who made this change.
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }

    /**
     * Check if this change has expired.
     */
    public function isExpired(): bool
    {
        if ($this->change_type === PercentageChangeType::PERMANENT) {
            return false;
        }

        if ($this->change_type === PercentageChangeType::TEMPORARY_DATE) {
            return $this->expires_at && $this->expires_at->isPast();
        }

        if ($this->change_type === PercentageChangeType::TEMPORARY_MONTHS) {
            return $this->months_remaining !== null && $this->months_remaining <= 0;
        }

        return false;
    }

    /**
     * Get remaining months for temporary month-based changes.
     */
    public function getRemainingMonths(): ?int
    {
        if ($this->change_type === PercentageChangeType::TEMPORARY_MONTHS) {
            return max(0, $this->months_remaining ?? 0);
        }

        return null;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
            'paypal_payer_id' => null,
            'paypal_verified' => false,
            'paypal_connected_at' => null,
            'paypal_metadata' => null,
        ]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketWaitlist extends Model
{
    protected $table = 'market_waitlist';

    protected $fillable = [
        'user_id',
        'postal_code',
        'custom_signup_page_id',
        'subscription_stripe_price_id',
        'intended_trial_days',
        'notified_at',
    ];

    protected function casts(): array
    {
        return [
            'notified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customSignupPage(): BelongsTo
    {
        return $this->belongsTo(CustomSignupPage::class);
    }

    /**
     * Mark this waitlist entry as notified
     */
    public function markAsNotified(): void
    {
        $this->update(['notified_at' => now()]);
    }

    /**
     * Scope for unnotified waitlist entries
     */
    public function scopeUnnotified($query)
    {
        return $query->whereNull('notified_at');
    }

    /**
     * Scope for waitlist entries by postal code
     */
    public function scopeByPostalCode($query, string $postalCode)
    {
        return $query->where('postal_code', $postalCode);
    }
}

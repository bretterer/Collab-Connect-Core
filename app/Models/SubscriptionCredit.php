<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravel\Cashier\Subscription;

class SubscriptionCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'key',
        'value',
        'reset_at',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'integer',
            'reset_at' => 'datetime',
        ];
    }

    /**
     * Get the subscription that owns this credit.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Scope to filter by credit key.
     */
    public function scopeForKey(Builder $query, string $key): Builder
    {
        return $query->where('key', $key);
    }

    /**
     * Scope to filter by subscription.
     */
    public function scopeForSubscription(Builder $query, int $subscriptionId): Builder
    {
        return $query->where('subscription_id', $subscriptionId);
    }

    /**
     * Deduct one credit from the value.
     *
     * @return bool True if credit was deducted, false if no credits remaining
     */
    public function deduct(int $amount = 1): bool
    {
        if ($this->value < $amount) {
            return false;
        }

        $this->decrement('value', $amount);

        return true;
    }

    /**
     * Reset the credit to a new value and update reset timestamp.
     */
    public function reset(int $newValue): void
    {
        $this->update([
            'value' => $newValue,
            'reset_at' => now(),
        ]);
    }

    /**
     * Check if credits are available.
     */
    public function hasCredits(): bool
    {
        return $this->value > 0;
    }

    /**
     * Get remaining credits.
     */
    public function remaining(): int
    {
        return max(0, $this->value);
    }
}

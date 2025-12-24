<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddonPriceMapping extends Model
{
    use HasFactory;

    protected $fillable = [
        'stripe_price_id',
        'credit_key',
        'credits_granted',
        'account_type',
        'is_active',
        'sort_order',
        'display_name',
    ];

    protected function casts(): array
    {
        return [
            'credits_granted' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function stripePrice(): BelongsTo
    {
        return $this->belongsTo(StripePrice::class);
    }

    /**
     * Scope to filter by account type (includes 'both').
     */
    public function scopeForAccountType(Builder $query, AccountType $accountType): void
    {
        $query->where(function ($q) use ($accountType) {
            $q->where('account_type', strtolower($accountType->name))
                ->orWhere('account_type', 'both');
        });
    }

    /**
     * Scope to filter by credit key.
     */
    public function scopeForCreditKey(Builder $query, string $creditKey): void
    {
        $query->where('credit_key', $creditKey);
    }

    /**
     * Scope for active mappings only.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Get the display name or fallback to price product name.
     */
    public function getEffectiveDisplayNameAttribute(): string
    {
        return $this->display_name ?? $this->stripePrice?->product_name ?? 'Credit Purchase';
    }
}

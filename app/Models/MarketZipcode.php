<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketZipcode extends Model
{
    protected $fillable = [
        'market_id',
        'postal_code',
    ];

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    /**
     * Get the PostalCode details if available
     */
    public function postalCodeDetails(): BelongsTo
    {
        return $this->belongsTo(PostalCode::class, 'postal_code', 'postal_code');
    }

    /**
     * Check if this postal code is in an active market
     */
    public static function isInActiveMarket(string $postalCode): bool
    {
        return static::where('postal_code', $postalCode)
            ->whereHas('market', fn ($query) => $query->where('is_active', true))
            ->exists();
    }

    /**
     * Get all postal codes for active markets
     */
    public static function activeMarketZipcodes(): array
    {
        return static::whereHas('market', fn ($query) => $query->where('is_active', true))
            ->pluck('postal_code')
            ->unique()
            ->toArray();
    }
}

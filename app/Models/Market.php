<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function zipcodes(): HasMany
    {
        return $this->hasMany(MarketZipcode::class);
    }

    public function waitlist(): HasMany
    {
        return $this->hasMany(MarketWaitlist::class);
    }

    /**
     * Check if a postal code is in this market
     */
    public function hasZipcode(string $postalCode): bool
    {
        return $this->zipcodes()->where('postal_code', $postalCode)->exists();
    }

    /**
     * Get user count for this market
     */
    public function getUserCount(): int
    {
        return User::where('market_approved', true)
            ->whereIn('postal_code', $this->zipcodes()->pluck('postal_code'))
            ->count();
    }

    /**
     * Get waitlist count for this market
     */
    public function getWaitlistCount(): int
    {
        return MarketWaitlist::whereIn('postal_code', $this->zipcodes()->pluck('postal_code'))
            ->count();
    }
}

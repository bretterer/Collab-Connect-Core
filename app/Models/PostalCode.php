<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostalCode extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'country_code',
        'postal_code',
        'place_name',
        'admin_name1',
        'admin_code1',
        'admin_name2',
        'admin_code2',
        'admin_name3',
        'admin_code3',
        'latitude',
        'longitude',
        'accuracy',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'accuracy' => 'integer',
    ];

    /**
     * Scope a query to only include postal codes for a specific country.
     */
    public function scopeForCountry($query, string $countryCode)
    {
        return $query->where('country_code', strtoupper($countryCode));
    }

    /**
     * Scope a query to only include postal codes matching a specific postal code.
     */
    public function scopeForPostalCode($query, string $postalCode)
    {
        return $query->where('postal_code', $postalCode);
    }

    /**
     * Scope a query to only include postal codes in a specific state/province.
     */
    public function scopeInState($query, string $stateCode)
    {
        return $query->where('admin_code1', $stateCode);
    }

    /**
     * Get the full address string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->place_name,
            $this->admin_name2,
            $this->admin_name1,
            $this->country_code,
            $this->postal_code,
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get coordinates as an array.
     */
    public function getCoordinatesAttribute(): ?array
    {
        if ($this->latitude && $this->longitude) {
            return [
                'lat' => (float) $this->latitude,
                'lng' => (float) $this->longitude,
            ];
        }

        return null;
    }

    /**
     * Check if the postal code has coordinates.
     */
    public function hasCoordinates(): bool
    {
        return $this->latitude !== null && $this->longitude !== null;
    }

    /**
     * Get the accuracy description.
     */
    public function getAccuracyDescriptionAttribute(): ?string
    {
        return match ($this->accuracy) {
            1 => 'Estimated',
            4 => 'GeoName ID',
            6 => 'Centroid of addresses or shape',
            default => null,
        };
    }

    /**
     * Calculate distance to another postal code in miles using Haversine formula.
     */
    public function distanceTo(PostalCode $otherPostalCode): ?float
    {
        if (! $this->hasCoordinates() || ! $otherPostalCode->hasCoordinates()) {
            return null;
        }

        return $this->calculateDistance(
            $this->latitude,
            $this->longitude,
            $otherPostalCode->latitude,
            $otherPostalCode->longitude
        );
    }

    /**
     * Calculate distance between two coordinates using Haversine formula.
     */
    public static function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 3959; // Earth's radius in miles

        $lat1Rad = deg2rad($lat1);
        $lon1Rad = deg2rad($lon1);
        $lat2Rad = deg2rad($lat2);
        $lon2Rad = deg2rad($lon2);

        $deltaLat = $lat2Rad - $lat1Rad;
        $deltaLon = $lon2Rad - $lon1Rad;

        $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
             cos($lat1Rad) * cos($lat2Rad) *
             sin($deltaLon / 2) * sin($deltaLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Find postal codes within a certain radius of this postal code.
     */
    public function withinRadius(float $radiusMiles)
    {
        if (! $this->hasCoordinates()) {
            return collect();
        }

        // Use a bounding box for initial filtering (more efficient than calculating distance for every record)
        $latDelta = $radiusMiles / 69; // Approximate miles per degree of latitude
        $lonDelta = $radiusMiles / (69 * cos(deg2rad($this->latitude))); // Adjust for longitude

        $query = $this->newQuery()
            ->where('country_code', $this->country_code)
            ->where('latitude', '>=', $this->latitude - $latDelta)
            ->where('latitude', '<=', $this->latitude + $latDelta)
            ->where('longitude', '>=', $this->longitude - $lonDelta)
            ->where('longitude', '<=', $this->longitude + $lonDelta);

        return $query->get()->filter(function ($postalCode) use ($radiusMiles) {
            $distance = $this->distanceTo($postalCode);

            return $distance !== null && $distance <= $radiusMiles;
        });
    }
}

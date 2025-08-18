<?php

namespace App\Models;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use App\Enums\CompensationType;
use App\Models\PostalCode;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Influencer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'address',
        'city',
        'state',
        'county',
        'postal_code',
        'phone_number',
        'content_types',
        'preferred_business_types',
        'compensation_types',
        'typical_lead_time_days',
        'onboarding_complete',
    ];

    protected function casts(): array
    {
        return [
            'content_types' => 'array',
            'preferred_business_types' => 'array',
            'compensation_types' => 'array',
            'onboarding_complete' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'influencer_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function postalCodeInfo(): BelongsTo
    {
        return $this->belongsTo(PostalCode::class, 'postal_code', 'postal_code');
    }

    public function socials(): HasMany
    {
        return $this->hasMany(InfluencerSocial::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(InfluencerSocial::class);
    }

    public function getTotalFollowersAttribute(): int
    {
        return $this->socialAccounts->sum('followers');
    }

    public function getContentTypesLabelsAttribute(): array
    {
        return collect($this->content_types ?? [])
            ->map(fn ($type) => BusinessIndustry::from($type)->label())
            ->toArray();
    }

    public function getPreferredBusinessTypesLabelsAttribute(): array
    {
        return collect($this->preferred_business_types ?? [])
            ->map(fn ($type) => BusinessType::from($type)->label())
            ->toArray();
    }

    public function getCompensationTypesLabelsAttribute(): array
    {
        return collect($this->compensation_types ?? [])
            ->map(fn ($type) => CompensationType::from($type)->label())
            ->toArray();
    }
}

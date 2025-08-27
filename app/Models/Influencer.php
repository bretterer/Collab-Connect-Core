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
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Influencer extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

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

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('profile_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);

        $this->addMediaCollection('banner_image')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(150)
            ->height(150)
            ->sharpen(10);

        $this->addMediaConversion('profile_preview')
            ->width(400)
            ->height(400)
            ->nonQueued();

        $this->addMediaConversion('banner_preview')
            ->width(1200)
            ->height(400)
            ->nonQueued();
    }

    public function getProfileImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('profile_image', 'profile_preview') ?: null;
    }

    public function getProfileImageThumbUrl(): ?string
    {
        return $this->getFirstMediaUrl('profile_image', 'thumb') ?: null;
    }

    public function getBannerImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('banner_image', 'banner_preview') ?: null;
    }

    public function getBannerImageThumbUrl(): ?string
    {
        return $this->getFirstMediaUrl('banner_image', 'thumb') ?: null;
    }
}

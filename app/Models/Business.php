<?php

namespace App\Models;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Business extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'website',
        'primary_contact',
        'contact_role',
        'maturity',
        'size',
        'type',
        'industry',
        'description',
        'selling_points',
        'city',
        'state',
        'postal_code',
        'target_age_range',
        'target_gender',
        'business_goals',
        'platforms',
    ];

    public function casts(): array
    {
        return [
            'type' => BusinessType::class,
            'industry' => BusinessIndustry::class,
            'target_age_range' => 'array',
            'target_gender' => 'array',
            'business_goals' => 'array',
            'platforms' => 'array',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')
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

        $this->addMediaConversion('preview')
            ->width(400)
            ->height(400)
            ->nonQueued();

        $this->addMediaConversion('banner_preview')
            ->width(1200)
            ->height(400)
            ->nonQueued();
    }

    public function getLogoUrl(): ?string
    {
        return $this->getFirstMediaUrl('logo', 'preview') ?: null;
    }

    public function getLogoThumbUrl(): ?string
    {
        return $this->getFirstMediaUrl('logo', 'thumb') ?: null;
    }

    public function getBannerImageUrl(): ?string
    {
        return $this->getFirstMediaUrl('banner_image', 'banner_preview') ?: null;
    }

    public function getBannerImageThumbUrl(): ?string
    {
        return $this->getFirstMediaUrl('banner_image', 'thumb') ?: null;
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_users')
            ->withPivot('role');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_users')
            ->withPivot('role');
    }

    public function pendingInvites(): HasMany
    {
        return $this->hasMany(BusinessMemberInvite::class)->whereNull('joined_at');
    }

    public function socials(): HasMany
    {
        return $this->hasMany(BusinessSocial::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * Get all chats for this business.
     */
    public function chats(): HasMany
    {
        return $this->hasMany(Chat::class);
    }

    /**
     * Get the owner of the business based on the users where role is `owner`
     */
    public function owner(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'business_users')
            ->withPivot('role')
            ->wherePivot('role', 'owner');
    }
}

<?php

namespace App\Models;

use App\Enums\BusinessIndustry;
use App\Enums\BusinessType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
    }

    public function getLogoUrl(): ?string
    {
        return $this->getFirstMediaUrl('logo', 'preview') ?: null;
    }

    public function getLogoThumbUrl(): ?string
    {
        return $this->getFirstMediaUrl('logo', 'thumb') ?: null;
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, BusinessUser::class);
    }

    public function socials(): HasMany
    {
        return $this->hasMany(BusinessSocial::class);
    }
}

<?php

namespace App\Models;

use Database\Factories\InfluencerProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfluencerProfile extends Model
{
    /** @use HasFactory<InfluencerProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'creator_name',
        'primary_niche',
        'primary_zip_code',
        'media_kit_url',
        'has_media_kit',
        'collaboration_preferences',
        'preferred_brands',
        'subscription_plan',
        'follower_count',
        'onboarding_completed',
    ];

    protected function casts(): array
    {
        return [
            'primary_niche' => \App\Enums\Niche::class,
            'subscription_plan' => \App\Enums\SubscriptionPlan::class,
            'collaboration_preferences' => 'array',
            'preferred_brands' => 'array',
            'has_media_kit' => 'boolean',
            'onboarding_completed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): InfluencerProfileFactory
    {
        return InfluencerProfileFactory::new();
    }
}

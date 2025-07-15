<?php

namespace App\Models;

use Database\Factories\BusinessProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessProfile extends Model
{
    /** @use HasFactory<BusinessProfileFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'industry',
        'websites',
        'primary_zip_code',
        'location_count',
        'is_franchise',
        'is_national_brand',
        'contact_name',
        'contact_email',
        'subscription_plan',
        'collaboration_goals',
        'campaign_types',
        'team_members',
        'onboarding_completed',
    ];

    protected function casts(): array
    {
        return [
            'industry' => \App\Enums\Niche::class,
            'subscription_plan' => \App\Enums\SubscriptionPlan::class,
            'websites' => 'array',
            'collaboration_goals' => 'array',
            'campaign_types' => 'array',
            'team_members' => 'array',
            'is_franchise' => 'boolean',
            'is_national_brand' => 'boolean',
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
    protected static function newFactory(): BusinessProfileFactory
    {
        return BusinessProfileFactory::new();
    }
}

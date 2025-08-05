<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Campaign extends Model
{
    /** @use HasFactory<CampaignFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'campaign_goal',
        'campaign_type',
        'target_zip_code',
        'target_area',
        'campaign_description',
        'influencer_count',
        'application_deadline',
        'campaign_completion_date',
        'publish_action',
        'scheduled_date',
        'current_step',
        'published_at',
        'compensation_type',
        'compensation_amount',
        'compensation_description',
        'compensation_details',
        'brand_overview',
        'current_advertising_campaign',
        'brand_story',
        'campaign_objective',
        'key_insights',
        'fan_motivator',
        'creative_connection',
        'specific_products',
        'posting_restrictions',
        'additional_considerations',
        'target_platforms',
        'deliverables',
        'success_metrics',
        'timing_details',
        'target_audience',
        'content_guidelines',
        'brand_guidelines',
        'main_contact',
        'project_name',
        'social_requirements',
        'placement_requirements',
        'additional_requirements',
    ];

    protected $casts = [
        'influencer_count' => 'integer',
        'application_deadline' => 'date',
        'campaign_completion_date' => 'date',
        'scheduled_date' => 'date',
        'current_step' => 'integer',
        'published_at' => 'datetime',
        'status' => CampaignStatus::class,
        'campaign_type' => \App\Enums\CampaignType::class,
        'compensation_type' => \App\Enums\CompensationType::class,
        'compensation_details' => 'array',
        'social_requirements' => 'array',
        'placement_requirements' => 'array',
        'target_platforms' => 'array',
        'deliverables' => 'array',
        'success_metrics' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(CampaignApplication::class);
    }

    public function brief(): HasOne
    {
        return $this->hasOne(CampaignBrief::class);
    }

    public function brand(): HasOne
    {
        return $this->hasOne(CampaignBrand::class);
    }

    public function requirements(): HasOne
    {
        return $this->hasOne(CampaignRequirements::class);
    }

    public function compensation(): HasOne
    {
        return $this->hasOne(CampaignCompensation::class);
    }

    public function isDraft(): bool
    {
        return $this->status === CampaignStatus::DRAFT;
    }

    public function isPublished(): bool
    {
        return $this->status === CampaignStatus::PUBLISHED;
    }

    public function isScheduled(): bool
    {
        return $this->status === CampaignStatus::SCHEDULED;
    }

    public function isArchived(): bool
    {
        return $this->status === CampaignStatus::ARCHIVED;
    }

    public function scopeDrafts($query)
    {
        return $query->where('status', CampaignStatus::DRAFT);
    }

    public function scopePublished($query)
    {
        return $query->where('status', CampaignStatus::PUBLISHED);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', CampaignStatus::SCHEDULED);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', CampaignStatus::ARCHIVED);
    }

    /**
     * Get compensation display through relationship
     */
    public function getCompensationDisplayAttribute(): string
    {
        return $this->compensation?->compensation_display ?? 'Not set';
    }

    /**
     * Check if campaign has monetary compensation through relationship
     */
    public function isMonetaryCompensation(): bool
    {
        return $this->compensation?->isMonetaryCompensation() ?? false;
    }
}

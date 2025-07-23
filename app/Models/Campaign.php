<?php

namespace App\Models;

use App\Enums\CampaignStatus;
use App\Enums\CompensationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'social_requirements',
        'placement_requirements',
        'compensation_type',
        'compensation_amount',
        'compensation_description',
        'compensation_details',
        'influencer_count',
        'application_deadline',
        'campaign_completion_date',
        'additional_requirements',
        'publish_action',
        'scheduled_date',
        'current_step',
        'published_at',
    ];

    protected $casts = [
        'social_requirements' => 'array',
        'placement_requirements' => 'array',
        'compensation_amount' => 'integer',
        'compensation_details' => 'array',
        'influencer_count' => 'integer',
        'application_deadline' => 'date',
        'campaign_completion_date' => 'date',
        'scheduled_date' => 'date',
        'current_step' => 'integer',
        'published_at' => 'datetime',
        'status' => CampaignStatus::class,
        'compensation_type' => CompensationType::class,
        'campaign_type' => \App\Enums\CampaignType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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

    public function getCompensationDisplayAttribute(): string
    {
        if ($this->compensation_type === CompensationType::MONETARY) {
            return '$' . number_format($this->compensation_amount);
        }

        if ($this->compensation_description) {
            return $this->compensation_description;
        }

        return $this->compensation_type->label();
    }

    public function isMonetaryCompensation(): bool
    {
        return $this->compensation_type === CompensationType::MONETARY;
    }

    // Budget methods removed - use compensation_amount and compensation_type instead
}

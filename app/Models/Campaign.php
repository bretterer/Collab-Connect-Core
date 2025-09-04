<?php

namespace App\Models;

use App\Enums\BusinessIndustry;
use App\Enums\CampaignStatus;
use App\Facades\MatchScore;
use App\Jobs\SendCampaignNotifications;
use App\Models\Influencer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

class Campaign extends Model
{
    /** @use HasFactory<CampaignFactory> */
    use HasFactory;

    protected $fillable = [
        'business_id',
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
        'campaign_type' => 'array',
        'compensation_type' => \App\Enums\CompensationType::class,
        'compensation_details' => 'array',
        'social_requirements' => 'array',
        'placement_requirements' => 'array',
        'target_platforms' => 'array',
        'deliverables' => 'array',
        'success_metrics' => 'array',
        'additional_requirements' => 'array',
    ];

    protected static function booted()
    {
        static::created(function (Campaign $campaign) {
            // Only dispatch notifications for published campaigns
            if ($campaign->status === CampaignStatus::PUBLISHED) {
                SendCampaignNotifications::dispatch($campaign);
            }
        });

        static::updated(function (Campaign $campaign) {
            // Only dispatch notifications for published campaigns
            if ($campaign->status === CampaignStatus::PUBLISHED) {
                SendCampaignNotifications::dispatch($campaign);
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(CampaignApplication::class);
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

    public function isInProgress(): bool
    {
        return $this->status === CampaignStatus::IN_PROGRESS;
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

    public function scopeInProgress($query)
    {
        return $query->where('status', CampaignStatus::IN_PROGRESS);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', CampaignStatus::ARCHIVED);
    }

    /**
     * Get compensation display from direct fields or relationship
     */
    public function getCompensationDisplayAttribute(): string
    {
        // Build display from direct fields
        if ($this->compensation_type && $this->compensation_amount) {
            return match ($this->compensation_type) {
                \App\Enums\CompensationType::MONETARY => '$'.number_format($this->compensation_amount),
                \App\Enums\CompensationType::BARTER => 'Barter (worth $'.number_format($this->compensation_amount).')',
                \App\Enums\CompensationType::FREE_PRODUCT => 'Free Products (worth $'.number_format($this->compensation_amount).')',
                \App\Enums\CompensationType::DISCOUNT => $this->compensation_amount.'% Discount',
                \App\Enums\CompensationType::GIFT_CARD => '$'.number_format($this->compensation_amount).' Gift Card',
                \App\Enums\CompensationType::EXPERIENCE => 'Experience (worth $'.number_format($this->compensation_amount).')',
                \App\Enums\CompensationType::OTHER => 'Custom ($'.number_format($this->compensation_amount).' value)',
                default => 'Not specified'
            };
        }

        return 'Not set';
    }


    /**
     * Find influencers that match this campaign based on threshold score.
     * Uses database pre-filtering to optimize performance.
     */
    public function findMatchingInfluencers(float $threshold = 80): Collection
    {
        $business = $this->business;
        if (! $business) {
            return collect();
        }

        // Get related industries for database pre-filtering
        $relatedIndustries = $this->getRelatedIndustries($business->industry);

        // Build base query with database-level optimizations
        $query = Influencer::query()
            ->with(['user']) // Eager load to avoid N+1 queries
            ->where('onboarding_complete', true)
            ->whereHas('user', function ($userQuery) {
                // Exclude the business owner if they have an influencer profile
                $userQuery->where('id', '!=', $this->business?->users()->first()?->id);
            });

        // Pre-filter by industry if we have campaign business industry
        if ($business->industry && count($relatedIndustries) > 0) {
            $query->whereIn('primary_industry', $relatedIndustries);
        }

        // Pre-filter by location if we have target zip code
        if ($this->target_zip_code) {
            // For exact matches, prioritize them
            $query->where(function ($locationQuery) {
                $locationQuery->where('postal_code', $this->target_zip_code)
                    // Also include nearby zip codes - this is a rough optimization
                    // Could be enhanced with more sophisticated geographical queries
                    ->orWhereNotNull('postal_code');
            });
        }

        // Get candidate influencers and calculate match scores
        $candidates = $query->get();
        $matchingInfluencers = collect();

        foreach ($candidates as $influencer) {
            $matchScore = MatchScore::calculateMatchScore($this, $influencer);

            if ($matchScore >= $threshold) {
                // Add the score to the influencer for potential sorting/debugging
                $influencer->match_score = $matchScore;
                $matchingInfluencers->push($influencer);
            }
        }

        // Sort by match score descending for best matches first
        return $matchingInfluencers->sortByDesc('match_score')->values();
    }

    /**
     * Get related industries for database pre-filtering.
     * This mirrors the logic from MatchScoreService but returns all relevant industries.
     */
    private function getRelatedIndustries(BusinessIndustry $businessIndustry): array
    {
        $industryMapping = [
            BusinessIndustry::FOOD_BEVERAGE->value => [
                BusinessIndustry::FOOD_BEVERAGE,
                BusinessIndustry::FITNESS_WELLNESS,
                BusinessIndustry::TRAVEL_TOURISM
            ],
            BusinessIndustry::FASHION_APPAREL->value => [
                BusinessIndustry::FASHION_APPAREL,
                BusinessIndustry::BEAUTY_COSMETICS,
                BusinessIndustry::FITNESS_WELLNESS
            ],
            BusinessIndustry::BEAUTY_COSMETICS->value => [
                BusinessIndustry::BEAUTY_COSMETICS,
                BusinessIndustry::FASHION_APPAREL,
                BusinessIndustry::FITNESS_WELLNESS
            ],
            BusinessIndustry::FITNESS_WELLNESS->value => [
                BusinessIndustry::FITNESS_WELLNESS,
                BusinessIndustry::HEALTHCARE,
                BusinessIndustry::FOOD_BEVERAGE,
                BusinessIndustry::BEAUTY_COSMETICS,
                BusinessIndustry::FASHION_APPAREL
            ],
            BusinessIndustry::HOME_GARDEN->value => [
                BusinessIndustry::HOME_GARDEN,
                BusinessIndustry::RETAIL,
                BusinessIndustry::BABY_KIDS
            ],
            BusinessIndustry::TRAVEL_TOURISM->value => [
                BusinessIndustry::TRAVEL_TOURISM,
                BusinessIndustry::FOOD_BEVERAGE,
                BusinessIndustry::ENTERTAINMENT
            ],
            BusinessIndustry::RETAIL->value => [
                BusinessIndustry::RETAIL,
                BusinessIndustry::FASHION_APPAREL,
                BusinessIndustry::HOME_GARDEN
            ],
        ];

        // Return the mapped industries or just the exact industry if no mapping exists
        return isset($industryMapping[$businessIndustry->value])
            ? array_map(fn($industry) => $industry->value, $industryMapping[$businessIndustry->value])
            : [$businessIndustry->value];
    }
}

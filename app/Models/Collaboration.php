<?php

namespace App\Models;

use App\Enums\CollaborationStatus;
use App\Enums\ReviewStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Collaboration extends Model
{
    /** @use HasFactory<\Database\Factories\CollaborationFactory> */
    use HasFactory;

    public const REVIEW_PERIOD_DAYS = 15;

    protected $fillable = [
        'campaign_id',
        'campaign_application_id',
        'influencer_id',
        'business_id',
        'status',
        'started_at',
        'completed_at',
        'cancelled_at',
        'deliverables_submitted_at',
        'notes',
        'cancellation_reason',
        'review_status',
        'review_period_starts_at',
        'review_period_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => CollaborationStatus::class,
            'review_status' => ReviewStatus::class,
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'deliverables_submitted_at' => 'datetime',
            'review_period_starts_at' => 'datetime',
            'review_period_ends_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(CampaignApplication::class, 'campaign_application_id');
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'influencer_id');
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function businessReview(): HasOne
    {
        return $this->hasOne(Review::class)->where('reviewer_type', 'business');
    }

    public function influencerReview(): HasOne
    {
        return $this->hasOne(Review::class)->where('reviewer_type', 'influencer');
    }

    public function isActive(): bool
    {
        return $this->status === CollaborationStatus::ACTIVE;
    }

    public function isCompleted(): bool
    {
        return $this->status === CollaborationStatus::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === CollaborationStatus::CANCELLED;
    }

    public function hasSubmittedDeliverables(): bool
    {
        return $this->deliverables_submitted_at !== null;
    }

    public function isReviewPeriodOpen(): bool
    {
        return $this->review_status === ReviewStatus::OPEN;
    }

    public function isReviewPeriodExpired(): bool
    {
        return $this->review_status === ReviewStatus::EXPIRED;
    }

    public function areReviewsVisible(): bool
    {
        return $this->review_status?->areReviewsVisible() ?? false;
    }

    public function canSubmitReview(): bool
    {
        return $this->review_status === ReviewStatus::OPEN;
    }

    public function hasBusinessReview(): bool
    {
        return $this->businessReview()->exists();
    }

    public function hasInfluencerReview(): bool
    {
        return $this->influencerReview()->exists();
    }

    public function hasBothReviews(): bool
    {
        return $this->hasBusinessReview() && $this->hasInfluencerReview();
    }

    public function daysRemainingForReview(): ?int
    {
        if (! $this->review_period_ends_at || ! $this->isReviewPeriodOpen()) {
            return null;
        }

        return max(0, (int) now()->diffInDays($this->review_period_ends_at, false));
    }

    public function scopeActive($query)
    {
        return $query->where('status', CollaborationStatus::ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', CollaborationStatus::COMPLETED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', CollaborationStatus::CANCELLED);
    }

    public function scopeForCampaign($query, int $campaignId)
    {
        return $query->where('campaign_id', $campaignId);
    }

    public function scopeForInfluencer($query, int $influencerId)
    {
        return $query->where('influencer_id', $influencerId);
    }

    public function scopeReviewOpen($query)
    {
        return $query->where('review_status', ReviewStatus::OPEN);
    }

    public function scopeReviewExpired($query)
    {
        return $query->where('review_status', ReviewStatus::EXPIRED);
    }

    public function scopeReviewClosed($query)
    {
        return $query->where('review_status', ReviewStatus::CLOSED);
    }

    public function scopeReviewPeriodEnded($query)
    {
        return $query->where('review_status', ReviewStatus::OPEN)
            ->where('review_period_ends_at', '<=', now());
    }

    public function scopeAwaitingReview($query)
    {
        return $query->where('review_status', ReviewStatus::OPEN);
    }
}

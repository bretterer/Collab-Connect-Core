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

    public function deliverables(): HasMany
    {
        return $this->hasMany(CollaborationDeliverable::class);
    }

    public function activities(): HasMany
    {
        return $this->hasMany(CollaborationActivity::class)->orderByDesc('created_at');
    }

    /**
     * Get the chat associated with this collaboration.
     */
    public function chat(): ?Chat
    {
        if (! $this->business || ! $this->campaign) {
            return null;
        }

        $influencer = $this->influencer?->influencer;
        if (! $influencer) {
            return null;
        }

        return Chat::where('business_id', $this->business_id)
            ->where('campaign_id', $this->campaign_id)
            ->where('influencer_id', $influencer->id)
            ->first();
    }

    /**
     * Check if a user is a participant in this collaboration.
     */
    public function hasParticipant(User $user): bool
    {
        return $this->isInfluencer($user) || $this->isBusinessMember($user);
    }

    /**
     * Check if the given user is the influencer in this collaboration.
     */
    public function isInfluencer(User $user): bool
    {
        return $this->influencer_id === $user->id;
    }

    /**
     * Check if the given user is a member of the business in this collaboration.
     */
    public function isBusinessMember(User $user): bool
    {
        if (! $this->business) {
            return false;
        }

        if ($this->business->relationLoaded('users')) {
            return $this->business->users->contains('id', $user->id);
        }

        return $this->business->users()->where('users.id', $user->id)->exists();
    }

    /**
     * Get the role of a user in this collaboration.
     */
    public function getUserRole(User $user): ?string
    {
        if ($this->isInfluencer($user)) {
            return 'influencer';
        }

        if ($this->isBusinessMember($user)) {
            return 'business';
        }

        return null;
    }

    /**
     * Get the count of approved deliverables.
     */
    public function getApprovedDeliverablesCountAttribute(): int
    {
        return $this->deliverables()->approved()->count();
    }

    /**
     * Get the total count of deliverables.
     */
    public function getTotalDeliverablesCountAttribute(): int
    {
        return $this->deliverables()->count();
    }

    /**
     * Get the progress percentage for this collaboration.
     */
    public function getProgressPercentageAttribute(): int
    {
        $total = $this->total_deliverables_count;
        if ($total === 0) {
            return 0;
        }

        return (int) round(($this->approved_deliverables_count / $total) * 100);
    }

    /**
     * Check if all deliverables are approved.
     */
    public function areAllDeliverablesApproved(): bool
    {
        $total = $this->deliverables()->count();

        return $total > 0 && $this->deliverables()->approved()->count() === $total;
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

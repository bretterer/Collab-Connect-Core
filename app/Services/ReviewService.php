<?php

namespace App\Services;

use App\Enums\ReviewStatus;
use App\Mail\ReviewRequestMail;
use App\Models\Business;
use App\Models\Collaboration;
use App\Models\Review;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReviewService
{
    /**
     * Start the review period for a collaboration.
     * Called when a campaign is completed.
     */
    public function startReviewPeriod(Collaboration $collaboration): Collaboration
    {
        $collaboration->update([
            'review_status' => ReviewStatus::OPEN,
            'review_period_starts_at' => now(),
            'review_period_ends_at' => now()->addDays(Collaboration::REVIEW_PERIOD_DAYS),
        ]);

        // Send review request emails to both parties
        $this->sendReviewRequestEmails($collaboration->fresh());

        return $collaboration->fresh();
    }

    /**
     * Send review request emails to both parties.
     */
    protected function sendReviewRequestEmails(Collaboration $collaboration): void
    {
        $collaboration->load(['campaign', 'influencer', 'business.owner']);

        $businessOwner = $collaboration->business->owner->first();
        $influencer = $collaboration->influencer;

        // Send email to business owner
        Mail::to($businessOwner)->send(new ReviewRequestMail(
            collaboration: $collaboration,
            recipient: $businessOwner,
            otherParty: $influencer,
            recipientRole: 'business',
        ));

        // Send email to influencer
        Mail::to($influencer)->send(new ReviewRequestMail(
            collaboration: $collaboration,
            recipient: $influencer,
            otherParty: $businessOwner,
            recipientRole: 'influencer',
        ));
    }

    /**
     * Submit a review for a collaboration.
     */
    public function submitReview(
        Collaboration $collaboration,
        User $reviewer,
        int $rating,
        ?string $comment = null
    ): Review {
        if (! $collaboration->canSubmitReview()) {
            throw new \RuntimeException('Review period is not open for this collaboration.');
        }

        // Determine reviewer type and reviewee
        $reviewerType = $this->getReviewerType($collaboration, $reviewer);
        $reviewee = $this->getReviewee($collaboration, $reviewerType);

        // Check if reviewer has already submitted
        if ($this->hasAlreadyReviewed($collaboration, $reviewer)) {
            throw new \RuntimeException('You have already submitted a review for this collaboration.');
        }

        return DB::transaction(function () use ($collaboration, $reviewer, $reviewee, $reviewerType, $rating, $comment) {
            $review = Review::create([
                'collaboration_id' => $collaboration->id,
                'reviewer_id' => $reviewer->id,
                'reviewee_id' => $reviewee->id,
                'reviewer_type' => $reviewerType,
                'rating' => $rating,
                'comment' => $comment,
                'submitted_at' => now(),
            ]);

            // Check if both reviews are now submitted
            $this->checkAndCloseReviewPeriod($collaboration->fresh());

            return $review;
        });
    }

    /**
     * Expire review periods that have passed their end date.
     * Called by scheduled command.
     */
    public function expireOverdueReviewPeriods(): int
    {
        $count = 0;

        Collaboration::reviewPeriodEnded()
            ->each(function (Collaboration $collaboration) use (&$count) {
                $collaboration->update([
                    'review_status' => ReviewStatus::EXPIRED,
                ]);
                $count++;
            });

        return $count;
    }

    /**
     * Check if both reviews are submitted and close the review period.
     */
    public function checkAndCloseReviewPeriod(Collaboration $collaboration): void
    {
        if ($collaboration->hasBothReviews() && $collaboration->review_status === ReviewStatus::OPEN) {
            $collaboration->update([
                'review_status' => ReviewStatus::CLOSED,
            ]);
        }
    }

    /**
     * Get reviews for a user (as reviewee).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Review>
     */
    public function getReviewsForUser(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Review::forReviewee($user->id)
            ->submitted()
            ->whereHas('collaboration', function ($query) {
                $query->whereIn('review_status', [ReviewStatus::CLOSED, ReviewStatus::EXPIRED]);
            })
            ->with(['reviewer', 'collaboration.campaign'])
            ->latest('submitted_at')
            ->get();
    }

    /**
     * Get average rating for a user.
     */
    public function getAverageRating(User $user): ?float
    {
        $average = Review::forReviewee($user->id)
            ->submitted()
            ->whereHas('collaboration', function ($query) {
                $query->whereIn('review_status', [ReviewStatus::CLOSED, ReviewStatus::EXPIRED]);
            })
            ->avg('rating');

        return $average ? round($average, 1) : null;
    }

    /**
     * Get review count for a user.
     */
    public function getReviewCount(User $user): int
    {
        return Review::forReviewee($user->id)
            ->submitted()
            ->whereHas('collaboration', function ($query) {
                $query->whereIn('review_status', [ReviewStatus::CLOSED, ReviewStatus::EXPIRED]);
            })
            ->count();
    }

    /**
     * Get reviews for a business entity.
     * These are reviews from influencers about their experience with the business.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Review>
     */
    public function getReviewsForBusiness(Business $business): \Illuminate\Database\Eloquent\Collection
    {
        return Review::fromInfluencer()
            ->submitted()
            ->whereHas('collaboration', function ($query) use ($business) {
                $query->where('business_id', $business->id)
                    ->whereIn('review_status', [ReviewStatus::CLOSED, ReviewStatus::EXPIRED]);
            })
            ->with(['reviewer', 'collaboration.campaign'])
            ->latest('submitted_at')
            ->get();
    }

    /**
     * Get average rating for a business.
     */
    public function getAverageRatingForBusiness(Business $business): ?float
    {
        $average = Review::fromInfluencer()
            ->submitted()
            ->whereHas('collaboration', function ($query) use ($business) {
                $query->where('business_id', $business->id)
                    ->whereIn('review_status', [ReviewStatus::CLOSED, ReviewStatus::EXPIRED]);
            })
            ->avg('rating');

        return $average ? round($average, 1) : null;
    }

    /**
     * Get review count for a business.
     */
    public function getReviewCountForBusiness(Business $business): int
    {
        return Review::fromInfluencer()
            ->submitted()
            ->whereHas('collaboration', function ($query) use ($business) {
                $query->where('business_id', $business->id)
                    ->whereIn('review_status', [ReviewStatus::CLOSED, ReviewStatus::EXPIRED]);
            })
            ->count();
    }

    /**
     * Check if a user can view reviews for a collaboration.
     */
    public function canViewReviews(Collaboration $collaboration, User $user): bool
    {
        // Reviews are visible only when period is closed or expired
        if (! $collaboration->areReviewsVisible()) {
            return false;
        }

        // User must be either the business owner or the influencer
        return $this->isParticipant($collaboration, $user);
    }

    /**
     * Check if user can submit a review for a collaboration.
     */
    public function canSubmitReview(Collaboration $collaboration, User $user): bool
    {
        if (! $collaboration->canSubmitReview()) {
            return false;
        }

        if (! $this->isParticipant($collaboration, $user)) {
            return false;
        }

        return ! $this->hasAlreadyReviewed($collaboration, $user);
    }

    /**
     * Get the review submitted by a specific user for a collaboration.
     */
    public function getReviewByUser(Collaboration $collaboration, User $user): ?Review
    {
        return $collaboration->reviews()
            ->where('reviewer_id', $user->id)
            ->first();
    }

    /**
     * Get collaborations awaiting review for a user.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Collaboration>
     */
    public function getCollaborationsAwaitingReview(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return Collaboration::awaitingReview()
            ->where(function ($query) use ($user) {
                $query->where('influencer_id', $user->id)
                    ->orWhereHas('business', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            })
            ->whereDoesntHave('reviews', function ($query) use ($user) {
                $query->where('reviewer_id', $user->id);
            })
            ->with(['campaign', 'influencer', 'business'])
            ->get();
    }

    /**
     * Determine the reviewer type based on user's relationship to the collaboration.
     */
    protected function getReviewerType(Collaboration $collaboration, User $reviewer): string
    {
        // Check if reviewer is the business owner
        $businessOwner = $collaboration->business->owner->first();
        if ($businessOwner && $businessOwner->id === $reviewer->id) {
            return 'business';
        }

        // Check if reviewer is the influencer
        if ($collaboration->influencer_id === $reviewer->id) {
            return 'influencer';
        }

        throw new \RuntimeException('User is not a participant in this collaboration.');
    }

    /**
     * Get the reviewee (opposite party) based on reviewer type.
     */
    protected function getReviewee(Collaboration $collaboration, string $reviewerType): User
    {
        if ($reviewerType === 'business') {
            return $collaboration->influencer;
        }

        return $collaboration->business->owner->first();
    }

    /**
     * Check if a user is a participant in the collaboration.
     */
    protected function isParticipant(Collaboration $collaboration, User $user): bool
    {
        $businessOwner = $collaboration->business->owner->first();

        return $collaboration->influencer_id === $user->id
            || ($businessOwner && $businessOwner->id === $user->id);
    }

    /**
     * Check if user has already submitted a review.
     */
    protected function hasAlreadyReviewed(Collaboration $collaboration, User $user): bool
    {
        return $collaboration->reviews()
            ->where('reviewer_id', $user->id)
            ->exists();
    }
}

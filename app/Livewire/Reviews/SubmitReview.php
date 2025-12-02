<?php

namespace App\Livewire\Reviews;

use App\Livewire\BaseComponent;
use App\Models\Collaboration;
use App\Services\ReviewService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SubmitReview extends BaseComponent
{
    public Collaboration $collaboration;

    public int $rating = 0;

    public string $comment = '';

    public bool $hasSubmitted = false;

    public function mount(Collaboration $collaboration)
    {
        $this->collaboration = $collaboration->load(['campaign', 'influencer', 'business.owner']);

        // Check if user is a participant
        if (! $this->isParticipant()) {
            abort(403, 'You are not authorized to review this collaboration.');
        }

        // Check if review period is open
        if (! $this->collaboration->canSubmitReview()) {
            abort(403, 'The review period is not open for this collaboration.');
        }

        // Check if user has already submitted
        $reviewService = app(ReviewService::class);
        if ($reviewService->getReviewByUser($this->collaboration, Auth::user())) {
            $this->hasSubmitted = true;
        }
    }

    public function render()
    {
        return view('livewire.reviews.submit-review', [
            'daysRemaining' => $this->collaboration->daysRemainingForReview(),
            'reviewee' => $this->getReviewee(),
        ]);
    }

    public function submitReview()
    {
        $this->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $reviewService = app(ReviewService::class);

        try {
            $reviewService->submitReview(
                $this->collaboration,
                Auth::user(),
                $this->rating,
                $this->comment ?: null
            );

            $this->hasSubmitted = true;

            Flux::toast(
                heading: 'Review Submitted',
                text: 'Your review has been submitted successfully. It will be visible once the other party submits their review or the review period ends.',
                variant: 'success'
            );

            // Check if both reviews are in (reviews are now visible)
            $this->collaboration->refresh();
            if ($this->collaboration->areReviewsVisible()) {
                return $this->redirect(route('collaborations.reviews', $this->collaboration), navigate: true);
            }
        } catch (\RuntimeException $e) {
            Flux::toast(
                heading: 'Error',
                text: $e->getMessage(),
                variant: 'danger'
            );
        }
    }

    public function setRating(int $rating): void
    {
        $this->rating = $rating;
    }

    protected function isParticipant(): bool
    {
        $user = Auth::user();
        $businessOwner = $this->collaboration->business->owner->first();

        return $this->collaboration->influencer_id === $user->id
            || ($businessOwner && $businessOwner->id === $user->id);
    }

    protected function getReviewee()
    {
        $user = Auth::user();
        $businessOwner = $this->collaboration->business->owner->first();

        // If current user is the business owner, reviewee is the influencer
        if ($businessOwner && $businessOwner->id === $user->id) {
            return $this->collaboration->influencer;
        }

        // Otherwise, reviewee is the business owner
        return $businessOwner;
    }
}

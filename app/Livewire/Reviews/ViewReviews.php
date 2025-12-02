<?php

namespace App\Livewire\Reviews;

use App\Livewire\BaseComponent;
use App\Models\Collaboration;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ViewReviews extends BaseComponent
{
    public Collaboration $collaboration;

    public function mount(Collaboration $collaboration)
    {
        $this->collaboration = $collaboration->load([
            'campaign',
            'influencer',
            'business.owner',
            'businessReview.reviewer',
            'influencerReview.reviewer',
        ]);

        // Check if user is a participant
        if (! $this->isParticipant()) {
            abort(403, 'You are not authorized to view these reviews.');
        }

        // Check if reviews are visible
        $reviewService = app(ReviewService::class);
        if (! $reviewService->canViewReviews($this->collaboration, Auth::user())) {
            abort(403, 'Reviews are not yet visible for this collaboration.');
        }
    }

    public function render()
    {
        $businessOwner = $this->collaboration->business->owner->first();

        return view('livewire.reviews.view-reviews', [
            'businessReview' => $this->collaboration->businessReview,
            'influencerReview' => $this->collaboration->influencerReview,
            'isBusinessOwner' => $businessOwner && $businessOwner->id === Auth::id(),
            'businessOwner' => $businessOwner,
        ]);
    }

    protected function isParticipant(): bool
    {
        $user = Auth::user();
        $businessOwner = $this->collaboration->business->owner->first();

        return $this->collaboration->influencer_id === $user->id
            || ($businessOwner && $businessOwner->id === $user->id);
    }
}

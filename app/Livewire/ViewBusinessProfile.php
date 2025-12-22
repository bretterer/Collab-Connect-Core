<?php

namespace App\Livewire;

use App\Enums\AccountType;
use App\Models\User;
use App\Services\ReviewService;
use Combindma\FacebookPixel\Facades\MetaPixel;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ViewBusinessProfile extends Component
{
    public User $user;

    public function mount($username)
    {
        // Find user by business username or business ID
        $this->user = User::whereHas('businesses', function ($query) use ($username) {
            $query->where('businesses.username', $username)
                ->orWhere('businesses.id', $username);
        })
            ->with(['businesses', 'influencer'])
            ->firstOrFail();

        // Track ViewContent for business profile viewing
        MetaPixel::track('ViewContent', [
            'content_type' => 'business_profile',
            'content_ids' => [$this->user->currentBusiness?->id ?? $this->user->id],
            'content_name' => $this->user->currentBusiness?->name ?? $this->user->name,
        ]);
    }

    public function render()
    {
        // Determine which profile view to show based on account type
        if ($this->user->account_type === AccountType::BUSINESS) {
            $reviewService = app(ReviewService::class);
            $business = $this->user->currentBusiness;

            return view('livewire.profiles.business-profile', [
                'user' => $this->user,
                'averageRating' => $business ? $reviewService->getAverageRatingForBusiness($business) : null,
                'reviewCount' => $business ? $reviewService->getReviewCountForBusiness($business) : 0,
                'reviews' => $business ? $reviewService->getReviewsForBusiness($business) : collect(),
            ]);
        }

        abort(404, 'Profile not found');
    }
}

<?php

namespace App\Livewire\Reviews;

use App\Enums\AccountType;
use App\Models\Business;
use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class UserReviews extends Component
{
    public ?User $user = null;

    public ?Business $business = null;

    public string $type = 'influencer';

    public ?float $averageRating = null;

    public int $reviewCount = 0;

    public Collection $reviews;

    public function mount(string $type, string $username)
    {
        $this->type = $type;
        $reviewService = app(ReviewService::class);

        if ($type === 'business') {
            // Find by business username or ID
            $this->business = Business::where('username', $username)
                ->orWhere('id', $username)
                ->firstOrFail();

            $this->averageRating = $reviewService->getAverageRatingForBusiness($this->business);
            $this->reviewCount = $reviewService->getReviewCountForBusiness($this->business);
            $this->reviews = $reviewService->getReviewsForBusiness($this->business);
        } else {
            // Find influencer user by username or ID
            $this->user = User::whereHas('influencer', function ($query) use ($username) {
                $query->where('username', $username);
            })
                ->orWhere('id', $username)
                ->where('account_type', AccountType::INFLUENCER)
                ->with(['influencer'])
                ->firstOrFail();

            $this->averageRating = $reviewService->getAverageRating($this->user);
            $this->reviewCount = $reviewService->getReviewCount($this->user);
            $this->reviews = $reviewService->getReviewsForUser($this->user);
        }
    }

    public function render()
    {
        return view('livewire.reviews.user-reviews');
    }
}

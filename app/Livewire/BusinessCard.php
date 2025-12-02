<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\ReviewService;
use Livewire\Component;

class BusinessCard extends Component
{
    public User $user;

    public bool $isPromoted = false;

    public bool $isVerified = false;

    public bool $showFavorites = true;

    public string $profileImageUrl;

    public string $coverImageUrl;

    public int $randomSeed;

    public ?float $averageRating = null;

    public int $reviewCount = 0;

    public function mount()
    {
        // Generate random seed for consistent images per component instance
        $this->randomSeed = rand(1, 799);
        $this->profileImageUrl = "https://picsum.photos/seed/{$this->randomSeed}/150/150";
        $this->coverImageUrl = 'https://picsum.photos/seed/'.($this->randomSeed + 1).'/400/200';

        // Get real review data for the business
        if ($this->user->currentBusiness) {
            $reviewService = app(ReviewService::class);
            $this->averageRating = $reviewService->getAverageRatingForBusiness($this->user->currentBusiness);
            $this->reviewCount = $reviewService->getReviewCountForBusiness($this->user->currentBusiness);
        }
    }

    public function viewBusinessProfile()
    {
        return $this->redirect(route('business.profile', ['username' => $this->user->currentBusiness?->username ?? $this->user->currentBusiness?->id ?? $this->user->id]), navigate: true);
    }

    public function viewBusinessCampaigns()
    {
        return $this->redirect(route('business.campaigns', ['user' => $this->user->id]), navigate: true);
    }

    public function render()
    {
        return view('livewire.business-card');
    }
}

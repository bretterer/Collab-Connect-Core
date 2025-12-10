<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Auth;
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

    public bool $isSaved = false;

    public bool $isHidden = false;

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

        // Check if current user has saved/hidden this user
        $currentUser = Auth::user();
        if ($currentUser) {
            $this->isSaved = $currentUser->hasSavedUser($this->user);
            $this->isHidden = $currentUser->hasHiddenUser($this->user);
        }
    }

    public function toggleSave(): void
    {
        $currentUser = Auth::user();
        if (! $currentUser) {
            return;
        }

        if ($this->isSaved) {
            $currentUser->unsaveUser($this->user);
            $this->isSaved = false;
        } else {
            // If hidden, unhide first
            if ($this->isHidden) {
                $currentUser->unhideUser($this->user);
                $this->isHidden = false;
            }
            $currentUser->saveUser($this->user);
            $this->isSaved = true;
        }
    }

    public function toggleHide(): void
    {
        $currentUser = Auth::user();
        if (! $currentUser) {
            return;
        }

        if ($this->isHidden) {
            $currentUser->unhideUser($this->user);
            $this->isHidden = false;
        } else {
            // If saved, unsave first
            if ($this->isSaved) {
                $currentUser->unsaveUser($this->user);
                $this->isSaved = false;
            }
            $currentUser->hideUser($this->user);
            $this->isHidden = true;
            $this->dispatch('user-hidden', userId: $this->user->id);
        }
    }

    public function viewBusinessProfile()
    {
        $business = $this->user->currentBusiness;

        if (! $business) {
            return;
        }

        $username = ! empty($business->username) ? $business->username : $business->id;

        return $this->redirect(route('business.profile', ['username' => $username]), navigate: true);
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

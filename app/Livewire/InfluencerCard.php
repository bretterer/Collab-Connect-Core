<?php

namespace App\Livewire;

use App\Models\User;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Vite;
use Livewire\Component;

class InfluencerCard extends Component
{
    public User $user;

    public bool $isPromoted = false;

    public bool $isVerified = false;

    public bool $showFavorites = true;

    public string $profileImageUrl;

    public string $coverImageUrl;

    public ?float $averageRating = null;

    public int $reviewCount = 0;

    public function mount()
    {
        // Use uploaded images from media library, with fallbacks
        $this->profileImageUrl = $this->user->influencer?->getProfileImageUrl()
            ?: Vite::asset('resources/images/CollabConnectMark.png');

        $this->coverImageUrl = $this->user->influencer?->getBannerImageUrl()
            ?: 'data:image/svg+xml;base64,'.base64_encode('
                <svg width="400" height="200" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#0ea5e9;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#0284c7;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grad)"/>
                </svg>');

        // Get real review data
        $reviewService = app(ReviewService::class);
        $this->averageRating = $reviewService->getAverageRating($this->user);
        $this->reviewCount = $reviewService->getReviewCount($this->user);
    }

    public function getUsername(): string
    {
        return $this->user->username();
    }

    public function render()
    {
        return view('livewire.influencer-card', [
            'socialAccounts' => $this->user->profile->socialAccounts,
            'totalFollowers' => $this->user->profile->totalFollowers,
        ]);
    }
}

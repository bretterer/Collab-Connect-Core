<?php

namespace App\Livewire;

use App\Models\User;
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

    public function mount()
    {
        // Use uploaded images from media library, with fallbacks
        $this->profileImageUrl = $this->user->influencer?->getProfileImageUrl() 
            ?: Vite::asset('resources/images/CollabConnectMark.png');
            
        $this->coverImageUrl = $this->user->influencer?->getBannerImageUrl() 
            ?: 'data:image/svg+xml;base64,' . base64_encode('
                <svg width="400" height="200" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#0ea5e9;stop-opacity:1" />
                            <stop offset="100%" style="stop-color:#0284c7;stop-opacity:1" />
                        </linearGradient>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grad)"/>
                </svg>');
    }

    public function getRandomRating()
    {
        // Generate random rating between 3.0 and 5.0
        return round(rand(30, 50) / 10, 1);
    }


    public function render()
    {
        return view('livewire.influencer-card', [
            'socialAccounts' => $this->user->socialMediaAccounts,
            'totalFollowers' => $this->user->socialMediaAccounts->sum('followers')
        ]);
    }
}

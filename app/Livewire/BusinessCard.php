<?php

namespace App\Livewire;

use App\Models\User;
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

    public function mount()
    {
        // Generate random seed for consistent images per component instance
        $this->randomSeed = rand(1, 799);
        $this->profileImageUrl = "https://picsum.photos/seed/{$this->randomSeed}/150/150";
        $this->coverImageUrl = 'https://picsum.photos/seed/'.($this->randomSeed + 1).'/400/200';
    }

    public function getRandomRating()
    {
        // Generate random rating between 3.0 and 5.0
        return round(rand(30, 50) / 10, 1);
    }

    public function viewBusinessProfile()
    {
        return $this->redirect(route('business.profile', $this->user), navigate: true);
    }

    public function viewBusinessCampaigns()
    {
        return $this->redirect(route('business.campaigns', $this->user), navigate: true);
    }

    public function render()
    {
        return view('livewire.business-card');
    }
}

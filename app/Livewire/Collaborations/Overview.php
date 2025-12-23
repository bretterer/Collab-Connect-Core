<?php

namespace App\Livewire\Collaborations;

use App\Livewire\BaseComponent;
use App\Models\Collaboration;
use Illuminate\View\View;

class Overview extends BaseComponent
{
    public Collaboration $collaboration;

    public function mount(Collaboration $collaboration): void
    {
        $this->collaboration = $collaboration->load(['campaign', 'business', 'influencer']);
    }

    public function getOtherPartyProperty()
    {
        $user = $this->getAuthenticatedUser();

        if ($this->collaboration->isInfluencer($user)) {
            return [
                'type' => 'business',
                'name' => $this->collaboration->business->name ?? 'Business',
                'avatar' => $this->collaboration->business->logo_url ?? null,
                'profile_url' => route('business.profile', $this->collaboration->business->username ?? $this->collaboration->business->id),
            ];
        }

        return [
            'type' => 'influencer',
            'name' => $this->collaboration->influencer->name ?? 'Influencer',
            'avatar' => $this->collaboration->influencer->avatar_url ?? null,
            'profile_url' => route('influencer.profile', $this->collaboration->influencer->username ?? $this->collaboration->influencer->id),
        ];
    }

    public function getCampaignProperty()
    {
        return $this->collaboration->campaign;
    }

    public function getIsBusinessProperty(): bool
    {
        return $this->collaboration->isBusinessMember($this->getAuthenticatedUser());
    }

    public function render(): View
    {
        return view('livewire.collaborations.overview');
    }
}

<?php

namespace App\Livewire;

use App\Models\Campaign;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class InviteInfluencerModal extends Component
{
    public bool $showModal = false;
    public ?int $influencerId = null;
    public string $influencerName = '';
    public string $selectedCampaign = '';
    public string $message = '';

    #[Computed]
    public function campaigns()
    {
        $user = auth()->user();

        if (!$user || !$user->currentBusiness) {
            return collect();
        }

        return Campaign::where('business_id', $user->currentBusiness->id)
            ->where('status', 'published')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[Computed]
    public function selectedCampaignData()
    {
        if (!$this->selectedCampaign) {
            return null;
        }

        return Campaign::find($this->selectedCampaign);
    }

    #[On('open-invite-modal')]
    public function openModal($influencerId, $influencerName)
    {
        $this->influencerId = $influencerId;
        $this->influencerName = $influencerName;
        $this->showModal = true;
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->selectedCampaign = '';
        $this->message = '';
    }

    public function sendInvite()
    {
        $this->validate([
            'selectedCampaign' => 'required|exists:campaigns,id',
            'message' => 'required|min:10|max:1000',
        ]);

        // Placeholder for sending invite logic
        $this->dispatch('invite-sent', [
            'influencerId' => $this->influencerId,
            'campaignId' => $this->selectedCampaign,
            'message' => $this->message
        ]);

        session()->flash('message', 'Invitation sent successfully to ' . $this->influencerName);
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.invite-influencer-modal');
    }
}

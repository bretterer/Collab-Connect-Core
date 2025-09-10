<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\User;
use App\Notifications\CampaignInviteNotification;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

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

        $campaign = Campaign::findOrFail($this->selectedCampaign);
        $influencer = User::findOrFail($this->influencerId);
        $invitingUser = auth()->user();

        // Send the invitation notification
        $influencer->notify(new CampaignInviteNotification(
            campaign: $campaign,
            invitingBusiness: $invitingUser,
            personalMessage: $this->message
        ));

        $this->dispatch('invite-sent', [
            'influencerId' => $this->influencerId,
            'campaignId' => $this->selectedCampaign,
            'message' => $this->message
        ]);

        Toaster::success('Invitation sent successfully to ' . $this->influencerName . '! They will receive both an email and in-app notification.');
        $this->closeModal();
    }

    public function render()
    {
        return view('livewire.invite-influencer-modal');
    }
}

<?php

namespace App\Livewire\Campaigns;

use App\Models\Campaign;
use App\Models\Chat;
use App\Models\User;
use App\Services\CampaignService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ShowCampaign extends Component
{
    public Campaign $campaign;

    public bool $isOwner = false;

    public function mount(Campaign $campaign)
    {
        // Load the campaign with all relationships
        $this->campaign = $campaign->load(['brief', 'brand', 'requirements', 'compensation', 'user.businessProfile']);

        // Check if current user is the owner
        $this->isOwner = $this->campaign->user_id === Auth::user()->id;

        // Use the policy to authorize viewing
        $this->authorize('view', $campaign);
    }

    public function render()
    {
        return view('livewire.campaigns.show-campaign');
    }

    public function backToCampaigns()
    {
        return redirect()->route('campaigns.index');
    }

    public function editCampaign()
    {
        $this->authorize('update', $this->campaign);

        return redirect()->route('campaigns.edit', $this->campaign);
    }

    public function unpublishCampaign()
    {
        $this->authorize('unpublish', $this->campaign);
        CampaignService::unpublishCampaign($this->campaign);
        $this->campaign->refresh();
        session()->flash('message', 'Campaign unpublished successfully!');
    }

    public function archiveCampaign()
    {
        $this->authorize('archive', $this->campaign);
        CampaignService::archiveCampaign($this->campaign);
        $this->campaign->refresh();
        session()->flash('message', 'Campaign archived successfully!');
    }

    public function applyToCampaign()
    {
        $this->authorize('apply', $this->campaign);

        // This will be handled by the ApplyToCampaign component
        return redirect()->route('campaigns.show', $this->campaign);
    }

    public function getApplicationsCount()
    {
        if (! $this->isOwner) {
            return 0;
        }

        return $this->campaign->applications()->count();
    }

    public function getPendingApplicationsCount()
    {
        if (! $this->isOwner) {
            return 0;
        }

        return $this->campaign->applications()->where('status', 'pending')->count();
    }

    public function backToDiscover()
    {
        return redirect()->route('discover');
    }

    public function backToDashboard()
    {
        return redirect()->route('dashboard');
    }

    public function openChatWithUser($userId)
    {
        $currentUser = Auth::user();
        $otherUser = User::findOrFail($userId);

        // Determine which user is business and which is influencer
        if ($currentUser->account_type === 'BUSINESS') {
            $businessUser = $currentUser;
            $influencerUser = $otherUser;
        } else {
            $businessUser = $otherUser;
            $influencerUser = $currentUser;
        }

        // Find or create chat between users
        $chat = Chat::findOrCreateBetweenUsers($businessUser, $influencerUser);

        // Redirect to chat with specific chat selected
        return redirect()->route('chat.show', ['chatId' => $chat->id]);
    }
}

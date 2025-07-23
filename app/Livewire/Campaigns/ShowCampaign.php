<?php

namespace App\Livewire\Campaigns;

use App\Models\Campaign;
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
        $this->campaign = $campaign;

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
        // TODO: Implement unpublish logic
        session()->flash('message', 'Campaign unpublished successfully!');
    }

    public function archiveCampaign()
    {
        $this->authorize('archive', $this->campaign);
        // TODO: Implement archive logic
        session()->flash('message', 'Campaign archived successfully!');
    }

    public function applyToCampaign()
    {
        $this->authorize('apply', $this->campaign);
        // TODO: Implement apply logic
        session()->flash('message', 'Application submitted successfully!');
    }

    public function backToDiscover()
    {
        return redirect()->route('discover');
    }

    public function backToDashboard()
    {
        return redirect()->route('dashboard');
    }
}
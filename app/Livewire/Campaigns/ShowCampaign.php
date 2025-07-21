<?php

namespace App\Livewire\Campaigns;

use App\Models\Campaign;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ShowCampaign extends Component
{
    public Campaign $campaign;

    public function mount($campaignId)
    {
        $this->campaign = Campaign::query()
            ->where('id', $campaignId)
            ->where('user_id', Auth::user()->id)
            ->firstOrFail();
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
        return redirect()->route('campaigns.edit', $this->campaign);
    }
}
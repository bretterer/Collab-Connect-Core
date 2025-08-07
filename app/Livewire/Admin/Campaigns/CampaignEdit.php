<?php

namespace App\Livewire\Admin\Campaigns;

use App\Enums\CampaignStatus;
use App\Models\Campaign;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class CampaignEdit extends Component
{
    public Campaign $campaign;
    public string $campaignGoal;
    public ?string $campaignDescription;
    public CampaignStatus $status;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;
        $this->campaignGoal = $campaign->campaign_goal;
        $this->campaignDescription = $campaign->campaign_description;
        $this->status = $campaign->status;
    }

    public function rules()
    {
        return [
            'campaignGoal' => 'required|string|max:500',
            'campaignDescription' => 'nullable|string|max:2000',
            'status' => 'required|in:' . implode(',', array_map(fn($case) => $case->value, CampaignStatus::cases())),
        ];
    }

    public function save()
    {
        $this->validate();

        $this->campaign->update([
            'campaign_goal' => $this->campaignGoal,
            'campaign_description' => $this->campaignDescription,
            'status' => $this->status,
        ]);

        session()->flash('message', 'Campaign updated successfully.');

        return redirect()->route('admin.campaigns.show', $this->campaign);
    }

    public function getStatusOptions()
    {
        return collect(CampaignStatus::cases())
            ->mapWithKeys(fn($case) => [$case->value => $case->label()])
            ->toArray();
    }

    public function render()
    {
        return view('livewire.admin.campaigns.campaign-edit');
    }
}

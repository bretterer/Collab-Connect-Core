<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.dashboard');
    }

    public function getDraftCampaigns()
    {
        if (Auth::user()->account_type === \App\Enums\AccountType::BUSINESS) {
            return Campaign::query()
                ->where('user_id', Auth::user()->id)
                ->where('status', \App\Enums\CampaignStatus::DRAFT)
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        return collect();
    }

    public function getPublishedCampaigns()
    {
        if (Auth::user()->account_type === \App\Enums\AccountType::BUSINESS) {
            return Campaign::query()
                ->where('user_id', Auth::user()->id)
                ->where('status', \App\Enums\CampaignStatus::PUBLISHED)
                ->orderBy('published_at', 'desc')
                ->get();
        }

        return collect();
    }

    public function getScheduledCampaigns()
    {
        if (Auth::user()->account_type === \App\Enums\AccountType::BUSINESS) {
            return Campaign::query()
                ->where('user_id', Auth::user()->id)
                ->where('status', \App\Enums\CampaignStatus::SCHEDULED)
                ->orderBy('scheduled_date', 'asc')
                ->get();
        }

        return collect();
    }
}
<?php

namespace App\Livewire\Applications;

use App\Enums\CampaignApplicationStatus;
use App\Livewire\BaseComponent;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Chat;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ApplicationsIndex extends BaseComponent
{
    use WithPagination;

    public string $statusFilter = 'all';

    public string $campaignFilter = 'all';

    public string $sortBy = 'newest';

    public int $perPage = 15;

    protected $queryString = [
        'statusFilter' => ['except' => 'all'],
        'campaignFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'newest'],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        // Ensure user is a business
        if (Auth::user()->account_type !== \App\Enums\AccountType::BUSINESS) {
            abort(403, 'Access denied.');
        }
    }

    public function getApplicationsProperty()
    {
        $query = CampaignApplication::query()
            ->whereHas('campaign', function ($q) {
                $q->where('business_id', Auth::user()->currentBusiness->id);
            })
            ->with([
                'user.influencer',
                'user.socialMediaAccounts',
                'campaign',
            ]);

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        // Apply campaign filter
        if ($this->campaignFilter !== 'all') {
            $query->where('campaign_id', $this->campaignFilter);
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'newest':
                $query->orderBy('submitted_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('submitted_at', 'asc');
                break;
            case 'followers_high':
                $query->leftJoin('influencer_profiles', 'campaign_applications.user_id', '=', 'influencer_profiles.user_id')
                    ->orderByDesc('influencer_profiles.follower_count');
                break;
            case 'followers_low':
                $query->leftJoin('influencer_profiles', 'campaign_applications.user_id', '=', 'influencer_profiles.user_id')
                    ->orderBy('influencer_profiles.follower_count');
                break;
        }

        return $query->paginate($this->perPage);
    }

    public function getCampaignsProperty()
    {
        return Campaign::where('business_id', Auth::user()->currentBusiness->id)
            ->whereHas('applications')
            ->withCount('applications')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getStatsProperty()
    {
        $baseQuery = CampaignApplication::whereHas('campaign', function ($q) {
            $q->where('business_id', Auth::user()->currentBusiness->id);
        });

        return [
            'total' => $baseQuery->count(),
            'pending' => $baseQuery->where('status', CampaignApplicationStatus::PENDING)->count(),
            'accepted' => $baseQuery->where('status', CampaignApplicationStatus::ACCEPTED)->count(),
            'rejected' => $baseQuery->where('status', CampaignApplicationStatus::REJECTED)->count(),
        ];
    }

    public function acceptApplication($applicationId)
    {
        $application = CampaignApplication::find($applicationId);

        if (! $application || ! $application->campaign || $application->campaign->user_id !== Auth::user()->id) {
            $this->flashError('Application not found or you do not have permission to accept it.');

            return;
        }

        $application->update([
            'status' => CampaignApplicationStatus::ACCEPTED,
            'accepted_at' => now(),
        ]);

        $application->user->notify(new \App\Notifications\CampaignApplicationAcceptedNotification($application->fresh()));

        // Create chat between business and influencer for this campaign
        $influencer = $application->user->influencer;
        if ($influencer) {
            Chat::findOrCreateForCampaign(
                $application->campaign->business,
                $influencer,
                $application->campaign
            );
        }

        $this->flashSuccess('Application accepted successfully!');
    }

    public function declineApplication($applicationId)
    {
        $application = CampaignApplication::find($applicationId);

        if (! $application || ! $application->campaign || $application->campaign->user_id !== Auth::user()->id) {
            $this->flashError('Application not found or you do not have permission to decline it.');

            return;
        }

        $application->update([
            'status' => CampaignApplicationStatus::REJECTED,
            'rejected_at' => now(),
        ]);

        $application->user->notify(new \App\Notifications\CampaignApplicationDeclinedNotification($application->fresh()));
        $this->flashSuccess('Application declined.');
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedCampaignFilter()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.applications.applications-index', [
            'applications' => $this->applications,
            'campaigns' => $this->campaigns,
            'stats' => $this->stats,
        ]);
    }
}

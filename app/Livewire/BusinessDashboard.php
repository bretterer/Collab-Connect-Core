<?php

namespace App\Livewire;

use App\Enums\AccountType;
use App\Enums\CampaignApplicationStatus;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\Chat;
use App\Notifications\CampaignApplicationAcceptedNotification;
use App\Notifications\CampaignApplicationDeclinedNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

#[Layout('layouts.app')]
class BusinessDashboard extends Component
{
    public function mount()
    {
        // Ensure this is only accessible to business users
        if (Auth::user()->account_type !== AccountType::BUSINESS) {
            abort(403, 'Unauthorized access to business dashboard');
        }
    }

    public function render()
    {
        return view('livewire.business-dashboard');
    }

    public function getDraftCampaigns(): Collection
    {
        $business = Auth::user()->currentBusiness;
        if (! $business) {
            return collect();
        }

        return Campaign::query()
            ->where('business_id', $business->id)
            ->where('status', CampaignStatus::DRAFT)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function getPublishedCampaigns(): Collection
    {
        $business = Auth::user()->currentBusiness;
        if (! $business) {
            return collect();
        }

        return Campaign::query()
            ->where('business_id', $business->id)
            ->where('status', CampaignStatus::PUBLISHED)
            ->orderBy('published_at', 'desc')
            ->get();
    }

    public function getScheduledCampaigns(): Collection
    {
        $business = Auth::user()->currentBusiness;
        if (! $business) {
            return collect();
        }

        return Campaign::query()
            ->where('business_id', $business->id)
            ->where('status', CampaignStatus::SCHEDULED)
            ->orderBy('scheduled_date', 'asc')
            ->get();
    }

    /**
     * Get pending applications for business users
     */
    public function getPendingApplications(): Collection
    {
        return \App\Models\CampaignApplication::query()
            ->where('status', CampaignApplicationStatus::PENDING)
            ->whereHas('campaign', function ($query) {
                $query->where('business_id', Auth::user()->currentBusiness->id);
            })
            ->with([
                'campaign',
                'user.socialMediaAccounts',
            ])
            ->orderBy('submitted_at', 'desc')
            ->get();
    }

    /**
     * Get accepted applications for business users
     */
    public function getAcceptedApplications(): Collection
    {
        return \App\Models\CampaignApplication::query()
            ->where('status', CampaignApplicationStatus::ACCEPTED)
            ->whereHas('campaign', function ($query) {
                $query->where('business_id', Auth::user()->currentBusiness->id);
            })
            ->with([
                'campaign',
                'user.socialMediaAccounts',
            ])
            ->orderBy('accepted_at', 'desc')
            ->get();
    }

    /**
     * Get total application count for business stats
     */
    public function getTotalApplicationsCount(): int
    {
        return \App\Models\CampaignApplication::query()
            ->whereHas('campaign', function ($query) {
                $query->where('business_id', Auth::user()->currentBusiness->id);
            })
            ->count();
    }

    /**
     * Accept an application
     */
    public function acceptApplication($applicationId)
    {
        $application = \App\Models\CampaignApplication::find($applicationId);

        if (! $application || ! $application->campaign || $application->campaign->business_id !== Auth::user()->currentBusiness->id) {
            Toaster::error('Application not found or you do not have permission to accept it.');

            return;
        }

        $application->update([
            'status' => CampaignApplicationStatus::ACCEPTED,
            'accepted_at' => now(),
        ]);

        $application->user->notify(new CampaignApplicationAcceptedNotification($application->fresh()));

        // Create chat between business and influencer for this campaign
        $influencer = $application->user->influencer;
        if ($influencer) {
            Chat::findOrCreateForCampaign(
                $application->campaign->business,
                $influencer,
                $application->campaign
            );
        }

        Toaster::success('Application accepted successfully!');
    }

    /**
     * Decline an application
     */
    public function declineApplication($applicationId)
    {
        $application = \App\Models\CampaignApplication::find($applicationId);

        if (! $application || ! $application->campaign || $application->campaign->business_id !== Auth::user()->currentBusiness->id) {
            Toaster::error('Application not found or you do not have permission to decline it.');

            return;
        }

        $application->update([
            'status' => CampaignApplicationStatus::REJECTED,
            'rejected_at' => now(),
        ]);

        $application->user->notify(new CampaignApplicationDeclinedNotification($application->fresh()));
        Toaster::success('Application declined.');
    }
}

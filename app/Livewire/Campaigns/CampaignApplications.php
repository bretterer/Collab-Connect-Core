<?php

namespace App\Livewire\Campaigns;

use App\Enums\SystemMessageType;
use App\Livewire\BaseComponent;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Chat;
use App\Services\ChatService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CampaignApplications extends BaseComponent
{
    use WithPagination;

    public Campaign $campaign;

    public string $statusFilter = 'all';

    public int $perPage = 10;

    public function mount(Campaign $campaign)
    {
        $this->campaign = $campaign;

        // Check if user owns this campaign
        if ($this->campaign->business_id !== Auth::user()->current_business) {
            abort(403, 'You can only view applications for your own campaigns.');
        }
    }

    public function render()
    {
        $applications = $this->getApplications();

        return view('livewire.campaigns.campaign-applications', [
            'applications' => $applications,
        ]);
    }

    public function getApplications()
    {
        $query = $this->campaign->applications()
            ->with(['user.influencer', 'user.socialMediaAccounts'])
            ->orderBy('submitted_at', 'desc');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->paginate($this->perPage);
    }

    public function updateStatus($applicationId, $status)
    {
        $application = CampaignApplication::findOrFail($applicationId);

        // Check if user owns the campaign
        if ($application->campaign->business_id !== Auth::user()->current_business) {
            abort(403, 'You can only update applications for your own campaigns.');
        }

        $application->update([
            'status' => $status,
            'reviewed_at' => now(),
        ]);

        // Create chat when application is accepted
        if ($status === 'accepted') {
            $this->createChatForApplication($application);
        }

        session()->flash('success', 'Application status updated successfully.');
    }

    /**
     * Create a chat for an accepted application.
     */
    protected function createChatForApplication(CampaignApplication $application): void
    {
        $campaign = $application->campaign;
        $influencer = $application->user->influencer;
        $business = $campaign->business;

        // Create or find existing chat
        $chat = Chat::findOrCreateForCampaign($business, $influencer, $campaign);

        // Send welcome system message
        ChatService::sendSystemMessage(
            $chat,
            SystemMessageType::InfluencerAccepted,
            "Welcome! {$influencer->user->name} has been accepted to the \"{$campaign->project_name}\" campaign. You can now start collaborating!"
        );
    }

    public function getStatusBadgeClass($status): string
    {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'accepted' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'contracted' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            'withdrawn' => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };
    }

    public function getStatusLabel($status): string
    {
        return match ($status) {
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'contracted' => 'Contracted',
            'rejected' => 'Rejected',
            'withdrawn' => 'Withdrawn',
            default => 'Unknown',
        };
    }

    public function getAcceptedApplicationsCount(): int
    {
        return $this->campaign->applications()->where('status', 'accepted')->count();
    }

    public function getContractedApplicationsCount(): int
    {
        return $this->campaign->applications()->where('status', 'contracted')->count();
    }

    public function getApplicationsCount(): int
    {
        return $this->campaign->applications()->count();
    }

    public function getPendingApplicationsCount(): int
    {
        return $this->campaign->applications()->where('status', 'pending')->count();
    }
}

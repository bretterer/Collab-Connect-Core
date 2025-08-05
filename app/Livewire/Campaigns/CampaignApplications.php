<?php

namespace App\Livewire\Campaigns;

use App\Livewire\BaseComponent;
use App\Models\Campaign;
use App\Models\CampaignApplication;
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
        if ($this->campaign->user_id !== Auth::user()->id) {
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
            ->with(['user.influencerProfile', 'user.socialMediaAccounts'])
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
        if ($application->campaign->user_id !== Auth::user()->id) {
            abort(403, 'You can only update applications for your own campaigns.');
        }

        $application->update([
            'status' => $status,
            'reviewed_at' => now(),
        ]);

        session()->flash('success', 'Application status updated successfully.');
    }

    public function getStatusBadgeClass($status): string
    {
        return match ($status) {
            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'accepted' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'rejected' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300',
        };
    }

    public function getStatusLabel($status): string
    {
        return match ($status) {
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            default => 'Unknown',
        };
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
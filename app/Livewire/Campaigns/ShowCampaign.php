<?php

namespace App\Livewire\Campaigns;

use App\Enums\AccountType;
use App\Enums\CampaignApplicationStatus;
use App\Models\Campaign;
use App\Models\Chat;
use App\Models\User;
use App\Services\CampaignService;
use App\Services\CollaborationService;
use Combindma\FacebookPixel\Facades\MetaPixel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

#[Layout('layouts.app')]
class ShowCampaign extends Component
{
    public Campaign $campaign;

    public bool $isOwner = false;

    public Collection $applications;

    public function mount(Campaign $campaign)
    {
        // Load the campaign with all relationships
        $this->campaign = $campaign->load([
            'applications.user.influencer',
        ]);

        $this->applications = $this->campaign->applications;

        if (Auth::user()->account_type === AccountType::INFLUENCER) {
            $this->applications = $this->applications->filter(function ($application) {
                return $application->user_id == Auth::user()->id;
            });
        }

        // Check if current user is the owner
        $this->isOwner = $this->campaign->business->members->pluck('id')->contains(Auth::user()->id);

        // Use the policy to authorize viewing
        $this->authorize('view', $campaign);

        // Track ViewContent for campaign viewing
        MetaPixel::track('ViewContent', [
            'content_type' => 'campaign',
            'content_ids' => [$campaign->id],
            'content_name' => $campaign->campaign_goal,
        ]);
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

    public function publishCampaign()
    {
        $this->authorize('publish', $this->campaign);
        CampaignService::publishCampaign($this->campaign);
        $this->campaign->refresh();
        session()->flash('message', 'Campaign published successfully!');
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

    public function startCampaign()
    {
        $this->authorize('update', $this->campaign);

        $acceptedCount = $this->campaign->applications()
            ->where('status', CampaignApplicationStatus::ACCEPTED)
            ->count();

        if ($acceptedCount === 0) {
            Toaster::error('You must accept at least one influencer application before starting this campaign.');

            return;
        }

        CampaignService::startCampaign($this->campaign, Auth::user());
        $this->campaign->refresh();
        Toaster::success('Campaign started! Collaborations have been created for accepted influencers.');
    }

    public function completeCampaign()
    {
        $this->authorize('update', $this->campaign);

        if (! $this->campaign->isInProgress()) {
            Toaster::error('Only in-progress campaigns can be completed.');

            return;
        }

        CampaignService::completeCampaign($this->campaign, Auth::user());
        $this->campaign->refresh();
        Toaster::success('Campaign completed successfully!');
    }

    public function completeCollaboration(int $collaborationId)
    {
        $this->authorize('update', $this->campaign);

        $collaboration = $this->campaign->collaborations()->findOrFail($collaborationId);

        if (! $collaboration->isActive()) {
            Toaster::error('This collaboration is not active.');

            return;
        }

        CollaborationService::complete($collaboration);
        $this->campaign->refresh();
        Toaster::success("Collaboration with {$collaboration->influencer->name} has been completed. Review requests have been sent.");
    }

    public function getAcceptedApplicationsCount()
    {
        if (! $this->isOwner) {
            return 0;
        }

        return $this->campaign->applications()->where('status', CampaignApplicationStatus::ACCEPTED)->count();
    }

    public function getCollaborations()
    {
        if (! $this->isOwner) {
            return collect();
        }

        return $this->campaign->collaborations()->with('influencer.influencer')->get();
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

    public function getSortedApplications(int $limit = 5)
    {
        if (! $this->isOwner) {
            return collect();
        }

        $accepted = CampaignApplicationStatus::ACCEPTED->value;
        $pending = CampaignApplicationStatus::PENDING->value;
        $contracted = CampaignApplicationStatus::CONTRACTED->value;

        // Sort: accepted first, then pending, then others. Within each group, sort by submitted_at desc
        return $this->campaign->applications()
            ->with(['user.influencer'])
            ->orderByRaw('CASE
                WHEN status = ? THEN 1
                WHEN status = ? THEN 2
                WHEN status = ? THEN 3
                ELSE 4
            END', [$accepted, $pending, $contracted])
            ->orderBy('submitted_at', 'desc')
            ->take($limit)
            ->get();
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
        if ($currentUser->account_type === AccountType::BUSINESS) {
            $influencerUser = $otherUser;

            // Verify the influencer has an application for this campaign
            $hasRelationship = $this->campaign->applications()
                ->where('user_id', $influencerUser->id)
                ->exists();

            if (! $hasRelationship) {
                abort(403, 'You cannot message this user.');
            }
        } else {
            $influencerUser = $currentUser;

            // Verify the current user (influencer) has applied to this campaign
            $hasApplication = $this->campaign->applications()
                ->where('user_id', $influencerUser->id)
                ->exists();

            if (! $hasApplication) {
                abort(403, 'You cannot message this user.');
            }
        }

        // Get the influencer profile
        $influencerProfile = $influencerUser->influencer;
        if (! $influencerProfile) {
            abort(403, 'Influencer profile not found.');
        }

        // Find or create chat for this campaign
        $chat = Chat::findOrCreateForCampaign(
            $this->campaign->business,
            $influencerProfile,
            $this->campaign
        );

        // Redirect to chat with specific chat selected
        return redirect()->route('chat.show', ['chatId' => $chat->id]);
    }
}

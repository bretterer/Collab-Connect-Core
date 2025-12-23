<?php

namespace App\Policies;

use App\Enums\AccountType;
use App\Enums\CampaignApplicationStatus;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignPolicy
{
    use HandlesAuthorization;

    /**
     * Check if the user is a member of the campaign's business.
     * Members include all users with access to the business (owners and team members).
     */
    private function isBusinessMember(User $user, Campaign $campaign): bool
    {
        return $campaign->business->members->contains('id', $user->id);
    }

    /**
     * Determine whether the user can view any campaigns.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view the campaign list/discovery
    }

    /**
     * Determine whether the user can view the campaign.
     */
    public function view(User $user, Campaign $campaign): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Published campaigns are viewable by anyone
        if ($campaign->status === CampaignStatus::PUBLISHED) {
            return true;
        }

        // Allow viewing campaign if influencer is the accepted applicant
        if ($user->account_type === AccountType::INFLUENCER) {
            $application = $campaign->applications()
                ->where('user_id', $user->id)
                ->where('status', CampaignApplicationStatus::ACCEPTED)
                ->orWhere('status', CampaignApplicationStatus::CONTRACTED)
                ->first();
            if ($application) {
                return true;
            }
        }

        // Unpublished campaigns only viewable by business members
        // Return 404 to hide existence from non-members
        if (! $this->isBusinessMember($user, $campaign)) {
            abort(404);
        }

        return true;
    }

    /**
     * Determine whether the user can create campaigns.
     */
    public function create(User $user): bool
    {
        return $user->account_type === AccountType::BUSINESS;
    }

    /**
     * Determine whether the user can update the campaign.
     */
    public function update(User $user, Campaign $campaign): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->isBusinessMember($user, $campaign);
    }

    /**
     * Determine whether the user can delete the campaign.
     */
    public function delete(User $user, Campaign $campaign): bool
    {
        return $this->isBusinessMember($user, $campaign);
    }

    /**
     * Determine whether the user can restore the campaign.
     */
    public function restore(User $user, Campaign $campaign): bool
    {
        return $this->isBusinessMember($user, $campaign);
    }

    /**
     * Determine whether the user can permanently delete the campaign.
     */
    public function forceDelete(User $user, Campaign $campaign): bool
    {
        return $this->isBusinessMember($user, $campaign);
    }

    /**
     * Determine whether the user can publish the campaign.
     */
    public function publish(User $user, Campaign $campaign): bool
    {
        return $this->isBusinessMember($user, $campaign);
    }

    /**
     * Determine whether the user can unpublish the campaign.
     */
    public function unpublish(User $user, Campaign $campaign): bool
    {
        return $this->isBusinessMember($user, $campaign);
    }

    /**
     * Determine whether the user can archive the campaign.
     */
    public function archive(User $user, Campaign $campaign): bool
    {
        return $this->isBusinessMember($user, $campaign);
    }

    /**
     * Determine whether the user can apply to the campaign.
     */
    public function apply(User $user, Campaign $campaign): bool
    {
        return $user->account_type === AccountType::INFLUENCER &&
               $campaign->status === CampaignStatus::PUBLISHED;
    }
}

<?php

namespace App\Policies;

use App\Enums\AccountType;
use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class CampaignPolicy
{
    use HandlesAuthorization;

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
        if ($campaign->status !== CampaignStatus::PUBLISHED && $campaign->user_id !== $user->id) {
            abort(404);
        }

        // Allow viewing if:
        // 1. Campaign is published (anyone can view)
        // 2. User is the owner (can view unpublished campaigns)
        return $campaign->status === CampaignStatus::PUBLISHED ||
               $campaign->user_id === $user->id;
    }

    /**
     * Determine whether the user can create campaigns.
     */
    public function create(User $user): bool
    {
        // Only business users can create campaigns
        return $user->account_type === AccountType::BUSINESS;
    }

    /**
     * Determine whether the user can update the campaign.
     */
    public function update(User $user, Campaign $campaign): bool
    {
        // Only the campaign owner can update
        return $campaign->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the campaign.
     */
    public function delete(User $user, Campaign $campaign): bool
    {
        // Only the campaign owner can delete
        return $campaign->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the campaign.
     */
    public function restore(User $user, Campaign $campaign): bool
    {
        // Only the campaign owner can restore
        return $campaign->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the campaign.
     */
    public function forceDelete(User $user, Campaign $campaign): bool
    {
        // Only the campaign owner can permanently delete
        return $campaign->user_id === $user->id;
    }

    /**
     * Determine whether the user can publish the campaign.
     */
    public function publish(User $user, Campaign $campaign): bool
    {
        // Only the campaign owner can publish
        return $campaign->user_id === $user->id;
    }

    /**
     * Determine whether the user can unpublish the campaign.
     */
    public function unpublish(User $user, Campaign $campaign): bool
    {
        // Only the campaign owner can unpublish
        return $campaign->user_id === $user->id;
    }

    /**
     * Determine whether the user can archive the campaign.
     */
    public function archive(User $user, Campaign $campaign): bool
    {
        // Only the campaign owner can archive
        return $campaign->user_id === $user->id;
    }

    /**
     * Determine whether the user can apply to the campaign.
     */
    public function apply(User $user, Campaign $campaign): bool
    {
        // Only influencers can apply, and only to published campaigns
        return $user->account_type === AccountType::INFLUENCER &&
               $campaign->status === CampaignStatus::PUBLISHED;
    }
}

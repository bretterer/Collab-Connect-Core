<?php

namespace App\Services;

use App\Enums\CampaignStatus;
use App\Enums\CompensationType;
use App\Events\CampaignArchived;
use App\Events\CampaignEdited;
use App\Events\CampaignPublished;
use App\Events\CampaignScheduled;
use App\Events\CampaignUnpublished;
use App\Models\Campaign;
use App\Models\User;

class CampaignService
{
    /**
     * Save or update a campaign draft
     */
    public static function saveDraft(User $user, array $data): Campaign
    {
        $campaign = Campaign::updateOrCreate(
            [
                'user_id' => $user->id,
                'id' => $data['campaign_id'] ?? null,
            ],
            [
                'status' => CampaignStatus::DRAFT,
                'campaign_goal' => $data['campaign_goal'] ?? '',
                'campaign_type' => $data['campaign_type'] ?? '',
                'target_zip_code' => $data['target_zip_code'] ?? '',
                'target_area' => $data['target_area'] ?? '',
                'campaign_description' => $data['campaign_description'] ?? '',
                'social_requirements' => $data['social_requirements'] ?? [],
                'placement_requirements' => $data['placement_requirements'] ?? [],
                'compensation_type' => $data['compensation_type'] ?? CompensationType::MONETARY,
                'compensation_amount' => $data['compensation_amount'] ?? 0,
                'compensation_description' => $data['compensation_description'] ?? null,
                'compensation_details' => $data['compensation_details'] ?? null,
                'influencer_count' => $data['influencer_count'] ?? 1,
                'application_deadline' => $data['application_deadline'] ?? null,
                'campaign_completion_date' => $data['campaign_completion_date'] ?? null,
                'additional_requirements' => $data['additional_requirements'] ?? '',
                'publish_action' => $data['publish_action'] ?? 'publish',
                'scheduled_date' => $data['scheduled_date'] ?? null,
                'current_step' => $data['current_step'] ?? 1,
            ]
        );

        return $campaign;
    }

    /**
     * Publish a campaign
     */
    public static function publishCampaign(Campaign $campaign): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        // Fire the CampaignPublished event
        event(new CampaignPublished($campaign, $campaign->user));

        return $campaign;
    }

    /**
     * Schedule a campaign
     */
    public static function scheduleCampaign(Campaign $campaign, string $scheduledDate): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::SCHEDULED,
            'scheduled_date' => $scheduledDate,
        ]);

        // Fire the CampaignScheduled event
        event(new CampaignScheduled($campaign, $campaign->user, $scheduledDate));

        return $campaign;
    }

    /**
     * Archive a campaign
     */
    public static function archiveCampaign(Campaign $campaign): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::ARCHIVED,
        ]);

        // Fire the CampaignArchived event
        event(new CampaignArchived($campaign, $campaign->user));

        return $campaign;
    }

    /**
     * Unschedule a campaign and convert it back to draft
     */
    public static function unscheduleCampaign(Campaign $campaign): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::DRAFT,
            'scheduled_date' => null,
        ]);

        // Fire the CampaignUnpublished event
        event(new CampaignUnpublished($campaign, $campaign->user));

        return $campaign;
    }

    /**
     * Update a campaign and fire edit event
     */
    public static function updateCampaign(int $campaignId, array $data): Campaign
    {
        $campaign = Campaign::findOrFail($campaignId);

        // Store original values to track changes
        $originalData = $campaign->toArray();

        $campaign->update($data);

        // Determine what changed
        $changes = array_diff_assoc($campaign->fresh()->toArray(), $originalData);

        // Fire the CampaignEdited event
        event(new CampaignEdited($campaign, $campaign->user, $changes));

        return $campaign;
    }

    /**
     * Get user's draft campaigns
     */
    public static function getUserDrafts(User $user)
    {
        return $user->campaigns()->drafts()->orderBy('updated_at', 'desc')->get();
    }

    /**
     * Get user's published campaigns
     */
    public static function getUserPublished(User $user)
    {
        return $user->campaigns()->published()->orderBy('published_at', 'desc')->get();
    }

    /**
     * Get user's scheduled campaigns
     */
    public static function getUserScheduled(User $user)
    {
        return $user->campaigns()->scheduled()->orderBy('scheduled_date', 'asc')->get();
    }

    /**
     * Get user's archived campaigns
     */
    public static function getUserArchived(User $user)
    {
        return $user->campaigns()->archived()->orderBy('updated_at', 'desc')->get();
    }
}
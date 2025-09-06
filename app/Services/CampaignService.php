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
     * Save or update a campaign draft with all fields
     */
    public static function saveDraft(User $user, array $data): Campaign
    {
        // Create or update the main campaign with all fields
        $campaign = Campaign::updateOrCreate(
            [
                'business_id' => $user->currentBusiness->id,
                'id' => $data['campaign_id'] ?? null,
            ],
            [
                // Basic campaign fields
                'status' => CampaignStatus::DRAFT,
                'campaign_goal' => $data['campaign_goal'] ?? '',
                'campaign_type' => ! empty($data['campaign_type']) ? $data['campaign_type'] : null,
                'target_zip_code' => $data['target_zip_code'] ?? '',
                'target_area' => $data['target_area'] ?? '',
                'campaign_description' => $data['campaign_description'] ?? '',
                'influencer_count' => $data['influencer_count'] ?? 1,
                'application_deadline' => !empty($data['application_deadline']) ? $data['application_deadline'] : null,
                'campaign_completion_date' => !empty($data['campaign_completion_date']) ? $data['campaign_completion_date'] : null,
                'publish_action' => $data['publish_action'] ?? 'publish',
                'scheduled_date' => !empty($data['scheduled_date']) ? $data['scheduled_date'] : null,
                'current_step' => $data['current_step'] ?? 1,

                // Brief fields
                'project_name' => $data['project_name'] ?? null,
                'main_contact' => $data['main_contact'] ?? null,
                'campaign_objective' => $data['campaign_objective'] ?? null,
                'key_insights' => $data['key_insights'] ?? null,
                'fan_motivator' => $data['fan_motivator'] ?? null,
                'creative_connection' => $data['creative_connection'] ?? null,
                'target_audience' => $data['target_audience'] ?? null,
                'timing_details' => $data['timing_details'] ?? null,
                'additional_requirements' => $data['additional_requirements'] ?? null,

                // Brand fields
                'brand_overview' => $data['brand_overview'] ?? null,
                'current_advertising_campaign' => $data['current_advertising_campaign'] ?? null,
                'brand_story' => $data['brand_story'] ?? null,
                'brand_guidelines' => $data['brand_guidelines'] ?? null,

                // Requirements fields
                'social_requirements' => $data['social_requirements'] ?? null,
                'placement_requirements' => $data['placement_requirements'] ?? null,
                'target_platforms' => $data['target_platforms'] ?? null,
                'deliverables' => $data['deliverables'] ?? null,
                'success_metrics' => $data['success_metrics'] ?? null,
                'content_guidelines' => $data['content_guidelines'] ?? null,
                'posting_restrictions' => $data['posting_restrictions'] ?? null,
                'specific_products' => $data['specific_products'] ?? null,
                'additional_considerations' => $data['additional_considerations'] ?? null,

                // Compensation fields
                'compensation_type' => $data['compensation_type'] ?? \App\Enums\CompensationType::MONETARY,
                'compensation_amount' => $data['compensation_amount'] ?? 0,
                'compensation_description' => $data['compensation_description'] ?? null,
                'compensation_details' => $data['compensation_details'] ?? null,
            ]
        );

        return $campaign;
    }


    /**
     * Publish a campaign
     */
    public static function publishCampaign(Campaign $campaign, ?User $publisher = null): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        // Fire the CampaignPublished event
        if ($publisher) {
            event(new CampaignPublished($campaign, $publisher));
        }

        return $campaign;
    }

    /**
     * Schedule a campaign
     */
    public static function scheduleCampaign(Campaign $campaign, string $scheduledDate, ?User $scheduler = null): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::SCHEDULED,
            'scheduled_date' => $scheduledDate,
        ]);

        // Fire the CampaignScheduled event
        if ($scheduler) {
            event(new CampaignScheduled($campaign, $scheduler, $scheduledDate));
        }

        return $campaign;
    }

    /**
     * Archive a campaign
     */
    public static function archiveCampaign(Campaign $campaign, ?User $archiver = null): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::ARCHIVED,
        ]);

        // Fire the CampaignArchived event
        if ($archiver) {
            event(new CampaignArchived($campaign, $archiver));
        }

        return $campaign;
    }

    public static function startCampaign(Campaign $campaign): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::IN_PROGRESS,
        ]);

        return $campaign;
    }

    public static function unpublishCampaign(Campaign $campaign, ?User $unpublisher = null): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::DRAFT,
            'published_at' => null,
        ]);

        // Fire the CampaignUnpublished event
        if ($unpublisher) {
            event(new CampaignUnpublished($campaign, $unpublisher));
        }

        return $campaign;
    }

    /**
     * Unschedule a campaign and convert it back to draft
     */
    public static function unscheduleCampaign(Campaign $campaign, ?User $unscheduler = null): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::DRAFT,
            'scheduled_date' => null,
        ]);

        // Fire the CampaignUnpublished event
        if ($unscheduler) {
            event(new CampaignUnpublished($campaign, $unscheduler));
        }

        return $campaign;
    }

    /**
     * Update a campaign and fire edit event
     */
    public static function updateCampaign(int $campaignId, array $data, ?User $editor = null): Campaign
    {
        $campaign = Campaign::findOrFail($campaignId);

        // Store original values to track changes
        $originalData = $campaign->toArray();

        // Handle array fields to prevent conversion errors
        $updateData = $data;
        $arrayFields = ['target_platforms', 'deliverables', 'success_metrics', 'social_requirements', 'placement_requirements', 'compensation_details', 'additional_requirements'];

        foreach ($arrayFields as $field) {
            if (isset($updateData[$field])) {
                $updateData[$field] = ! empty($updateData[$field]) ? $updateData[$field] : [];
            }
        }

        $campaign->update($updateData);

        // Determine what changed
        $changes = self::arrayRecursiveDiff($campaign->fresh()->toArray(), $originalData);

        // Fire the CampaignEdited event
        if ($editor) {
            event(new CampaignEdited($campaign, $editor, $changes));
        }

        return $campaign;
    }

    /**
     * Get user's draft campaigns
     */
    public static function getUserDrafts(User $user)
    {
        return $user->currentBusiness->campaigns()->drafts()->orderBy('updated_at', 'desc')->get();
    }

    /**
     * Get user's published campaigns
     */
    public static function getUserPublished(User $user)
    {
        return $user->currentBusiness->campaigns()->published()->orderBy('published_at', 'desc')->get();
    }

    /**
     * Get user's scheduled campaigns
     */
    public static function getUserScheduled(User $user)
    {
        return $user->currentBusiness->campaigns()->scheduled()->orderBy('scheduled_date', 'asc')->get();
    }

    public static function getUserInProgress(User $user)
    {
        return $user->currentBusiness->campaigns()->inProgress()->orderBy('updated_at', 'desc')->get();
    }

    /**
     * Get user's archived campaigns
     */
    public static function getUserArchived(User $user)
    {
        return $user->currentBusiness->campaigns()->archived()->orderBy('updated_at', 'desc')->get();
    }

    private static function arrayRecursiveDiff($array1, $array2)
    {
        $difference = [];
        foreach ($array1 as $key => $value) {
            if (is_array($value)) {
                if (! isset($array2[$key]) || ! is_array($array2[$key])) {
                    $difference[$key] = $value;
                } else {
                    $recursiveDiff = self::arrayRecursiveDiff($value, $array2[$key]);
                    if ($recursiveDiff) {
                        $difference[$key] = $recursiveDiff;
                    }
                }
            } else {
                if (! isset($array2[$key]) || $array2[$key] !== $value) {
                    $difference[$key] = $value;
                }
            }
        }

        return $difference;
    }
}

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
     * Save or update a campaign draft with normalized structure
     */
    public static function saveDraft(User $user, array $data): Campaign
    {
        // Create or update the main campaign
        $campaign = Campaign::updateOrCreate(
            [
                'user_id' => $user->id,
                'id' => $data['campaign_id'] ?? null,
            ],
            [
                'status' => CampaignStatus::DRAFT,
                'campaign_goal' => $data['campaign_goal'] ?? '',
                'campaign_type' => ! empty($data['campaign_type']) ? $data['campaign_type'] : null,
                'target_zip_code' => $data['target_zip_code'] ?? '',
                'target_area' => $data['target_area'] ?? '',
                'campaign_description' => $data['campaign_description'] ?? '',
                'influencer_count' => $data['influencer_count'] ?? 1,
                'application_deadline' => $data['application_deadline'] ?? null,
                'campaign_completion_date' => $data['campaign_completion_date'] ?? null,
                'publish_action' => $data['publish_action'] ?? 'publish',
                'scheduled_date' => $data['scheduled_date'] ?? null,
                'current_step' => $data['current_step'] ?? 1,
            ]
        );

        // Create or update related models
        self::saveCampaignBrief($campaign, $data);
        self::saveCampaignBrand($campaign, $data);
        self::saveCampaignRequirements($campaign, $data);
        self::saveCampaignCompensation($campaign, $data);

        return $campaign;
    }

    /**
     * Save campaign brief information
     */
    private static function saveCampaignBrief(Campaign $campaign, array $data): void
    {
        $briefData = [
            'project_name' => $data['project_name'] ?? null,
            'main_contact' => $data['main_contact'] ?? null,
            'campaign_objective' => $data['campaign_objective'] ?? null,
            'key_insights' => $data['key_insights'] ?? null,
            'fan_motivator' => $data['fan_motivator'] ?? null,
            'creative_connection' => $data['creative_connection'] ?? null,
            'target_audience' => $data['target_audience'] ?? null,
            'timing_details' => $data['timing_details'] ?? null,
            'additional_requirements' => $data['additional_requirements'] ?? null,
        ];

        $campaign->brief()->updateOrCreate(['campaign_id' => $campaign->id], $briefData);
    }

    /**
     * Save campaign brand information
     */
    private static function saveCampaignBrand(Campaign $campaign, array $data): void
    {
        $brandData = [
            'brand_overview' => $data['brand_overview'] ?? null,
            'brand_essence' => $data['brand_essence'] ?? null,
            'brand_pillars' => $data['brand_pillars'] ?? null,
            'current_advertising_campaign' => $data['current_advertising_campaign'] ?? null,
            'brand_story' => $data['brand_story'] ?? null,
            'brand_guidelines' => $data['brand_guidelines'] ?? null,
        ];

        $campaign->brand()->updateOrCreate(['campaign_id' => $campaign->id], $brandData);
    }

    /**
     * Save campaign requirements information
     */
    private static function saveCampaignRequirements(Campaign $campaign, array $data): void
    {
        $requirementsData = [
            'social_requirements' => $data['social_requirements'] ?? null,
            'placement_requirements' => $data['placement_requirements'] ?? null,
            'target_platforms' => $data['target_platforms'] ?? null,
            'deliverables' => $data['deliverables'] ?? null,
            'success_metrics' => $data['success_metrics'] ?? null,
            'content_guidelines' => $data['content_guidelines'] ?? null,
            'posting_restrictions' => $data['posting_restrictions'] ?? null,
            'specific_products' => $data['specific_products'] ?? null,
            'additional_considerations' => $data['additional_considerations'] ?? null,
        ];

        $campaign->requirements()->updateOrCreate(['campaign_id' => $campaign->id], $requirementsData);
    }

    /**
     * Save campaign compensation information
     */
    private static function saveCampaignCompensation(Campaign $campaign, array $data): void
    {
        $compensationData = [
            'compensation_type' => $data['compensation_type'] ?? CompensationType::MONETARY,
            'compensation_amount' => $data['compensation_amount'] ?? null,
            'compensation_description' => $data['compensation_description'] ?? null,
            'compensation_details' => $data['compensation_details'] ?? null,
        ];

        $campaign->compensation()->updateOrCreate(['campaign_id' => $campaign->id], $compensationData);
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

    public static function unpublishCampaign(Campaign $campaign): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::DRAFT,
            'published_at' => null,
        ]);

        // Fire the CampaignUnpublished event
        event(new CampaignUnpublished($campaign, $campaign->user));

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

        // Handle array fields to prevent conversion errors
        $updateData = $data;
        $arrayFields = ['target_platforms', 'deliverables', 'success_metrics', 'social_requirements', 'placement_requirements', 'compensation_details'];

        foreach ($arrayFields as $field) {
            if (isset($updateData[$field])) {
                $updateData[$field] = ! empty($updateData[$field]) ? $updateData[$field] : [];
            }
        }

        $campaign->update($updateData);

        // Determine what changed
        $changes = self::arrayRecursiveDiff($campaign->fresh()->toArray(), $originalData);

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

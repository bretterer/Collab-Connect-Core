<?php

namespace App\Services;

use App\Enums\CampaignStatus;
use App\Enums\SystemMessageType;
use App\Events\CampaignArchived;
use App\Events\CampaignEdited;
use App\Events\CampaignPublished;
use App\Events\CampaignScheduled;
use App\Events\CampaignUnpublished;
use App\Models\Campaign;
use App\Models\Chat;
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
                'exclusivity_period' => $data['exclusivity_period'] ?? null,
                'application_deadline' => ! empty($data['application_deadline']) ? $data['application_deadline'] : null,
                'campaign_start_date' => ! empty($data['campaign_start_date']) ? $data['campaign_start_date'] : null,
                'campaign_completion_date' => ! empty($data['campaign_completion_date']) ? $data['campaign_completion_date'] : null,
                'publish_action' => $data['publish_action'] ?? 'publish',
                'scheduled_date' => ! empty($data['scheduled_date']) ? $data['scheduled_date'] : null,
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
            'archived_at' => now(),
        ]);

        // Cancel all active collaborations
        $campaign->collaborations()
            ->where('status', \App\Enums\CollaborationStatus::ACTIVE)
            ->update([
                'status' => \App\Enums\CollaborationStatus::CANCELLED,
                'cancelled_at' => now(),
                'cancellation_reason' => 'Campaign was archived',
            ]);

        // Fire the CampaignArchived event
        if ($archiver) {
            event(new CampaignArchived($campaign, $archiver));
        }

        return $campaign;
    }

    public static function startCampaign(Campaign $campaign, ?User $starter = null): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::IN_PROGRESS,
            'started_at' => now(),
        ]);

        // Create collaborations for all accepted applications
        self::createCollaborationsFromAcceptedApplications($campaign);

        // Send system message to all active chats for this campaign
        self::sendSystemMessageToAllCampaignChats(
            $campaign,
            SystemMessageType::CampaignStarted,
            "The campaign \"{$campaign->project_name}\" has started! Good luck with your content creation."
        );

        return $campaign;
    }

    public static function completeCampaign(Campaign $campaign, ?User $completer = null): Campaign
    {
        $campaign->update([
            'status' => CampaignStatus::COMPLETED,
            'completed_at' => now(),
        ]);

        // Complete all remaining active collaborations (those not already completed individually)
        // This uses CollaborationService which handles starting review periods
        $campaign->collaborations()
            ->where('status', \App\Enums\CollaborationStatus::ACTIVE)
            ->each(function ($collaboration) {
                CollaborationService::complete($collaboration);
            });

        // Send system message to all active chats for this campaign
        self::sendSystemMessageToAllCampaignChats(
            $campaign,
            SystemMessageType::CampaignEnded,
            "The campaign \"{$campaign->project_name}\" has ended. Thank you for your participation!"
        );

        // Archive all chats for this campaign so no more messages can be sent
        ChatService::archiveCampaignChats($campaign);

        return $campaign;
    }

    protected static function createCollaborationsFromAcceptedApplications(Campaign $campaign): void
    {
        $acceptedApplications = $campaign->applications()
            ->where('status', \App\Enums\CampaignApplicationStatus::ACCEPTED)
            ->get();

        foreach ($acceptedApplications as $application) {
            // Update application status to CONTRACTED
            $application->update([
                'status' => \App\Enums\CampaignApplicationStatus::CONTRACTED,
            ]);

            // Create collaboration record if it doesn't already exist
            \App\Models\Collaboration::firstOrCreate(
                [
                    'campaign_id' => $campaign->id,
                    'influencer_id' => $application->user_id,
                ],
                [
                    'campaign_application_id' => $application->id,
                    'business_id' => $campaign->business_id,
                    'status' => \App\Enums\CollaborationStatus::ACTIVE,
                    'started_at' => now(),
                ]
            );
        }
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
        $arrayFields = ['target_platforms', 'deliverables', 'success_metrics', 'social_requirements', 'placement_requirements', 'compensation_details'];

        foreach ($arrayFields as $field) {
            if (isset($updateData[$field])) {
                $updateData[$field] = ! empty($updateData[$field]) ? $updateData[$field] : [];
            }
        }

        // Handle date fields to prevent datetime format errors
        $dateFields = ['application_deadline', 'campaign_completion_date', 'scheduled_date'];

        foreach ($dateFields as $field) {
            if (isset($updateData[$field])) {
                $updateData[$field] = ! empty($updateData[$field]) ? $updateData[$field] : null;
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
        return $user->currentBusiness->campaigns()->inProgress()->orderBy('started_at', 'desc')->get();
    }

    /**
     * Get user's completed campaigns
     */
    public static function getUserCompleted(User $user)
    {
        return $user->currentBusiness->campaigns()->completed()->orderBy('completed_at', 'desc')->get();
    }

    /**
     * Get user's archived campaigns
     */
    public static function getUserArchived(User $user)
    {
        return $user->currentBusiness->campaigns()->archived()->orderBy('archived_at', 'desc')->get();
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

    /**
     * Send a system message to all active chats for a campaign.
     */
    protected static function sendSystemMessageToAllCampaignChats(
        Campaign $campaign,
        SystemMessageType $type,
        string $message
    ): void {
        $chats = Chat::forCampaign($campaign)
            ->active()
            ->get();

        foreach ($chats as $chat) {
            ChatService::sendSystemMessage($chat, $type, $message);
        }
    }
}

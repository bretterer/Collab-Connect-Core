<?php

namespace App\Services;

use App\Enums\CollaborationStatus;
use App\Models\Campaign;
use App\Models\CampaignApplication;
use App\Models\Collaboration;
use App\Models\User;

class CollaborationService
{
    public static function createFromApplication(CampaignApplication $application): Collaboration
    {
        $campaign = $application->campaign;

        return Collaboration::create([
            'campaign_id' => $campaign->id,
            'campaign_application_id' => $application->id,
            'influencer_id' => $application->user_id,
            'business_id' => $campaign->business_id,
            'status' => CollaborationStatus::ACTIVE,
            'started_at' => now(),
        ]);
    }

    public static function complete(Collaboration $collaboration, ?string $notes = null): Collaboration
    {
        $collaboration->update([
            'status' => CollaborationStatus::COMPLETED,
            'completed_at' => now(),
            'notes' => $notes,
        ]);

        // Start the review period for this individual collaboration
        $reviewService = app(ReviewService::class);
        $reviewService->startReviewPeriod($collaboration);

        return $collaboration->fresh();
    }

    public static function cancel(Collaboration $collaboration, ?string $reason = null): Collaboration
    {
        $collaboration->update([
            'status' => CollaborationStatus::CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        return $collaboration;
    }

    public static function markDeliverablesSubmitted(Collaboration $collaboration): Collaboration
    {
        $collaboration->update([
            'deliverables_submitted_at' => now(),
        ]);

        return $collaboration;
    }

    public static function getActiveForInfluencer(User $influencer)
    {
        return Collaboration::query()
            ->where('influencer_id', $influencer->id)
            ->where('status', CollaborationStatus::ACTIVE)
            ->with(['campaign', 'business'])
            ->orderBy('started_at', 'desc')
            ->get();
    }

    public static function getCompletedForInfluencer(User $influencer)
    {
        return Collaboration::query()
            ->where('influencer_id', $influencer->id)
            ->where('status', CollaborationStatus::COMPLETED)
            ->with(['campaign', 'business'])
            ->orderBy('completed_at', 'desc')
            ->get();
    }

    public static function getForCampaign(Campaign $campaign)
    {
        return $campaign->collaborations()
            ->with(['influencer', 'application'])
            ->orderBy('started_at', 'desc')
            ->get();
    }

    public static function getActiveForCampaign(Campaign $campaign)
    {
        return $campaign->collaborations()
            ->where('status', CollaborationStatus::ACTIVE)
            ->with(['influencer', 'application'])
            ->get();
    }
}

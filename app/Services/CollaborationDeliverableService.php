<?php

namespace App\Services;

use App\Enums\CollaborationActivityType;
use App\Enums\CollaborationDeliverableStatus;
use App\Events\DeliverableStatusChanged;
use App\Models\Collaboration;
use App\Models\CollaborationDeliverable;
use App\Models\CollaborationDeliverableFile;
use App\Models\User;
use App\Notifications\DeliverableApprovedNotification;
use App\Notifications\DeliverableSubmittedNotification;
use App\Notifications\RevisionRequestedNotification;
use Illuminate\Support\Facades\Storage;

class CollaborationDeliverableService
{
    /**
     * Initialize deliverables for a collaboration from the campaign's deliverables.
     */
    public static function initializeDeliverablesFromCampaign(Collaboration $collaboration): void
    {
        $campaign = $collaboration->campaign;
        $deliverables = $campaign->deliverables ?? [];

        foreach ($deliverables as $deliverableType) {
            CollaborationDeliverable::create([
                'collaboration_id' => $collaboration->id,
                'deliverable_type' => $deliverableType,
                'status' => CollaborationDeliverableStatus::NOT_STARTED,
            ]);
        }

        // Log the started activity
        CollaborationActivityService::log(
            $collaboration,
            null,
            CollaborationActivityType::STARTED,
            ['deliverable_count' => count($deliverables)]
        );
    }

    /**
     * Mark a deliverable as in progress.
     */
    public static function markInProgress(CollaborationDeliverable $deliverable): CollaborationDeliverable
    {
        $deliverable->update([
            'status' => CollaborationDeliverableStatus::IN_PROGRESS,
        ]);

        DeliverableStatusChanged::dispatch($deliverable);

        return $deliverable->fresh();
    }

    /**
     * Submit a deliverable.
     */
    public static function submit(
        CollaborationDeliverable $deliverable,
        User $user,
        string $postUrl,
        ?string $notes = null,
        ?array $files = null
    ): CollaborationDeliverable {
        $deliverable->update([
            'status' => CollaborationDeliverableStatus::SUBMITTED,
            'submitted_at' => now(),
            'post_url' => $postUrl,
            'notes' => $notes,
            'revision_feedback' => null, // Clear any previous feedback
        ]);

        // Handle file uploads
        if ($files) {
            foreach ($files as $file) {
                CollaborationDeliverableFile::create([
                    'collaboration_deliverable_id' => $deliverable->id,
                    'file_path' => $file['path'],
                    'file_name' => $file['name'],
                    'file_type' => $file['type'],
                    'uploaded_by_user_id' => $user->id,
                    'created_at' => now(),
                ]);
            }
        }

        // Log activity
        CollaborationActivityService::log(
            $deliverable->collaboration,
            $user,
            CollaborationActivityType::DELIVERABLE_SUBMITTED,
            ['deliverable_type' => $deliverable->deliverable_type->value]
        );

        // Notify business
        self::notifyBusinessOfSubmission($deliverable);

        DeliverableStatusChanged::dispatch($deliverable);

        return $deliverable->fresh();
    }

    /**
     * Approve a deliverable.
     */
    public static function approve(
        CollaborationDeliverable $deliverable,
        User $user
    ): CollaborationDeliverable {
        $deliverable->update([
            'status' => CollaborationDeliverableStatus::APPROVED,
            'approved_at' => now(),
        ]);

        // Log activity
        CollaborationActivityService::log(
            $deliverable->collaboration,
            $user,
            CollaborationActivityType::DELIVERABLE_APPROVED,
            ['deliverable_type' => $deliverable->deliverable_type->value]
        );

        // Notify influencer
        self::notifyInfluencerOfApproval($deliverable);

        DeliverableStatusChanged::dispatch($deliverable);

        return $deliverable->fresh();
    }

    /**
     * Request revision for a deliverable.
     */
    public static function requestRevision(
        CollaborationDeliverable $deliverable,
        User $user,
        string $feedback
    ): CollaborationDeliverable {
        $deliverable->update([
            'status' => CollaborationDeliverableStatus::REVISION_REQUESTED,
            'revision_feedback' => $feedback,
        ]);

        // Log activity
        CollaborationActivityService::log(
            $deliverable->collaboration,
            $user,
            CollaborationActivityType::REVISION_REQUESTED,
            [
                'deliverable_type' => $deliverable->deliverable_type->value,
                'feedback' => $feedback,
            ]
        );

        // Notify influencer
        self::notifyInfluencerOfRevisionRequest($deliverable);

        DeliverableStatusChanged::dispatch($deliverable);

        return $deliverable->fresh();
    }

    /**
     * Get progress statistics for a collaboration.
     */
    public static function getProgressStats(Collaboration $collaboration): array
    {
        $deliverables = $collaboration->deliverables;
        $total = $deliverables->count();

        if ($total === 0) {
            return [
                'total' => 0,
                'approved' => 0,
                'submitted' => 0,
                'in_progress' => 0,
                'not_started' => 0,
                'revision_requested' => 0,
                'pending' => 0,
                'percentage' => 0,
            ];
        }

        $approved = $deliverables->where('status', CollaborationDeliverableStatus::APPROVED)->count();
        $submitted = $deliverables->where('status', CollaborationDeliverableStatus::SUBMITTED)->count();
        $inProgress = $deliverables->where('status', CollaborationDeliverableStatus::IN_PROGRESS)->count();
        $notStarted = $deliverables->where('status', CollaborationDeliverableStatus::NOT_STARTED)->count();
        $revisionRequested = $deliverables->where('status', CollaborationDeliverableStatus::REVISION_REQUESTED)->count();

        return [
            'total' => $total,
            'approved' => $approved,
            'submitted' => $submitted,
            'in_progress' => $inProgress,
            'not_started' => $notStarted,
            'revision_requested' => $revisionRequested,
            'pending' => $total - $approved, // All non-approved deliverables
            'percentage' => (int) round(($approved / $total) * 100),
        ];
    }

    /**
     * Check if all deliverables are approved.
     */
    public static function areAllDeliverablesApproved(Collaboration $collaboration): bool
    {
        $deliverables = $collaboration->deliverables;

        if ($deliverables->isEmpty()) {
            return true; // No deliverables means nothing to approve
        }

        return $deliverables->every(fn ($d) => $d->status === CollaborationDeliverableStatus::APPROVED);
    }

    /**
     * Notify business users about a new submission.
     */
    protected static function notifyBusinessOfSubmission(CollaborationDeliverable $deliverable): void
    {
        $collaboration = $deliverable->collaboration;
        $business = $collaboration->business;

        if ($business) {
            foreach ($business->users as $businessUser) {
                $businessUser->notify(new DeliverableSubmittedNotification($deliverable));
            }
        }
    }

    /**
     * Notify the influencer about approval.
     */
    protected static function notifyInfluencerOfApproval(CollaborationDeliverable $deliverable): void
    {
        $influencer = $deliverable->collaboration->influencer;

        if ($influencer) {
            $influencer->notify(new DeliverableApprovedNotification($deliverable));
        }
    }

    /**
     * Notify the influencer about a revision request.
     */
    protected static function notifyInfluencerOfRevisionRequest(CollaborationDeliverable $deliverable): void
    {
        $influencer = $deliverable->collaboration->influencer;

        if ($influencer) {
            $influencer->notify(new RevisionRequestedNotification($deliverable));
        }
    }

    /**
     * Upload a file and return its storage info.
     */
    public static function uploadFile(
        CollaborationDeliverable $deliverable,
        $file,
        User $user
    ): array {
        $path = 'deliverables/'.$deliverable->id.'/'.uniqid().'.'.$file->extension();
        Storage::disk('linode')->put($path, file_get_contents($file->getRealPath()), 'public');

        return [
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'type' => $file->getMimeType(),
        ];
    }
}

<?php

namespace App\Services;

use App\Enums\CollaborationActivityType;
use App\Events\CollaborationActivityCreated;
use App\Models\Collaboration;
use App\Models\CollaborationActivity;
use App\Models\User;
use Illuminate\Support\Collection;

class CollaborationActivityService
{
    /**
     * Log an activity for a collaboration.
     */
    public static function log(
        Collaboration $collaboration,
        ?User $user,
        CollaborationActivityType $type,
        ?array $metadata = null
    ): CollaborationActivity {
        $activity = CollaborationActivity::create([
            'collaboration_id' => $collaboration->id,
            'user_id' => $user?->id,
            'type' => $type,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);

        // Dispatch event for real-time updates
        CollaborationActivityCreated::dispatch($activity);

        return $activity;
    }

    /**
     * Get the activity timeline for a collaboration.
     */
    public static function getTimeline(Collaboration $collaboration, ?int $limit = null): Collection
    {
        $query = $collaboration->activities()
            ->with('user')
            ->orderByDesc('created_at');

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get the most recent activity for a collaboration.
     */
    public static function getLatestActivity(Collaboration $collaboration): ?CollaborationActivity
    {
        return $collaboration->activities()
            ->with('user')
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * Get activities of a specific type for a collaboration.
     */
    public static function getActivitiesByType(
        Collaboration $collaboration,
        CollaborationActivityType $type
    ): Collection {
        return $collaboration->activities()
            ->where('type', $type)
            ->with('user')
            ->orderByDesc('created_at')
            ->get();
    }
}

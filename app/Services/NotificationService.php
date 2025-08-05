<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a new notification
     */
    public static function create(
        User $user,
        string $type,
        string $title,
        string $message,
        ?array $data = null,
        ?string $actionUrl = null
    ): Notification {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'action_url' => $actionUrl,
            'is_read' => false,
        ]);
    }

    /**
     * Create a campaign application notification
     */
    public static function createCampaignApplicationNotification(
        User $businessOwner,
        int $campaignId,
        string $campaignTitle,
        string $applicantName
    ): Notification {
        return self::create(
            $businessOwner,
            'campaign_application',
            'New Campaign Application',
            "{$applicantName} has applied to your campaign: {$campaignTitle}",
            [
                'campaign_id' => $campaignId,
                'applicant_name' => $applicantName,
            ],
            "/campaigns/{$campaignId}/applications"
        );
    }

    /**
     * Mark a notification as read
     */
    public static function markAsRead(Notification $notification): void
    {
        $notification->markAsRead();
    }

    /**
     * Mark all notifications as read for a user
     */
    public static function markAllAsRead(User $user): void
    {
        $user->notifications()
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Get unread notification count for a user
     */
    public static function getUnreadCount(User $user): int
    {
        return $user->notifications()
            ->where('is_read', false)
            ->count();
    }

    /**
     * Get recent notifications for a user
     */
    public static function getRecentNotifications(User $user, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return $user->notifications()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
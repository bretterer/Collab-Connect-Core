<?php

namespace App\Jobs;

use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use App\Notifications\UnreadMessagesReminderNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;

class CheckUnreadChatMessages implements ShouldQueue
{
    use Queueable;

    /**
     * The minimum number of hours after which to remind users about unread messages.
     */
    protected int $minHoursThreshold = 4;

    /**
     * The maximum number of days after which we stop sending reminders.
     * Messages older than this are considered "stale" and users have likely
     * intentionally ignored them.
     */
    protected int $maxDaysThreshold = 7;

    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * This properly targets notifications to the INTENDED recipient:
     * - If business member sends message → notify influencer if they haven't read it
     * - If influencer sends message → notify business members who haven't read it
     */
    public function handle(): void
    {
        // Get all user messages within the notification window (not system messages)
        // Only messages between minHoursThreshold and maxDaysThreshold old
        // We load reads to check per-recipient read status, not just "any read"
        $staleMessages = Message::query()
            ->where('is_system_message', false)
            ->where('created_at', '<', now()->subHours($this->minHoursThreshold))
            ->where('created_at', '>', now()->subDays($this->maxDaysThreshold))
            ->with([
                'chat.business.users',
                'chat.influencer.user',
                'user',
                'reads', // Load reads to check specific recipients
            ])
            ->get();

        // Group messages by chat to avoid spamming users
        $messagesByChat = $staleMessages->groupBy('chat_id');

        foreach ($messagesByChat as $chatId => $messages) {
            $chat = $messages->first()->chat;

            if (! $chat || ! $chat->isActive()) {
                continue;
            }

            // Determine who needs to be notified based on who HASN'T read
            $usersToNotify = collect();

            foreach ($messages as $message) {
                // Get the sender's role
                $senderRole = $chat->getUserRole($message->user);

                if ($senderRole === 'business') {
                    // Business member sent the message → check if influencer has read it
                    $influencerUser = $chat->influencer?->user;
                    if ($influencerUser && ! $message->isReadBy($influencerUser)) {
                        $usersToNotify->push($influencerUser);
                    }
                } elseif ($senderRole === 'influencer') {
                    // Influencer sent the message → check each business member
                    if ($chat->business) {
                        foreach ($chat->business->users as $businessUser) {
                            if (! $message->isReadBy($businessUser)) {
                                $usersToNotify->push($businessUser);
                            }
                        }
                    }
                }
            }

            // Remove duplicates and notify each user (with rate limiting)
            $usersToNotify->unique('id')->each(function (User $user) use ($chat) {
                $this->notifyUserIfNeeded($user, $chat);
            });
        }
    }

    /**
     * Send notification to user if they haven't been notified recently.
     */
    protected function notifyUserIfNeeded(User $user, Chat $chat): void
    {
        // Use cache to prevent sending multiple notifications within 24 hours
        $cacheKey = "unread_notification:{$user->id}:{$chat->id}";

        if (Cache::has($cacheKey)) {
            return;
        }

        // Send the notification
        $user->notify(new UnreadMessagesReminderNotification);

        // Cache for 24 hours to prevent spam
        Cache::put($cacheKey, true, now()->addHours(24));
    }
}

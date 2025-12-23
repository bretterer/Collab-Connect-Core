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
     * The number of hours after which to remind users about unread messages.
     */
    protected int $hoursThreshold = 4;

    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Get all messages that are unread and older than the threshold
        $staleMessages = Message::query()
            ->where('is_system_message', false)
            ->where('created_at', '<', now()->subHours($this->hoursThreshold))
            ->whereDoesntHave('reads')
            ->with(['chat.business.users', 'chat.influencer.user', 'user'])
            ->get();

        // Group messages by chat to avoid spamming users
        $messagesByChat = $staleMessages->groupBy('chat_id');

        foreach ($messagesByChat as $chatId => $messages) {
            $chat = Chat::with(['business.users', 'influencer.user'])->find($chatId);

            if (! $chat || ! $chat->isActive()) {
                continue;
            }

            // Determine who needs to be notified
            $usersToNotify = collect();

            foreach ($messages as $message) {
                // Get the sender's role
                $senderRole = $chat->getUserRole($message->user);

                if ($senderRole === 'business') {
                    // Business member sent the message, notify the influencer
                    if ($chat->influencer && $chat->influencer->user) {
                        $usersToNotify->push($chat->influencer->user);
                    }
                } elseif ($senderRole === 'influencer') {
                    // Influencer sent the message, notify all business members
                    if ($chat->business) {
                        $usersToNotify = $usersToNotify->merge($chat->business->users);
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

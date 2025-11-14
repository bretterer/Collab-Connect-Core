<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class UnreadMessagesReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    /**
     * Delivery channels.
     */
    public function via($notifiable): array
    {
        // You can also add 'database' if you want to track it
        return ['mail', 'database'];
    }

    /**
     * Email message.
     */
    public function toMail($notifiable): MailMessage
    {
        $url = url('/chat'); // adjust if your message center uses a route like route('messages.index')

        return (new MailMessage)
            ->subject('You have unread chat messages waiting for you')
            ->greeting("Hey {$notifiable->name},")
            ->line('You have one or more chat messages in your inbox that have not been read.')
            ->action('View Chats', $url)
            ->line('We just wanted to make sure you don’t miss any important updates or replies.');
    }

    /**
     * Optional database payload.
     */
    public function toArray($notifiable): array
    {
        return [
            'title' => 'Unread Chat Messages Reminder',
            'body' => 'You have chat messages that have not been read.',
            'link' => url('/chat'),
            'notified_at' => Carbon::now(),
        ];
    }
}

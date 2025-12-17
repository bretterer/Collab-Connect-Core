<?php

namespace App\Notifications;

use App\Models\Influencer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InfluencerReenabledNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Influencer $influencer)
    {

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {

        return (new MailMessage)
            ->subject('Your Profile is Now Searchable Again!')
            ->greeting('Welcome back, '.$this->influencer->user->name.'!')
            ->line('Great news! Your profile has been automatically re-enabled and is now visible to brands searching for creators.')
            ->line('You may start receiving campaign invitations and collaboration opportunities again.')
            ->action('View Your Dashboard', route('dashboard'))
            ->line('If you need to pause your profile again, you can do so anytime from your account settings.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

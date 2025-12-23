<?php

namespace App\Notifications;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignAutoPublishedNotification extends Notification
{
    use Queueable;

    public function __construct(public Campaign $campaign)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your Campaign \"{$this->campaign->project_name}\" is Now Live!")
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Great news! Your scheduled campaign **{$this->campaign->project_name}** has been automatically published and is now live on CollabConnect.")
            ->line('Influencers can now discover and apply to your campaign.')
            ->action('View Campaign', url("/campaigns/{$this->campaign->id}"))
            ->line('You\'ll be notified when influencers apply to your campaign.')
            ->salutation('Best regards, The CollabConnect Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Campaign \"{$this->campaign->project_name}\" is now live!",
            'message' => 'Your scheduled campaign has been automatically published and is now visible to influencers.',
            'action_url' => "/campaigns/{$this->campaign->id}",
        ];
    }
}

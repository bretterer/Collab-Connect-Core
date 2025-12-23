<?php

namespace App\Notifications;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignAutoStartedNotification extends Notification
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
        $collaboratorCount = $this->campaign->collaborations()->count();

        return (new MailMessage)
            ->subject("Your Campaign \"{$this->campaign->project_name}\" Has Started!")
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Your campaign **{$this->campaign->project_name}** has officially started!")
            ->line("You have **{$collaboratorCount} influencer(s)** actively working on this campaign.")
            ->line('**What happens now:**')
            ->line('- Influencers will begin creating content according to your campaign guidelines')
            ->line('- You can communicate with them through the campaign chat')
            ->line('- Review and approve deliverables as they are submitted')
            ->action('Manage Campaign', url("/campaigns/{$this->campaign->id}"))
            ->salutation('Best regards, The CollabConnect Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Campaign \"{$this->campaign->project_name}\" has started!",
            'message' => 'Your campaign is now in progress. Influencers are actively working on content.',
            'action_url' => "/campaigns/{$this->campaign->id}",
        ];
    }
}

<?php

namespace App\Notifications;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignAutoCompletedNotification extends Notification
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
            ->subject("Your Campaign \"{$this->campaign->project_name}\" Has Completed!")
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Your campaign **{$this->campaign->project_name}** has been automatically marked as completed.")
            ->line('**What happens now:**')
            ->line('- A review period has started for all collaborations')
            ->line('- You can leave reviews for the influencers you worked with')
            ->line('- Influencers can also leave reviews for your business')
            ->action('View Campaign Results', url("/campaigns/{$this->campaign->id}"))
            ->line('Thank you for using CollabConnect for your influencer marketing!')
            ->salutation('Best regards, The CollabConnect Team');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => "Campaign \"{$this->campaign->project_name}\" has completed!",
            'message' => 'Your campaign has ended. Don\'t forget to leave reviews for your collaborators.',
            'action_url' => "/campaigns/{$this->campaign->id}",
        ];
    }
}

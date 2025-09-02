<?php

namespace App\Notifications;

use App\Models\Campaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCampaignNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Campaign $campaign)
    {
        //
    }

    public function databaseType(object $notifiable): string
    {
        return 'new-campaign';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Campaign Match!',
            'message' => "A new campaign '{$this->campaign->campaign_goal}' matches your profile.",
            'campaign_id' => $this->campaign->id,
            'campaign_title' => $this->campaign->campaign_goal,
            'business_name' => $this->campaign->business->name,
            'action_url' => "/campaigns/{$this->campaign->id}",
        ];
    }
}

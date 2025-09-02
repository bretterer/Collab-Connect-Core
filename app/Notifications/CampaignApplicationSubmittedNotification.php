<?php

namespace App\Notifications;

use App\Models\CampaignApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignApplicationSubmittedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public CampaignApplication $application)
    {
        //
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
            'title' => 'New Application to Campaign',
            'message' => 'A new application has been submitted to your campaign: ' . $this->application->campaign->project_name,
            'campaign_id' => $this->application->campaign_id,
            'user_id' => $this->application->user_id,
            'status' => $this->application->status,
            'submitted_at' => $this->application->submitted_at,
            'action_url' => "/applications/{$this->application->id}",
        ];
    }
}

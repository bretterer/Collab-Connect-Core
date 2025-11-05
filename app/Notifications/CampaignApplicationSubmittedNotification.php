<?php

namespace App\Notifications;

use App\Models\CampaignApplication;
use Illuminate\Bus\Queueable;
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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $influencer = $this->application->user;
        $campaign = $this->application->campaign;

        return (new MailMessage)
            ->subject('New Application Received for Your Campaign')
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Great news! You've received a new application for your campaign **{$campaign->project_name}**.")
            ->line('**Influencer Details:**')
            ->line("• Name: {$influencer->first_name} {$influencer->last_name}")
            ->line("• Email: {$influencer->email}")
            ->when($influencer->isInfluencerAccount(), function ($message) use ($influencer) {
                $profile = $influencer->profile;
                if ($profile->instagram_handle) {
                    $message->line("• Instagram: @{$profile->instagram_handle}");
                }
                if ($profile->tiktok_handle) {
                    $message->line("• TikTok: @{$profile->tiktok_handle}");
                }
                if ($profile->youtube_handle) {
                    $message->line("• YouTube: {$profile->youtube_handle}");
                }
                if ($profile->follower_count) {
                    $message->line('• Total Followers: '.number_format($profile->follower_count));
                }
            })
            ->line('**Application Details:**')
            ->line("• Submitted: {$this->application->submitted_at->format('M j, Y g:i A')}")
            ->when($this->application->message, function ($message) {
                $message->line("• Message: \"{$this->application->message}\"");
            })
            ->action('Review Application', url("/applications/{$this->application->id}"))
            ->line('Log in to your dashboard to review the full application details and manage your campaign applications.')
            ->salutation('Best regards, The CollabConnect Team');
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
            'message' => 'A new application has been submitted to your campaign: '.$this->application->campaign->project_name,
            'campaign_id' => $this->application->campaign_id,
            'user_id' => $this->application->user_id,
            'status' => $this->application->status,
            'submitted_at' => $this->application->submitted_at,
            'action_url' => "/applications/{$this->application->id}",
        ];
    }
}

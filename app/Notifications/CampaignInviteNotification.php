<?php

namespace App\Notifications;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignInviteNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Campaign $campaign,
        public User $invitingBusiness,
        public string $personalMessage
    ) {
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
        $business = $this->campaign->business;
        $businessContact = $this->invitingBusiness;

        return (new MailMessage)
            ->subject('🚀 You\'re Invited to Collaborate!')
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Exciting news! **{$businessContact->first_name} {$businessContact->last_name}** from **{$business->name}** has personally invited you to collaborate on their campaign.")
            ->line("**Campaign: {$this->campaign->project_name}**")
            ->line("**Personal Message from {$businessContact->first_name}:**")
            ->line("\"{$this->personalMessage}\"")
            ->line("**Campaign Details:**")
            ->line("• **Goal:** {$this->campaign->campaign_goal}")
            ->line("• **Business:** {$business->name}")
            ->line("• **Compensation:** {$this->campaign->compensation_type->label()}")
            ->when($this->campaign->campaign_completion_date, function ($message) {
                $message->line("• **Completion Date:** {$this->campaign->campaign_completion_date->format('M j, Y')}");
            })
            ->when($this->campaign->target_platforms, function ($message) {
                $platforms = implode(', ', array_map('ucfirst', $this->campaign->target_platforms));
                $message->line("• **Target Platforms:** {$platforms}");
            })
            ->line("**Why they chose you:**")
            ->line("This business specifically selected you based on your content, audience, and unique style. This is a curated invitation, not a mass campaign!")
            ->action('View Campaign & Apply', url("/campaigns/{$this->campaign->id}"))
            ->line("**Don't miss out!** Personal invitations like this are rare and show genuine interest in your work.")
            ->line("Ready to create something amazing together?")
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
            'title' => 'Campaign Invitation from ' . $this->campaign->business->name,
            'message' => $this->invitingBusiness->first_name . ' from ' . $this->campaign->business->name . ' has invited you to collaborate on "' . $this->campaign->project_name . '"',
            'campaign_id' => $this->campaign->id,
            'business_id' => $this->campaign->business_id,
            'inviting_user_id' => $this->invitingBusiness->id,
            'personal_message' => $this->personalMessage,
            'action_url' => "/campaigns/{$this->campaign->id}",
        ];
    }
}

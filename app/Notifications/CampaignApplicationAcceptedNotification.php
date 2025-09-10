<?php

namespace App\Notifications;

use App\Models\CampaignApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignApplicationAcceptedNotification extends Notification
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
        $campaign = $this->application->campaign;
        $business = $campaign->business;
        $businessContact = $business->users()->first();
        
        return (new MailMessage)
            ->subject('🎉 Your Campaign Application Has Been Accepted!')
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Fantastic news! Your application for the campaign **{$campaign->project_name}** has been accepted!")
            ->line("**Campaign Details:**")
            ->line("• Campaign: {$campaign->project_name}")
            ->line("• Business: {$business->name}")
            ->line("• Contact: {$businessContact?->first_name} {$businessContact?->last_name}")
            ->line("• Campaign Completion Date: {$campaign->campaign_completion_date->format('M j, Y')}")
            ->when($campaign->compensation_amount, function ($message) use ($campaign) {
                $message->line("• Compensation: {$campaign->compensation_type->label()} - \${$campaign->compensation_amount}");
            })
            ->line("**Next Steps:**")
            ->line("• The business will contact you directly with campaign details and requirements")
            ->line("• Please review all campaign guidelines and deliverables carefully")
            ->line("• Complete your content by the campaign completion date")
            ->action('View Campaign Details', url("/campaigns/{$campaign->id}"))
            ->line('We\'re excited to see the amazing content you\'ll create for this collaboration!')
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
            'title' => 'Your application to ' . $this->application->campaign->project_name . ' has been accepted!',
            'message' => 'You have been accepted to work on ' . $this->application->campaign->project_name . '! Please check watch your messages for information from the business about this campaign!',
            'action_url' => '/chat'
        ];
    }
}

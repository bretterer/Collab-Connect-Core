<?php

namespace App\Notifications;

use App\Models\CampaignApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignApplicationDeclinedNotification extends Notification
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
        
        return (new MailMessage)
            ->subject('Update on Your Campaign Application')
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Thank you for your interest in the campaign **{$campaign->project_name}** from {$business->name}.")
            ->line("After careful consideration, we regret to inform you that your application was not selected for this particular campaign.")
            ->line("**This doesn't reflect on your capabilities as a creator.** Businesses often have very specific requirements or may have limited spots available.")
            ->line("**We encourage you to:**")
            ->line("• Continue applying to other campaigns that match your skills and interests")
            ->line("• Keep creating amazing content and growing your audience")
            ->line("• Check out our platform regularly for new opportunities")
            ->action('Browse New Campaigns', url('/discover'))
            ->line('Thank you for being part of the CollabConnect community. We appreciate your interest and look forward to seeing you match with the perfect campaign soon!')
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
            'title' => 'Your Application to ' . $this->application->campaign->project_name . ' has been declined.',
            'message' => 'I\'m sorry to say that your application to ' . $this->application->campaign->project_name . ' has been declined. We appreciate your interest and encourage you to apply for future opportunities.',
        ];
    }
}

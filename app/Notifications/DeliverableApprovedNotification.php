<?php

namespace App\Notifications;

use App\Models\CollaborationDeliverable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliverableApprovedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public CollaborationDeliverable $deliverable)
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
        $collaboration = $this->deliverable->collaboration;
        $campaign = $collaboration->campaign;
        $business = $collaboration->business;

        return (new MailMessage)
            ->subject('Your Deliverable Has Been Approved!')
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Great news! Your deliverable for **{$campaign->project_name}** has been approved.")
            ->line('**Deliverable Details:**')
            ->line("- Type: {$this->deliverable->deliverable_type->label()}")
            ->line("- Approved by: {$business->name}")
            ->line("- Approved at: {$this->deliverable->approved_at->format('M j, Y g:i A')}")
            ->action('View Collaboration', url("/collaborations/{$collaboration->id}"))
            ->line('Keep up the great work!')
            ->salutation('Best regards, The CollabConnect Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $collaboration = $this->deliverable->collaboration;

        return [
            'title' => 'Deliverable Approved',
            'message' => "{$this->deliverable->deliverable_type->label()} approved for {$collaboration->campaign->project_name}",
            'collaboration_id' => $collaboration->id,
            'deliverable_id' => $this->deliverable->id,
            'deliverable_type' => $this->deliverable->deliverable_type->value,
            'action_url' => "/collaborations/{$collaboration->id}",
        ];
    }
}

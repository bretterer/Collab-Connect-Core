<?php

namespace App\Notifications;

use App\Models\CollaborationDeliverable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliverableSubmittedNotification extends Notification
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
        $influencer = $collaboration->influencer;
        $campaign = $collaboration->campaign;

        return (new MailMessage)
            ->subject('Deliverable Submitted for Review')
            ->greeting("Hi {$notifiable->first_name},")
            ->line("A deliverable has been submitted for your campaign **{$campaign->project_name}**.")
            ->line('**Deliverable Details:**')
            ->line("- Type: {$this->deliverable->deliverable_type->label()}")
            ->line("- Submitted by: {$influencer->name}")
            ->when($this->deliverable->post_url, function ($message) {
                $message->line("- Post URL: {$this->deliverable->post_url}");
            })
            ->when($this->deliverable->notes, function ($message) {
                $message->line("- Notes: \"{$this->deliverable->notes}\"");
            })
            ->action('Review Deliverable', url("/collaborations/{$collaboration->id}"))
            ->line('Please review and approve the deliverable or request revisions.')
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
            'title' => 'Deliverable Submitted',
            'message' => "{$this->deliverable->deliverable_type->label()} submitted for {$collaboration->campaign->project_name}",
            'collaboration_id' => $collaboration->id,
            'deliverable_id' => $this->deliverable->id,
            'deliverable_type' => $this->deliverable->deliverable_type->value,
            'action_url' => "/collaborations/{$collaboration->id}",
        ];
    }
}

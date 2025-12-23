<?php

namespace App\Notifications;

use App\Models\CollaborationDeliverable;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RevisionRequestedNotification extends Notification
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
            ->subject('Revision Requested for Your Deliverable')
            ->greeting("Hi {$notifiable->first_name},")
            ->line("{$business->name} has requested a revision for your deliverable on **{$campaign->project_name}**.")
            ->line('**Deliverable Details:**')
            ->line("- Type: {$this->deliverable->deliverable_type->label()}")
            ->when($this->deliverable->revision_feedback, function ($message) {
                $message->line('**Feedback:**')
                    ->line("\"{$this->deliverable->revision_feedback}\"");
            })
            ->action('View & Revise', url("/collaborations/{$collaboration->id}"))
            ->line('Please review the feedback and submit a revised version.')
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
            'title' => 'Revision Requested',
            'message' => "Revision requested for {$this->deliverable->deliverable_type->label()} on {$collaboration->campaign->project_name}",
            'collaboration_id' => $collaboration->id,
            'deliverable_id' => $this->deliverable->id,
            'deliverable_type' => $this->deliverable->deliverable_type->value,
            'revision_feedback' => $this->deliverable->revision_feedback,
            'action_url' => "/collaborations/{$collaboration->id}",
        ];
    }
}

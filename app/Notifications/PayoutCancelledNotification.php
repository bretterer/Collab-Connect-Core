<?php

namespace App\Notifications;

use App\Models\ReferralPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ReferralPayout $payout, public string $reason)
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->payout->amount, 2);

        return (new MailMessage)
            ->subject('Referral Payout Cancelled')
            ->greeting("Hi {$notifiable->first_name},")
            ->line("Unfortunately, your referral commission payout of **\${$amount}** has been cancelled.")
            ->line("**Reason:** {$this->reason}")
            ->line('After 3 attempts to process your payout, we were unable to complete the transaction because no PayPal email was provided.')
            ->line('To ensure future payouts are processed successfully, please add your PayPal email to your referral account.')
            ->action('Add PayPal Email', route('referral.index'))
            ->line('If you have any questions, please contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'payout_id' => $this->payout->id,
            'amount' => $this->payout->amount,
            'reason' => $this->reason,
        ];
    }
}

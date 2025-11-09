<?php

namespace App\Notifications;

use App\Models\ReferralPayout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayPalEmailRequiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ReferralPayout $payout, public int $attemptNumber)
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
        $isFinalAttempt = $this->attemptNumber === 3;

        $message = (new MailMessage)
            ->subject('Action Required: PayPal Email Needed for Payout')
            ->greeting("Hi {$notifiable->first_name},")
            ->line("You have a referral commission payout of **\${$amount}** ready to be processed!");

        if ($isFinalAttempt) {
            $message->line('**This is our final attempt to process your payout.**')
                ->line('If you do not add your PayPal email within the next 72 hours, this payout will be cancelled.');
        } else {
            $message->line('However, we need your PayPal email address to send you the payment.')
                ->line("We'll retry this payout in 72 hours (Attempt {$this->attemptNumber} of 3).");
        }

        return $message
            ->action('Add PayPal Email', route('referrals.index'))
            ->line('Thank you for being part of our referral program!');
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
            'attempt_number' => $this->attemptNumber,
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Stripe\SubscriptionSchedule;

class SubscriptionScheduleReleasedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public SubscriptionSchedule $schedule,
        public string $billableType,
        public int $billableId,
        public string $customerName,
        public string $customerEmail,
        public string $action,
    ) {}

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
        $mail = (new MailMessage)
            ->subject('Subscription Schedule Released - Action Required Review')
            ->greeting('Admin Alert')
            ->line("A subscription schedule was automatically released to allow a customer to {$this->action}.")
            ->line('**Customer Details:**')
            ->line("• Name: {$this->customerName}")
            ->line("• Email: {$this->customerEmail}")
            ->line("• Account Type: {$this->getBillableTypeName()}")
            ->line('')
            ->line('**Schedule Details:**')
            ->line("• Schedule ID: {$this->schedule->id}")
            ->line("• Subscription ID: {$this->schedule->subscription}")
            ->line("• Status (before release): {$this->schedule->status}");

        // Add phase information
        if (! empty($this->schedule->phases)) {
            $mail->line('')->line('**Scheduled Phases:**');

            foreach ($this->schedule->phases as $index => $phase) {
                $phaseNumber = $index + 1;
                $startDate = date('M j, Y', $phase->start_date);
                $endDate = $phase->end_date ? date('M j, Y', $phase->end_date) : 'Ongoing';

                $mail->line("Phase {$phaseNumber}: {$startDate} - {$endDate}");

                // Show items in the phase
                if (! empty($phase->items)) {
                    foreach ($phase->items as $item) {
                        $priceId = is_string($item->price) ? $item->price : $item->price->id ?? 'Unknown';
                        $mail->line("  • Price: {$priceId}");
                    }
                }

                // Show coupon if present
                if (! empty($phase->coupon)) {
                    $couponId = is_string($phase->coupon) ? $phase->coupon : $phase->coupon->id ?? 'Unknown';
                    $mail->line("  • Coupon: {$couponId}");
                }

                // Show discount if present
                if (! empty($phase->discounts)) {
                    foreach ($phase->discounts as $discount) {
                        $couponId = $discount->coupon ?? 'Unknown';
                        $mail->line("  • Discount Coupon: {$couponId}");
                    }
                }
            }
        }

        $mail->line('')
            ->line('**What This Means:**')
            ->line('The schedule has been released, meaning any future scheduled changes (like plan upgrades, downgrades, or coupon applications) have been cancelled. The subscription will continue on its current terms.')
            ->line('')
            ->line('If this schedule contained important changes (like a coupon for a customer), you may need to manually apply them.')
            ->action('View in Stripe Dashboard', "https://dashboard.stripe.com/subscription_schedules/{$this->schedule->id}")
            ->salutation('- CollabConnect System');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'schedule_id' => $this->schedule->id,
            'subscription_id' => $this->schedule->subscription,
            'billable_type' => $this->billableType,
            'billable_id' => $this->billableId,
            'action' => $this->action,
        ];
    }

    protected function getBillableTypeName(): string
    {
        return match ($this->billableType) {
            'App\Models\Business' => 'Business',
            'App\Models\Influencer' => 'Influencer',
            default => class_basename($this->billableType),
        };
    }
}

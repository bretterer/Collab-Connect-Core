<?php

namespace App\Notifications;

use App\Models\Market;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MarketOpenedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Market $market)
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
        return (new MailMessage)
            ->subject('Great News! We\'ve Launched in Your Area')
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('We have great news! **'.config('app.name').'** is now available in your area.')
            ->line('You were on the waitlist for the **'.$this->market->name.'** market, and we\'re excited to let you know that we\'ve officially launched!')
            ->line('You can now access the full platform and start connecting with '.($notifiable->isBusinessAccount() ? 'influencers' : 'businesses').' in your area.')
            ->action('Get Started Now', route('dashboard'))
            ->line('Thank you for your patience and for being an early supporter of '.config('app.name').'!')
            ->line('We can\'t wait to see what you create.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'market_id' => $this->market->id,
            'market_name' => $this->market->name,
        ];
    }
}

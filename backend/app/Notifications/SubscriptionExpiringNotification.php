<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class SubscriptionExpiringNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Subscription $subscription
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $daysRemaining = Carbon::now()->diffInDays($this->subscription->end_date);
        
        return (new MailMessage)
            ->subject('Your GeoOps Subscription is Expiring Soon')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your ' . $this->subscription->package->name . ' subscription will expire in ' . $daysRemaining . ' day(s).')
            ->line('To avoid service interruption, please renew your subscription before ' . $this->subscription->end_date->format('F j, Y') . '.')
            ->action('Renew Now', url('/subscriptions/renew'))
            ->line('Thank you for using GeoOps!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'subscription_expiring',
            'subscription_id' => $this->subscription->id,
            'package_name' => $this->subscription->package->name,
            'end_date' => $this->subscription->end_date,
            'days_remaining' => Carbon::now()->diffInDays($this->subscription->end_date),
        ];
    }
}

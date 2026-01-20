<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiredNotification extends Notification
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
        return (new MailMessage)
            ->subject('Your GeoOps Subscription Has Expired')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your ' . $this->subscription->package->name . ' subscription has expired.')
            ->line('To continue using GeoOps services, please renew your subscription.')
            ->action('Renew Subscription', url('/subscriptions/renew'))
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
            'type' => 'subscription_expired',
            'subscription_id' => $this->subscription->id,
            'package_name' => $this->subscription->package->name,
            'end_date' => $this->subscription->end_date,
        ];
    }
}

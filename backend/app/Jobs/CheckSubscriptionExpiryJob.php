<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Notifications\SubscriptionExpiredNotification;
use App\Notifications\SubscriptionExpiringNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckSubscriptionExpiryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Starting subscription expiry check');

            $now = Carbon::now();

            // Find expired subscriptions with eager loading to avoid N+1
            $expiredSubscriptions = Subscription::where('status', 'active')
                ->where('end_date', '<', $now)
                ->with(['organization.owner'])
                ->get();

            foreach ($expiredSubscriptions as $subscription) {
                $subscription->update(['status' => 'expired']);
                
                Log::info('Subscription expired', [
                    'subscription_id' => $subscription->id,
                    'organization_id' => $subscription->organization_id,
                ]);

                // Send notification to organization owner
                if ($subscription->organization && $subscription->organization->owner) {
                    $subscription->organization->owner->notify(
                        new SubscriptionExpiredNotification($subscription)
                    );
                }
            }

            // Find subscriptions expiring soon (within 7 days) with eager loading
            $expiringSoon = Subscription::where('status', 'active')
                ->whereBetween('end_date', [$now, $now->copy()->addDays(7)])
                ->with(['organization.owner'])
                ->get();

            foreach ($expiringSoon as $subscription) {
                Log::info('Subscription expiring soon', [
                    'subscription_id' => $subscription->id,
                    'organization_id' => $subscription->organization_id,
                    'end_date' => $subscription->end_date,
                ]);

                // Send reminder notification to organization owner
                if ($subscription->organization && $subscription->organization->owner) {
                    $subscription->organization->owner->notify(
                        new SubscriptionExpiringNotification($subscription)
                    );
                }
            }

            Log::info('Subscription expiry check completed', [
                'expired_count' => $expiredSubscriptions->count(),
                'expiring_soon_count' => $expiringSoon->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to check subscription expiry', [
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('CheckSubscriptionExpiryJob failed', [
            'error' => $exception->getMessage(),
        ]);
    }
}

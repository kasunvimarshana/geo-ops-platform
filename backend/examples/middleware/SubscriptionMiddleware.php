<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Subscription;
use Symfony\Component\HttpFoundation\Response;

/**
 * Subscription Middleware
 * 
 * Enforces subscription package limits and features.
 */
class SubscriptionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  string  $feature  Feature to check (e.g., 'measurements', 'drivers')
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = auth()->user();
        $organizationId = $user->organization_id;

        // Get active subscription
        $subscription = Subscription::where('organization_id', $organizationId)
            ->where('status', 'active')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'No active subscription found',
            ], 403);
        }

        // Check if subscription has expired
        if ($subscription->expires_at && $subscription->expires_at < now()) {
            return response()->json([
                'success' => false,
                'message' => 'Your subscription has expired. Please renew to continue.',
            ], 403);
        }

        // Check feature limits
        $features = $subscription->features;
        
        switch ($feature) {
            case 'measurements':
                $count = \App\Models\Measurement::where('organization_id', $organizationId)->count();
                $limit = $features['max_measurements'] ?? 0;
                
                if ($count >= $limit) {
                    return response()->json([
                        'success' => false,
                        'message' => "You have reached your measurement limit ({$limit}). Please upgrade your subscription.",
                    ], 403);
                }
                break;

            case 'drivers':
                $count = \App\Models\User::where('organization_id', $organizationId)
                    ->whereHas('role', fn($q) => $q->where('name', 'driver'))
                    ->count();
                $limit = $features['max_drivers'] ?? 0;
                
                if ($count >= $limit) {
                    return response()->json([
                        'success' => false,
                        'message' => "You have reached your driver limit ({$limit}). Please upgrade your subscription.",
                    ], 403);
                }
                break;

            case 'gps_tracking':
                if (empty($features['has_gps_tracking'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'GPS tracking is not available in your current subscription. Please upgrade.',
                    ], 403);
                }
                break;
        }

        return $next($request);
    }
}

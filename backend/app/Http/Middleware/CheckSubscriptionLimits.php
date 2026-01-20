<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Organization;

class CheckSubscriptionLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $limitType): Response
    {
        $user = $request->user();
        
        if (!$user || !$user->organization_id) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'UNAUTHORIZED',
                    'message' => 'User must belong to an organization',
                ]
            ], 401);
        }

        $organization = Organization::find($user->organization_id);
        
        if (!$organization) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'ORGANIZATION_NOT_FOUND',
                    'message' => 'Organization not found',
                ]
            ], 404);
        }

        // Check if subscription is active
        if (!$organization->hasActiveSubscription()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SUBSCRIPTION_EXPIRED',
                    'message' => 'Your subscription has expired. Please renew to continue.',
                    'details' => [
                        'package' => $organization->subscription_package,
                        'expired_at' => $organization->subscription_expires_at,
                    ]
                ]
            ], 403);
        }

        // Get subscription limits
        $limits = $organization->getSubscriptionLimits();

        // Check specific limit type
        $exceeded = false;
        $limitMessage = '';

        switch ($limitType) {
            case 'measurements':
                $count = $this->getMeasurementsThisMonth($organization);
                $limit = $limits['measurements_per_month'];
                
                if ($limit !== -1 && $count >= $limit) {
                    $exceeded = true;
                    $limitMessage = "You have reached your monthly measurement limit of {$limit}. Upgrade your plan for more.";
                }
                break;

            case 'drivers':
                $count = $organization->users()->where('role', 'driver')->count();
                $limit = $limits['drivers'];
                
                if ($limit !== -1 && $count >= $limit) {
                    $exceeded = true;
                    $limitMessage = "You have reached your driver limit of {$limit}. Upgrade your plan for more drivers.";
                }
                break;

            case 'pdf_exports':
                $count = $this->getPdfExportsThisMonth($organization);
                $limit = $limits['pdf_exports_per_month'];
                
                if ($limit !== -1 && $count >= $limit) {
                    $exceeded = true;
                    $limitMessage = "You have reached your monthly PDF export limit of {$limit}. Upgrade your plan for more.";
                }
                break;

            default:
                // Unknown limit type, allow through
                break;
        }

        if ($exceeded) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SUBSCRIPTION_LIMIT_EXCEEDED',
                    'message' => $limitMessage,
                    'details' => [
                        'limit_type' => $limitType,
                        'current_package' => $organization->subscription_package,
                        'current_limit' => $limit,
                        'current_usage' => $count,
                        'upgrade_available' => true,
                    ]
                ]
            ], 403);
        }

        return $next($request);
    }

    /**
     * Get the number of measurements created this month.
     */
    private function getMeasurementsThisMonth(Organization $organization): int
    {
        return \App\Models\LandMeasurement::where('organization_id', $organization->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Get the number of PDF exports this month.
     */
    private function getPdfExportsThisMonth(Organization $organization): int
    {
        return \App\Models\Invoice::where('organization_id', $organization->id)
            ->whereNotNull('pdf_generated_at')
            ->whereMonth('pdf_generated_at', now()->month)
            ->whereYear('pdf_generated_at', now()->year)
            ->count();
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizationMiddleware
{
    /**
     * Handle an incoming request.
     * Ensures that the authenticated user's organization is active
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        
        $organization = $user->organization;
        
        if (!$organization || $organization->status !== 'active') {
            return response()->json([
                'error' => 'Organization is not active',
                'status' => $organization->status ?? 'none'
            ], 403);
        }
        
        // Check if subscription is expired
        if ($organization->subscription_expires_at && 
            $organization->subscription_expires_at->isPast()) {
            return response()->json([
                'error' => 'Subscription has expired',
                'expired_at' => $organization->subscription_expires_at->toIso8601String()
            ], 403);
        }
        
        // Attach organization to request for easy access
        $request->merge(['organization' => $organization]);
        
        return $next($request);
    }
}

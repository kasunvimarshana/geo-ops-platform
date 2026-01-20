<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OrganizationIsolation
{
    /**
     * Handle an incoming request.
     * Ensures that users can only access data from their own organization
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get authenticated user
        $user = auth()->user();

        // If user is not authenticated, let auth middleware handle it
        if (!$user) {
            return $next($request);
        }

        // If user doesn't have an organization, they can't access multi-tenant resources
        if (!$user->organization_id) {
            return response()->json([
                'error' => 'User must belong to an organization to access this resource'
            ], 403);
        }

        // Store organization_id in request for easy access in controllers
        $request->merge(['organization_id' => $user->organization_id]);

        // Validate route model parameters belong to user's organization
        $route = $request->route();
        if ($route) {
            foreach ($route->parameters() as $parameter) {
                // Check if parameter is a model with organization_id
                if (is_object($parameter) && method_exists($parameter, 'getAttribute')) {
                    $modelOrganizationId = $parameter->getAttribute('organization_id');
                    
                    // If model has organization_id and it doesn't match user's organization
                    if ($modelOrganizationId !== null && $modelOrganizationId !== $user->organization_id) {
                        return response()->json([
                            'error' => 'Access denied to resources from other organizations'
                        ], 403);
                    }
                }
            }
        }

        return $next($request);
    }
}

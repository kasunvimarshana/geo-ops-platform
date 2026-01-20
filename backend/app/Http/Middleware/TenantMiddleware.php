<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Organization;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Extract the organization identifier from the request (e.g., subdomain or header)
        $organizationId = $request->header('X-Organization-ID');

        // Validate the organization ID
        if (!$organizationId || !Organization::where('id', $organizationId)->exists()) {
            return response()->json(['error' => 'Organization not found.'], 404);
        }

        // Set the organization context for the request
        app()->instance('currentOrganization', Organization::find($organizationId));

        return $next($request);
    }
}
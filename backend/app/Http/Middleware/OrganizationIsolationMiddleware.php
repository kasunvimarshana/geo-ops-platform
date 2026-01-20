<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Organization Isolation Middleware
 *
 * Ensures users can only access resources within their organization.
 */
class OrganizationIsolationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return $this->forbiddenResponse('User not authenticated.');
        }

        // Store the user's organization_id for query scoping
        $request->attributes->set('organization_id', $user->organization_id);

        return $next($request);
    }

    /**
     * Return a forbidden JSON response.
     *
     * @param string $message
     * @return Response
     */
    protected function forbiddenResponse(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], Response::HTTP_FORBIDDEN);
    }
}

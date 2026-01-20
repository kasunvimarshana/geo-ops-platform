<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role Authorization Middleware
 * 
 * Checks if authenticated user has required role(s).
 */
class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        // Check if user has any of the required roles
        $userRole = $user->role->name;
        
        if (!in_array($userRole, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to access this resource',
            ], 403);
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role-Based Authorization Middleware
 *
 * Restricts access to routes based on user roles.
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
            return $this->forbiddenResponse('User not authenticated.');
        }

        if (!in_array($user->role, $roles)) {
            return $this->forbiddenResponse('You do not have permission to access this resource.');
        }

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

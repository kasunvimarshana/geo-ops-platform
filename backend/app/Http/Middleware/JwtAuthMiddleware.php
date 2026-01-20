<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * JWT Authentication Middleware
 *
 * Validates JWT tokens and authenticates users for protected routes.
 */
class JwtAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->unauthorizedResponse('User not found.');
            }

            if (!$user->is_active) {
                return $this->unauthorizedResponse('User account is inactive.');
            }

        } catch (TokenExpiredException $e) {
            return $this->unauthorizedResponse('Token has expired.');
        } catch (TokenInvalidException $e) {
            return $this->unauthorizedResponse('Token is invalid.');
        } catch (JWTException $e) {
            return $this->unauthorizedResponse('Token not provided.');
        } catch (\Exception $e) {
            return $this->unauthorizedResponse('Authentication failed.');
        }

        return $next($request);
    }

    /**
     * Return an unauthorized JSON response.
     *
     * @param string $message
     * @return Response
     */
    protected function unauthorizedResponse(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], Response::HTTP_UNAUTHORIZED);
    }
}

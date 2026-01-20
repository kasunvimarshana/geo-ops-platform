<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests as BaseThrottleRequests;

class ThrottleRequests extends BaseThrottleRequests
{
    /**
     * Resolve the number of attempts if the user is authenticated or not.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int|string  $maxAttempts
     * @return int
     */
    protected function resolveMaxAttempts($request, $maxAttempts)
    {
        if (is_string($maxAttempts) && str_contains($maxAttempts, '|')) {
            $parts = explode('|', $maxAttempts, 2);
            if (count($parts) === 2) {
                $maxAttempts = $parts[$request->user() ? 1 : 0];
            }
        }

        return (int) $maxAttempts;
    }
}

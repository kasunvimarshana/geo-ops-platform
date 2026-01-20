<?php

namespace Tests\Unit;

use App\Http\Middleware\Authenticate;
use Illuminate\Http\Request;
use Tests\TestCase;

class AuthenticateTest extends TestCase
{
    public function test_returns_null_for_json_requests(): void
    {
        $request = Request::create('/api/v1/test', 'GET');
        $request->headers->set('Accept', 'application/json');

        $middleware = new Authenticate(app('Illuminate\Contracts\Auth\Factory'));

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('redirectTo');
        $method->setAccessible(true);

        $result = $method->invoke($middleware, $request);

        $this->assertNull($result);
    }

    public function test_returns_login_route_for_web_requests(): void
    {
        $request = Request::create('/dashboard', 'GET');
        $request->headers->set('Accept', 'text/html');

        $middleware = new Authenticate(app('Illuminate\Contracts\Auth\Factory'));

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('redirectTo');
        $method->setAccessible(true);

        $result = $method->invoke($middleware, $request);

        // Should return /login path (fallback since named route doesn't exist in test)
        $this->assertEquals('/login', $result);
    }
}

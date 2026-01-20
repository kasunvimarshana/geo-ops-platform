<?php

namespace Tests\Unit;

use App\Http\Middleware\ThrottleRequests;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ThrottleRequestsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        RateLimiter::clear('');
        parent::tearDown();
    }

    public function test_resolves_max_attempts_for_authenticated_user(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $organization->id]);

        $request = Request::create('/api/v1/test', 'GET');
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        $middleware = new ThrottleRequests(app('Illuminate\Cache\RateLimiter'));

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('resolveMaxAttempts');
        $method->setAccessible(true);

        // Test format: "unauthenticated|authenticated"
        $result = $method->invoke($middleware, $request, '5|60');
        
        $this->assertEquals(60, $result);
    }

    public function test_resolves_max_attempts_for_unauthenticated_user(): void
    {
        $request = Request::create('/api/v1/test', 'GET');

        $middleware = new ThrottleRequests(app('Illuminate\Cache\RateLimiter'));

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('resolveMaxAttempts');
        $method->setAccessible(true);

        // Test format: "unauthenticated|authenticated"
        $result = $method->invoke($middleware, $request, '5|60');
        
        $this->assertEquals(5, $result);
    }

    public function test_resolves_simple_max_attempts(): void
    {
        $request = Request::create('/api/v1/test', 'GET');

        $middleware = new ThrottleRequests(app('Illuminate\Cache\RateLimiter'));

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('resolveMaxAttempts');
        $method->setAccessible(true);

        // Test simple integer string
        $result = $method->invoke($middleware, $request, '10');
        
        $this->assertEquals(10, $result);
    }

    public function test_resolves_integer_max_attempts(): void
    {
        $request = Request::create('/api/v1/test', 'GET');

        $middleware = new ThrottleRequests(app('Illuminate\Cache\RateLimiter'));

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('resolveMaxAttempts');
        $method->setAccessible(true);

        // Test integer value
        $result = $method->invoke($middleware, $request, 20);
        
        $this->assertEquals(20, $result);
    }

    public function test_handles_malformed_pipe_format(): void
    {
        $request = Request::create('/api/v1/test', 'GET');

        $middleware = new ThrottleRequests(app('Illuminate\Cache\RateLimiter'));

        $reflection = new \ReflectionClass($middleware);
        $method = $reflection->getMethod('resolveMaxAttempts');
        $method->setAccessible(true);

        // Test format with more than one pipe (should be ignored)
        $result = $method->invoke($middleware, $request, '5|10|15');
        
        // Should use the first part for unauthenticated
        $this->assertEquals(5, $result);
    }

    public function test_middleware_allows_requests_under_limit(): void
    {
        RateLimiter::clear('test-key');
        
        $request = Request::create('/api/v1/test', 'GET');
        
        // Attach a route to the request
        $route = new \Illuminate\Routing\Route(['GET'], '/api/v1/test', function () {});
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });
        
        $middleware = new ThrottleRequests(app('Illuminate\Cache\RateLimiter'));

        $passed = false;
        $response = $middleware->handle($request, function ($req) use (&$passed) {
            $passed = true;
            return new Response('OK', 200);
        }, 10, 1); // 10 requests per minute

        $this->assertTrue($passed);
        $this->assertEquals(200, $response->getStatusCode());
    }
}

<?php

namespace Tests\Unit;

use App\Http\Middleware\OrganizationIsolation;
use App\Models\Organization;
use App\Models\User;
use App\Models\Field;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class OrganizationIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_middleware_allows_access_to_own_organization_data(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $field = Field::factory()->create(['organization_id' => $organization->id]);

        $this->actingAs($user, 'api');

        $request = Request::create('/api/v1/fields/' . $field->id, 'GET');
        $middleware = new OrganizationIsolation();

        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_middleware_blocks_access_to_other_organization_data(): void
    {
        $organization1 = Organization::factory()->create();
        $organization2 = Organization::factory()->create();
        
        $user = User::factory()->create(['organization_id' => $organization1->id]);
        $field = Field::factory()->create(['organization_id' => $organization2->id]);

        $this->actingAs($user, 'api');

        $request = Request::create('/api/v1/fields/' . $field->id, 'GET');
        $request->setRouteResolver(function () use ($field) {
            $route = new \Illuminate\Routing\Route('GET', '/api/v1/fields/{field}', []);
            $route->bind($request = Request::create('/'));
            $route->setParameter('field', $field);
            return $route;
        });

        $middleware = new OrganizationIsolation();

        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        // Should return 403 or handle appropriately
        $this->assertNotEquals('OK', $response->getContent());
    }

    public function test_middleware_skips_non_model_routes(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $organization->id]);

        $this->actingAs($user, 'api');

        $request = Request::create('/api/v1/fields', 'GET');
        $middleware = new OrganizationIsolation();

        $response = $middleware->handle($request, function ($req) {
            return new Response('OK', 200);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('OK', $response->getContent());
    }
}

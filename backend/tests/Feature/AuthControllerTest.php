<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $organization = Organization::factory()->create();

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
            'organization_id' => $organization->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'organization_id',
                ],
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
            'organization_id' => $organization->id,
        ]);
    }

    public function test_registration_requires_valid_data(): void
    {
        $response = $this->postJson('/api/v1/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $organization = Organization::factory()->create();

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'Password123',
            'password_confirmation' => 'different',
            'organization_id' => $organization->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_requires_minimum_password_length(): void
    {
        $organization = Organization::factory()->create();

        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'organization_id' => $organization->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_can_login(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123'),
            'organization_id' => $organization->id,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'Password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'organization_id',
                ],
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('Password123'),
            'organization_id' => $organization->id,
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401);
    }

    public function test_login_requires_valid_data(): void
    {
        $response = $this->postJson('/api/v1/auth/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_requires_minimum_password_length(): void
    {
        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'test@example.com',
            'password' => 'short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_user_can_logout(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/v1/auth/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Successfully logged out',
            ]);
    }

    public function test_user_can_get_profile(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $this->actingAs($user, 'api');

        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'organization_id',
            ])
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    public function test_unauthorized_access_to_protected_routes(): void
    {
        $response = $this->getJson('/api/v1/auth/me');

        $response->assertStatus(401);
    }
}

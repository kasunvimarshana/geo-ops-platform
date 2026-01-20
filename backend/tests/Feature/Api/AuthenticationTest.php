<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->organization = Organization::factory()->create();
    }

    /** @test */
    public function user_can_register_with_valid_data()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'organization_name' => 'Test Organization',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'organization_id',
                    'role',
                ],
                'token',
            ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'name' => 'Test User',
        ]);
    }

    /** @test */
    public function user_cannot_register_with_duplicate_email()
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'organization_name' => 'Test Organization',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    /** @test */
    public function user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'organization_id',
                    'role',
                ],
                'token',
            ]);
    }

    /** @test */
    public function user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJsonFragment([
                'message' => 'Invalid credentials',
            ]);
    }

    /** @test */
    public function authenticated_user_can_get_profile()
    {
        $user = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'organization_id',
                'role',
            ])
            ->assertJson([
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_protected_routes()
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_logout()
    {
        $user = User::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $response = $this->actingAs($user)
            ->postJson('/api/auth/logout');

        $response->assertStatus(200)
            ->assertJsonFragment([
                'message' => 'Successfully logged out',
            ]);
    }

    /** @test */
    public function registration_requires_all_fields()
    {
        $response = $this->postJson('/api/auth/register', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    /** @test */
    public function password_must_be_confirmed()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
            'organization_name' => 'Test Organization',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
    }

    /** @test */
    public function email_must_be_valid()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'organization_name' => 'Test Organization',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }
}

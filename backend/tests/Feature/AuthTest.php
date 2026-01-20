<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_register()
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    /** @test */
    public function a_user_can_login()
    {
        $user = \App\Models\User::factory()->create([
            'password' => bcrypt($password = 'password'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200);
        $this->assertArrayHasKey('token', $response->json());
    }

    /** @test */
    public function a_user_can_logout()
    {
        $user = \App\Models\User::factory()->create();
        $token = \auth()->login($user);

        $response = $this->postJson('/api/v1/auth/logout', [], [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Successfully logged out', $response->json('message'));
    }

    /** @test */
    public function a_user_can_get_their_profile()
    {
        $user = \App\Models\User::factory()->create();
        $token = \auth()->login($user);

        $response = $this->getJson('/api/v1/auth/me', [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200);
        $this->assertEquals($user->email, $response->json('email'));
    }
}
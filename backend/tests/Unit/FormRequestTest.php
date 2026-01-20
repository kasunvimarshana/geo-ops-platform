<?php

namespace Tests\Unit;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\StoreFieldRequest;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Validator;

class FormRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_request_validates_required_fields(): void
    {
        $request = new LoginRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_login_request_validates_email_format(): void
    {
        $request = new LoginRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'email' => 'not-an-email',
            'password' => 'password123',
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_login_request_validates_password_min_length(): void
    {
        $request = new LoginRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'email' => 'test@example.com',
            'password' => 'short',
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_login_request_passes_with_valid_data(): void
    {
        $request = new LoginRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'email' => 'test@example.com',
            'password' => 'password123',
        ], $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_register_request_validates_required_fields(): void
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_register_request_validates_email_uniqueness(): void
    {
        $organization = Organization::factory()->create();
        $existingUser = \App\Models\User::factory()->create([
            'email' => 'existing@example.com',
            'organization_id' => $organization->id,
        ]);

        $request = new RegisterRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'organization_id' => $organization->id,
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('email', $validator->errors()->toArray());
    }

    public function test_register_request_validates_password_confirmation(): void
    {
        $organization = Organization::factory()->create();
        $request = new RegisterRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different',
            'organization_id' => $organization->id,
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('password', $validator->errors()->toArray());
    }

    public function test_register_request_validates_organization_exists(): void
    {
        $request = new RegisterRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'organization_id' => 99999, // Non-existent ID
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('organization_id', $validator->errors()->toArray());
    }

    public function test_store_field_request_validates_required_fields(): void
    {
        $request = new StoreFieldRequest();
        $rules = $request->rules();

        $validator = Validator::make([], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('name', $validator->errors()->toArray());
        $this->assertArrayHasKey('boundary', $validator->errors()->toArray());
        $this->assertArrayHasKey('measurement_type', $validator->errors()->toArray());
    }

    public function test_store_field_request_validates_measurement_type_enum(): void
    {
        $request = new StoreFieldRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'name' => 'Test Field',
            'boundary' => json_encode(['type' => 'Polygon', 'coordinates' => [[[0, 0], [1, 0], [1, 1], [0, 1], [0, 0]]]]),
            'area' => 1000.0,
            'perimeter' => 400.0,
            'measurement_type' => 'invalid_type',
        ], $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('measurement_type', $validator->errors()->toArray());
    }

    public function test_store_field_request_passes_with_valid_data(): void
    {
        $request = new StoreFieldRequest();
        $rules = $request->rules();

        $validator = Validator::make([
            'name' => 'Test Field',
            'boundary' => json_encode(['type' => 'Polygon', 'coordinates' => [[[0, 0], [1, 0], [1, 1], [0, 1], [0, 0]]]]),
            'area' => 1000.0,
            'perimeter' => 400.0,
            'measurement_type' => 'walk_around',
        ], $rules);

        $this->assertFalse($validator->fails());
    }
}

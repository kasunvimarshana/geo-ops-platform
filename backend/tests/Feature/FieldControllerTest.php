<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use App\Models\Field;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FieldControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $organization;

    protected function setUp(): void
    {
        parent::setUp();

        // Create organization
        $this->organization = Organization::create([
            'name' => 'Test Farm',
            'type' => 'farm',
            'email' => 'test@farm.com',
        ]);

        // Create user
        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'organization_id' => $this->organization->id,
        ]);
    }

    public function test_can_list_fields()
    {
        $field = Field::create([
            'name' => 'Test Field',
            'location' => 'Test Location',
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'boundary' => json_encode([
                'type' => 'Polygon',
                'coordinates' => [
                    [
                        [80.0, 7.0],
                        [80.1, 7.0],
                        [80.1, 7.1],
                        [80.0, 7.1],
                        [80.0, 7.0]  // Close the polygon
                    ]
                ]
            ]),
            'area' => 10000,
            'perimeter' => 400,
            'measurement_type' => 'walk_around',
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/v1/fields');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'area', 'perimeter']
                ]
            ]);
    }

    public function test_can_create_field()
    {
        $data = [
            'name' => 'New Field',
            'location' => 'New Location',
            'area' => 15000,
            'perimeter' => 500,
            'measurement_type' => 'polygon',
            'crop_type' => 'Rice',
            'boundary' => [
                ['latitude' => 7.0, 'longitude' => 80.0],
                ['latitude' => 7.0, 'longitude' => 80.1],
                ['latitude' => 7.1, 'longitude' => 80.1],
                ['latitude' => 7.1, 'longitude' => 80.0],
            ]
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/fields', $data);

        $response->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'New Field',
                'crop_type' => 'Rice',
            ]);

        $this->assertDatabaseHas('fields', [
            'name' => 'New Field',
            'organization_id' => $this->organization->id,
        ]);
    }

    public function test_can_show_field()
    {
        $field = Field::create([
            'name' => 'Test Field',
            'location' => 'Test Location',
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'boundary' => json_encode([
                'type' => 'Polygon',
                'coordinates' => [
                    [
                        [80.0, 7.0],
                        [80.1, 7.0],
                        [80.1, 7.1],
                        [80.0, 7.1],
                        [80.0, 7.0]  // Close the polygon
                    ]
                ]
            ]),
            'area' => 10000,
            'perimeter' => 400,
            'measurement_type' => 'walk_around',
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/fields/{$field->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $field->id,
                'name' => 'Test Field',
            ]);
    }

    public function test_can_update_field()
    {
        $field = Field::create([
            'name' => 'Test Field',
            'location' => 'Test Location',
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'boundary' => json_encode([
                'type' => 'Polygon',
                'coordinates' => [
                    [
                        [80.0, 7.0],
                        [80.1, 7.0],
                        [80.1, 7.1],
                        [80.0, 7.1],
                        [80.0, 7.0]  // Close the polygon
                    ]
                ]
            ]),
            'area' => 10000,
            'perimeter' => 400,
            'measurement_type' => 'walk_around',
        ]);

        $updateData = [
            'name' => 'Updated Field',
            'crop_type' => 'Wheat',
        ];

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/v1/fields/{$field->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Field updated successfully',
                'field' => [
                    'name' => 'Updated Field',
                    'crop_type' => 'Wheat',
                ]
            ]);

        $this->assertDatabaseHas('fields', [
            'id' => $field->id,
            'name' => 'Updated Field',
        ]);
    }

    public function test_can_delete_field()
    {
        $field = Field::create([
            'name' => 'Test Field',
            'location' => 'Test Location',
            'organization_id' => $this->organization->id,
            'user_id' => $this->user->id,
            'boundary' => json_encode([
                'type' => 'Polygon',
                'coordinates' => [
                    [
                        [80.0, 7.0],
                        [80.1, 7.0],
                        [80.1, 7.1],
                        [80.0, 7.1],
                        [80.0, 7.0]  // Close the polygon
                    ]
                ]
            ]),
            'area' => 10000,
            'perimeter' => 400,
            'measurement_type' => 'walk_around',
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson("/api/v1/fields/{$field->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Field deleted successfully'
            ]);

        $this->assertSoftDeleted('fields', ['id' => $field->id]);
    }

    public function test_validates_required_fields()
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/fields', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Land;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_land()
    {
        $response = $this->postJson('/api/v1/lands', [
            'name' => 'Test Land',
            'coordinates' => '[[0,0],[1,1],[1,0],[0,1]]',
            'area' => 1.0,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('lands', [
            'name' => 'Test Land',
        ]);
    }

    /** @test */
    public function it_can_get_a_land()
    {
        $land = Land::factory()->create();

        $response = $this->getJson("/api/v1/lands/{$land->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $land->id,
                         'name' => $land->name,
                     ],
                 ]);
    }

    /** @test */
    public function it_can_update_a_land()
    {
        $land = Land::factory()->create();

        $response = $this->putJson("/api/v1/lands/{$land->id}", [
            'name' => 'Updated Land',
            'coordinates' => '[[0,0],[2,2],[2,0],[0,2]]',
            'area' => 2.0,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('lands', [
            'name' => 'Updated Land',
        ]);
    }

    /** @test */
    public function it_can_delete_a_land()
    {
        $land = Land::factory()->create();

        $response = $this->deleteJson("/api/v1/lands/{$land->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('lands', [
            'id' => $land->id,
        ]);
    }
}
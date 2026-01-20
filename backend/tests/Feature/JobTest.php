<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('db:seed'); // Seed the database with initial data
    }

    /** @test */
    public function it_can_create_a_job()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/jobs', [
            'title' => 'Plowing',
            'description' => 'Plowing the field',
            'land_id' => 1,
            'driver_id' => 1,
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'data' => [
                         'title' => 'Plowing',
                         'description' => 'Plowing the field',
                     ],
                 ]);

        $this->assertDatabaseHas('jobs', [
            'title' => 'Plowing',
            'description' => 'Plowing the field',
        ]);
    }

    /** @test */
    public function it_can_update_a_job()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $job = Job::factory()->create([
            'title' => 'Plowing',
            'description' => 'Plowing the field',
        ]);

        $response = $this->putJson("/api/v1/jobs/{$job->id}", [
            'title' => 'Updated Plowing',
            'description' => 'Updated description',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'title' => 'Updated Plowing',
                         'description' => 'Updated description',
                     ],
                 ]);

        $this->assertDatabaseHas('jobs', [
            'title' => 'Updated Plowing',
            'description' => 'Updated description',
        ]);
    }

    /** @test */
    public function it_can_delete_a_job()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $job = Job::factory()->create();

        $response = $this->deleteJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('jobs', [
            'id' => $job->id,
        ]);
    }

    /** @test */
    public function it_can_list_jobs()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Job::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/jobs');

        $response->assertStatus(200)
                 ->assertJsonCount(5, 'data');
    }

    /** @test */
    public function it_can_show_a_job()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $job = Job::factory()->create();

        $response = $this->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'data' => [
                         'id' => $job->id,
                         'title' => $job->title,
                         'description' => $job->description,
                     ],
                 ]);
    }
}
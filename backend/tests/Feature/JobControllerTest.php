<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use App\Models\Job;
use App\Models\Field;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobControllerTest extends TestCase
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

    public function test_can_list_jobs()
    {
        $job = Job::create([
            'title' => 'Test Job',
            'description' => 'Test Description',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson('/api/v1/jobs');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'description', 'status', 'priority']
                ]
            ]);
    }

    public function test_can_create_job()
    {
        $data = [
            'title' => 'New Job',
            'description' => 'New job description',
            'status' => 'pending',
            'priority' => 'high',
        ];

        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/jobs', $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Job created successfully',
                'job' => [
                    'title' => 'New Job',
                    'status' => 'pending',
                    'priority' => 'high',
                ]
            ]);

        $this->assertDatabaseHas('jobs', [
            'title' => 'New Job',
            'organization_id' => $this->organization->id,
        ]);
    }

    public function test_can_show_job()
    {
        $job = Job::create([
            'title' => 'Test Job',
            'description' => 'Test Description',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $job->id,
                'title' => 'Test Job',
            ]);
    }

    public function test_can_update_job()
    {
        $job = Job::create([
            'title' => 'Test Job',
            'description' => 'Test Description',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $updateData = [
            'title' => 'Updated Job',
            'status' => 'in_progress',
            'priority' => 'high',
        ];

        $response = $this->actingAs($this->user, 'api')
            ->putJson("/api/v1/jobs/{$job->id}", $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Job updated successfully',
                'job' => [
                    'title' => 'Updated Job',
                    'status' => 'in_progress',
                ]
            ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Updated Job',
            'status' => 'in_progress',
        ]);
    }

    public function test_can_delete_job()
    {
        $job = Job::create([
            'title' => 'Test Job',
            'description' => 'Test Description',
            'organization_id' => $this->organization->id,
            'created_by' => $this->user->id,
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->deleteJson("/api/v1/jobs/{$job->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Job deleted successfully'
            ]);

        $this->assertSoftDeleted('jobs', ['id' => $job->id]);
    }

    public function test_cannot_access_other_organization_jobs()
    {
        // Create another organization and job
        $otherOrg = Organization::create([
            'name' => 'Other Farm',
            'type' => 'farm',
            'email' => 'other@farm.com',
        ]);

        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
            'organization_id' => $otherOrg->id,
        ]);

        $otherJob = Job::create([
            'title' => 'Other Job',
            'description' => 'Other Description',
            'organization_id' => $otherOrg->id,
            'created_by' => $otherUser->id,
            'status' => 'pending',
            'priority' => 'medium',
        ]);

        $response = $this->actingAs($this->user, 'api')
            ->getJson("/api/v1/jobs/{$otherJob->id}");

        $response->assertStatus(404);
    }

    public function test_validates_required_fields()
    {
        $response = $this->actingAs($this->user, 'api')
            ->postJson('/api/v1/jobs', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }
}

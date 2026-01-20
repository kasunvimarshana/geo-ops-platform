<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\JobService;
use App\Models\Job;
use App\Models\Organization;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobServiceTest extends TestCase
{
    use RefreshDatabase;

    private JobService $service;
    private Organization $organization;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new JobService();
        $this->organization = Organization::factory()->create();
        $this->customer = Customer::factory()->create([
            'organization_id' => $this->organization->id,
        ]);
    }

    /** @test */
    public function it_can_create_job()
    {
        $data = [
            'organization_id' => $this->organization->id,
            'customer_id' => $this->customer->id,
            'service_type' => 'Land Plowing',
            'created_by' => 1,
        ];

        $job = $this->service->create($data);

        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals('Land Plowing', $job->service_type);
        $this->assertEquals('pending', $job->status);
        $this->assertEquals($this->customer->id, $job->customer_id);
    }

    /** @test */
    public function it_can_list_jobs_for_organization()
    {
        // Create jobs for this organization
        Job::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
        ]);

        // Create jobs for another organization (should not appear)
        $otherOrg = Organization::factory()->create();
        Job::factory()->count(2)->create([
            'organization_id' => $otherOrg->id,
        ]);

        $jobs = $this->service->getAll($this->organization->id);

        $this->assertCount(3, $jobs);
        $this->assertTrue($jobs->every(function ($job) {
            return $job->organization_id === $this->organization->id;
        }));
    }

    /** @test */
    public function it_can_update_job_status()
    {
        $job = Job::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'pending',
        ]);

        $updated = $this->service->updateStatus($job->id, 'in_progress');

        $this->assertEquals('in_progress', $updated->status);
    }

    /** @test */
    public function it_can_assign_driver_and_machine()
    {
        $job = Job::factory()->create([
            'organization_id' => $this->organization->id,
            'driver_id' => null,
            'machine_id' => null,
        ]);

        $updated = $this->service->assignDriverAndMachine($job->id, 1, 1);

        $this->assertEquals(1, $updated->driver_id);
        $this->assertEquals(1, $updated->machine_id);
    }

    /** @test */
    public function it_follows_correct_status_flow()
    {
        $job = Job::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'pending',
        ]);

        // Test status progression
        $statuses = ['assigned', 'in_progress', 'completed', 'billed', 'paid'];

        foreach ($statuses as $status) {
            $job = $this->service->updateStatus($job->id, $status);
            $this->assertEquals($status, $job->status);
        }
    }

    /** @test */
    public function it_can_update_job()
    {
        $job = Job::factory()->create([
            'organization_id' => $this->organization->id,
            'service_type' => 'Old Service',
        ]);

        $updated = $this->service->update($job->id, [
            'service_type' => 'New Service',
            'notes' => 'Test notes',
        ]);

        $this->assertEquals('New Service', $updated->service_type);
        $this->assertEquals('Test notes', $updated->notes);
    }

    /** @test */
    public function it_can_delete_job()
    {
        $job = Job::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $this->service->delete($job->id);

        $this->assertSoftDeleted('jobs', [
            'id' => $job->id,
        ]);
    }

    /** @test */
    public function it_sets_timestamps_on_status_changes()
    {
        $job = Job::factory()->create([
            'organization_id' => $this->organization->id,
            'status' => 'pending',
        ]);

        // Update to in_progress should set started_at
        $job = $this->service->updateStatus($job->id, 'in_progress');
        $this->assertNotNull($job->started_at);

        // Update to completed should set completed_at
        $job = $this->service->updateStatus($job->id, 'completed');
        $this->assertNotNull($job->completed_at);
    }
}

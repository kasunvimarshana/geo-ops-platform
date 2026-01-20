<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Organization;
use App\Models\Field;
use App\Models\Job;
use App\Models\Subscription;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_has_users(): void
    {
        $organization = Organization::factory()->create();
        $users = User::factory()->count(3)->create([
            'organization_id' => $organization->id,
        ]);

        $this->assertCount(3, $organization->users);
        $this->assertTrue($organization->users->contains($users[0]));
    }

    public function test_organization_has_fields(): void
    {
        $organization = Organization::factory()->create();
        $fields = Field::factory()->count(2)->create([
            'organization_id' => $organization->id,
        ]);

        $this->assertCount(2, $organization->fields);
        $this->assertTrue($organization->fields->contains($fields[0]));
    }

    public function test_user_belongs_to_organization(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $this->assertInstanceOf(Organization::class, $user->organization);
        $this->assertEquals($organization->id, $user->organization->id);
    }

    public function test_user_has_many_fields(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $organization->id,
        ]);
        $fields = Field::factory()->count(3)->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertCount(3, $user->fields);
        $this->assertTrue($user->fields->contains($fields[0]));
    }

    public function test_field_belongs_to_organization(): void
    {
        $organization = Organization::factory()->create();
        $field = Field::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $this->assertInstanceOf(Organization::class, $field->organization);
        $this->assertEquals($organization->id, $field->organization->id);
    }

    public function test_field_belongs_to_user(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $organization->id,
        ]);
        $field = Field::factory()->create([
            'user_id' => $user->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertInstanceOf(User::class, $field->user);
        $this->assertEquals($user->id, $field->user->id);
    }

    public function test_field_has_many_jobs(): void
    {
        $organization = Organization::factory()->create();
        $field = Field::factory()->create([
            'organization_id' => $organization->id,
        ]);
        $jobs = Job::factory()->count(2)->create([
            'field_id' => $field->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertCount(2, $field->jobs);
        $this->assertTrue($field->jobs->contains($jobs[0]));
    }

    public function test_job_belongs_to_organization(): void
    {
        $organization = Organization::factory()->create();
        $job = Job::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $this->assertInstanceOf(Organization::class, $job->organization);
        $this->assertEquals($organization->id, $job->organization->id);
    }

    public function test_job_belongs_to_field(): void
    {
        $organization = Organization::factory()->create();
        $field = Field::factory()->create([
            'organization_id' => $organization->id,
        ]);
        $job = Job::factory()->create([
            'field_id' => $field->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertInstanceOf(Field::class, $job->field);
        $this->assertEquals($field->id, $job->field->id);
    }

    public function test_subscription_belongs_to_organization(): void
    {
        $organization = Organization::factory()->create();
        $subscription = Subscription::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $this->assertInstanceOf(Organization::class, $subscription->organization);
        $this->assertEquals($organization->id, $subscription->organization->id);
    }

    public function test_invoice_belongs_to_organization(): void
    {
        $organization = Organization::factory()->create();
        $invoice = Invoice::factory()->create([
            'organization_id' => $organization->id,
        ]);

        $this->assertInstanceOf(Organization::class, $invoice->organization);
        $this->assertEquals($organization->id, $invoice->organization->id);
    }

    public function test_user_has_fillable_attributes(): void
    {
        $fillable = (new User())->getFillable();
        
        $this->assertContains('name', $fillable);
        $this->assertContains('email', $fillable);
        $this->assertContains('password', $fillable);
        $this->assertContains('organization_id', $fillable);
    }

    public function test_user_hides_password(): void
    {
        $user = User::factory()->create(['password' => 'secret']);
        
        $array = $user->toArray();
        
        $this->assertArrayNotHasKey('password', $array);
    }

    public function test_field_casts_boundary_to_array(): void
    {
        $organization = Organization::factory()->create();
        $boundary = ['type' => 'Polygon', 'coordinates' => [[[0, 0], [1, 0], [1, 1], [0, 0]]]];
        
        $field = Field::factory()->create([
            'organization_id' => $organization->id,
            'boundary' => $boundary,
        ]);

        $field->refresh();

        $this->assertIsArray($field->boundary);
        $this->assertEquals($boundary, $field->boundary);
    }
}

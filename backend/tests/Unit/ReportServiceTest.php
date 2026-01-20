<?php

namespace Tests\Unit;

use App\Models\Field;
use App\Models\Job;
use App\Models\Organization;
use App\Models\User;
use App\Models\Invoice;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ReportService $reportService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportService = new ReportService();
    }

    public function test_generates_field_report(): void
    {
        $organization = Organization::factory()->create([
            'name' => 'Test Org',
            'type' => 'farm',
            'email' => 'org@example.com',
        ]);

        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'organization_id' => $organization->id,
        ]);

        $boundary = [
            'type' => 'Polygon',
            'coordinates' => [[[0, 0], [1, 0], [1, 1], [0, 1], [0, 0]]]
        ];

        $field = Field::factory()->create([
            'name' => 'Test Field',
            'area' => 10000.0, // 1 hectare
            'perimeter' => 400.0,
            'crop_type' => 'Wheat',
            'measurement_type' => 'walk_around',
            'notes' => 'Test notes',
            'boundary' => $boundary,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
        ]);

        $report = $this->reportService->generateFieldReport($field);

        $this->assertEquals('Field Measurement Report', $report['title']);
        $this->assertArrayHasKey('generated_at', $report);
        $this->assertEquals($field->id, $report['field']['id']);
        $this->assertEquals('Test Field', $report['field']['name']);
        $this->assertEquals(1.0, $report['field']['area_ha']); // 10000 m² = 1 ha
        $this->assertEquals(10000.0, $report['field']['area_sqm']);
        $this->assertEquals(0.4, $report['field']['perimeter_km']); // 400 m = 0.4 km
        $this->assertEquals(400.0, $report['field']['perimeter_m']);
        $this->assertEquals('Wheat', $report['field']['crop_type']);
        $this->assertEquals('walk_around', $report['field']['measurement_type']);
        $this->assertEquals('Test notes', $report['field']['notes']);
        $this->assertEquals('Test Org', $report['organization']['name']);
        $this->assertEquals('farm', $report['organization']['type']);
        $this->assertEquals('John Doe', $report['measured_by']['name']);
        $this->assertNotEmpty($report['coordinates']);
        $this->assertEquals(0, $report['jobs_count']);
    }

    public function test_field_report_includes_jobs(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $field = Field::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
        ]);

        $job = Job::factory()->create([
            'field_id' => $field->id,
            'organization_id' => $organization->id,
            'created_by' => $user->id,
            'title' => 'Test Job',
            'status' => 'pending',
            'priority' => 'high',
        ]);

        $report = $this->reportService->generateFieldReport($field);

        $this->assertEquals(1, $report['jobs_count']);
        $this->assertCount(1, $report['jobs']);
        $this->assertEquals('Test Job', $report['jobs'][0]['title']);
        $this->assertEquals('pending', $report['jobs'][0]['status']);
        $this->assertEquals('high', $report['jobs'][0]['priority']);
    }

    public function test_field_report_handles_null_boundary(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create(['organization_id' => $organization->id]);
        $field = Field::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'boundary' => ['type' => 'Polygon', 'coordinates' => [[]]],
        ]);

        $report = $this->reportService->generateFieldReport($field);

        $this->assertEmpty($report['coordinates']);
    }

    public function test_generates_job_report(): void
    {
        $organization = Organization::factory()->create([
            'name' => 'Test Org',
            'type' => 'farm',
        ]);

        $creator = User::factory()->create([
            'name' => 'Creator User',
            'email' => 'creator@example.com',
            'organization_id' => $organization->id,
        ]);

        $assignee = User::factory()->create([
            'name' => 'Assignee User',
            'email' => 'assignee@example.com',
            'organization_id' => $organization->id,
        ]);

        $field = Field::factory()->create([
            'name' => 'Test Field',

            'area' => 50000.0,
            'organization_id' => $organization->id,
            'user_id' => $creator->id,
        ]);

        $job = Job::factory()->create([
            'title' => 'Test Job',
            'description' => 'Test Description',
            'status' => 'in_progress',
            'priority' => 'high',
            'field_id' => $field->id,
            'organization_id' => $organization->id,
            'created_by' => $creator->id,
            'assigned_to' => $assignee->id,
        ]);

        $report = $this->reportService->generateJobReport($job);

        $this->assertEquals('Job Report', $report['title']);
        $this->assertArrayHasKey('generated_at', $report);
        $this->assertEquals($job->id, $report['job']['id']);
        $this->assertEquals('Test Job', $report['job']['title']);
        $this->assertEquals('Test Description', $report['job']['description']);
        $this->assertEquals('in_progress', $report['job']['status']);
        $this->assertEquals('high', $report['job']['priority']);
        $this->assertEquals('Test Org', $report['organization']['name']);
        $this->assertEquals('farm', $report['organization']['type']);
        $this->assertEquals('Test Field', $report['field']['name']);
        $this->assertEquals(5.0, $report['field']['area_ha']); // 50000 m² = 5 ha
        $this->assertEquals('Creator User', $report['creator']['name']);
        $this->assertEquals('Assignee User', $report['assignee']['name']);
    }

    public function test_job_report_handles_null_field(): void
    {
        $organization = Organization::factory()->create();
        $creator = User::factory()->create(['organization_id' => $organization->id]);

        $job = Job::factory()->create([
            'field_id' => null,
            'organization_id' => $organization->id,
            'created_by' => $creator->id,
        ]);

        $report = $this->reportService->generateJobReport($job);

        $this->assertNull($report['field']);
    }

    public function test_job_report_handles_null_assignee(): void
    {
        $organization = Organization::factory()->create();
        $creator = User::factory()->create(['organization_id' => $organization->id]);
        $field = Field::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $creator->id,
        ]);

        $job = Job::factory()->create([
            'field_id' => $field->id,
            'organization_id' => $organization->id,
            'created_by' => $creator->id,
            'assigned_to' => null,
        ]);

        $report = $this->reportService->generateJobReport($job);

        $this->assertNull($report['assignee']);
    }

    public function test_job_report_includes_invoices(): void
    {
        $organization = Organization::factory()->create();
        $creator = User::factory()->create(['organization_id' => $organization->id]);
        $field = Field::factory()->create([
            'organization_id' => $organization->id,
            'user_id' => $creator->id,
        ]);

        $job = Job::factory()->create([
            'field_id' => $field->id,
            'organization_id' => $organization->id,
            'created_by' => $creator->id,
        ]);

        $invoice = Invoice::factory()->create([
            'job_id' => $job->id,
            'organization_id' => $organization->id,
            'invoice_number' => 'INV-001',
            'total' => 1000.00,
            'status' => 'paid',
        ]);

        $report = $this->reportService->generateJobReport($job);

        $this->assertCount(1, $report['invoices']);
        $this->assertEquals('INV-001', $report['invoices'][0]['number']);
        $this->assertEquals(1000.00, $report['invoices'][0]['total']);
        $this->assertEquals('paid', $report['invoices'][0]['status']);
    }

    public function test_formats_report_as_html(): void
    {
        $reportData = [
            'title' => 'Test Report',
            'generated_at' => '2026-01-19 12:00:00',
            'field' => [
                'name' => 'Test Field',

                'area_ha' => 1.5,
                'area_sqm' => 15000,
                'perimeter_km' => 0.5,
                'perimeter_m' => 500,
                'crop_type' => 'Wheat',
                'measurement_type' => 'walk_around',
            ],
            'organization' => [
                'name' => 'Test Org',
                'type' => 'farm',
            ],
        ];

        $html = $this->reportService->formatAsHtml($reportData);

        $this->assertStringContainsString('<html>', $html);
        $this->assertStringContainsString('Test Report', $html);
        $this->assertStringContainsString('Test Field', $html);
        $this->assertStringContainsString('1.5 ha', $html);
        $this->assertStringContainsString('Wheat', $html);
        $this->assertStringContainsString('Test Org', $html);
        $this->assertStringContainsString('</html>', $html);
    }

    public function test_formats_job_report_as_html(): void
    {
        $reportData = [
            'title' => 'Job Report',
            'generated_at' => '2026-01-19 12:00:00',
            'job' => [
                'title' => 'Test Job',
                'description' => 'Test Description',
                'status' => 'pending',
                'priority' => 'high',
                'due_date' => '2026-01-20',
            ],
        ];

        $html = $this->reportService->formatAsHtml($reportData);

        $this->assertStringContainsString('Job Report', $html);
        $this->assertStringContainsString('Test Job', $html);
        $this->assertStringContainsString('Test Description', $html);
        $this->assertStringContainsString('Pending', $html);
        $this->assertStringContainsString('High', $html);
    }

    public function test_formats_report_as_json(): void
    {
        $reportData = [
            'title' => 'Test Report',
            'field' => [
                'name' => 'Test Field',
                'area_ha' => 1.5,
            ],
        ];

        $json = $this->reportService->formatAsJson($reportData);

        $this->assertJson($json);
        $decoded = json_decode($json, true);
        $this->assertEquals('Test Report', $decoded['title']);
        $this->assertEquals('Test Field', $decoded['field']['name']);
        $this->assertEquals(1.5, $decoded['field']['area_ha']);
    }

    public function test_html_escapes_user_input(): void
    {
        $reportData = [
            'title' => 'Test Report',
            'field' => [
                'name' => '<script>alert("xss")</script>',
                'area_ha' => 1.0,
                'area_sqm' => 10000,
                'perimeter_km' => 0.4,
                'perimeter_m' => 400,
                'crop_type' => null,
                'measurement_type' => null,
            ],
            'organization' => [
                'name' => 'Test <> Org',
                'type' => 'farm',
            ],
        ];

        $html = $this->reportService->formatAsHtml($reportData);

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('Test <> Org', $html);
        $this->assertStringContainsString('&lt;&gt;', $html);
    }
}

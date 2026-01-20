<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\LandMeasurementService;
use App\Models\LandMeasurement;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LandMeasurementServiceTest extends TestCase
{
    use RefreshDatabase;

    private LandMeasurementService $service;
    private Organization $organization;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new LandMeasurementService();
        $this->organization = Organization::factory()->create();
    }

    /** @test */
    public function it_can_calculate_area_from_coordinates()
    {
        $coordinates = [
            ['latitude' => 0, 'longitude' => 0],
            ['latitude' => 0, 'longitude' => 1],
            ['latitude' => 1, 'longitude' => 1],
            ['latitude' => 1, 'longitude' => 0],
        ];

        $data = [
            'organization_id' => $this->organization->id,
            'name' => 'Test Field',
            'coordinates' => $coordinates,
        ];

        $measurement = $this->service->create($data);

        $this->assertInstanceOf(LandMeasurement::class, $measurement);
        $this->assertEquals('Test Field', $measurement->name);
        $this->assertNotNull($measurement->area_sqm);
        $this->assertGreaterThan(0, $measurement->area_sqm);
        $this->assertNotNull($measurement->area_acres);
        $this->assertNotNull($measurement->area_hectares);
    }

    /** @test */
    public function it_can_list_measurements_for_organization()
    {
        // Create measurements for this organization
        LandMeasurement::factory()->count(3)->create([
            'organization_id' => $this->organization->id,
        ]);

        // Create measurements for another organization (should not appear)
        $otherOrg = Organization::factory()->create();
        LandMeasurement::factory()->count(2)->create([
            'organization_id' => $otherOrg->id,
        ]);

        $measurements = $this->service->getAll($this->organization->id);

        $this->assertCount(3, $measurements);
        $this->assertTrue($measurements->every(function ($measurement) {
            return $measurement->organization_id === $this->organization->id;
        }));
    }

    /** @test */
    public function it_can_update_measurement()
    {
        $measurement = LandMeasurement::factory()->create([
            'organization_id' => $this->organization->id,
            'name' => 'Old Name',
        ]);

        $updated = $this->service->update($measurement->id, [
            'name' => 'New Name',
        ]);

        $this->assertEquals('New Name', $updated->name);
        $this->assertEquals($measurement->id, $updated->id);
    }

    /** @test */
    public function it_can_delete_measurement()
    {
        $measurement = LandMeasurement::factory()->create([
            'organization_id' => $this->organization->id,
        ]);

        $this->service->delete($measurement->id);

        $this->assertSoftDeleted('land_measurements', [
            'id' => $measurement->id,
        ]);
    }

    /** @test */
    public function it_converts_area_units_correctly()
    {
        $coordinates = [
            ['latitude' => 0, 'longitude' => 0],
            ['latitude' => 0, 'longitude' => 0.001],
            ['latitude' => 0.001, 'longitude' => 0.001],
            ['latitude' => 0.001, 'longitude' => 0],
        ];

        $data = [
            'organization_id' => $this->organization->id,
            'name' => 'Test Conversion',
            'coordinates' => $coordinates,
        ];

        $measurement = $this->service->create($data);

        // Check conversions
        $expectedAcres = $measurement->area_sqm * 0.000247105;
        $expectedHectares = $measurement->area_sqm * 0.0001;

        $this->assertEqualsWithDelta($expectedAcres, $measurement->area_acres, 0.001);
        $this->assertEqualsWithDelta($expectedHectares, $measurement->area_hectares, 0.001);
    }

    /** @test */
    public function it_stores_coordinates_as_polygon()
    {
        $coordinates = [
            ['latitude' => 6.9271, 'longitude' => 79.8612],
            ['latitude' => 6.9272, 'longitude' => 79.8613],
            ['latitude' => 6.9273, 'longitude' => 79.8614],
            ['latitude' => 6.9274, 'longitude' => 79.8615],
        ];

        $data = [
            'organization_id' => $this->organization->id,
            'name' => 'Sri Lanka Test Field',
            'coordinates' => $coordinates,
        ];

        $measurement = $this->service->create($data);

        $this->assertNotNull($measurement->polygon);
        $this->assertIsString($measurement->polygon);
    }
}

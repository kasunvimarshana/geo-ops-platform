<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\LandMeasurementService;
use App\Models\Land;

class LandMeasurementServiceTest extends TestCase
{
    protected $landMeasurementService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->landMeasurementService = new LandMeasurementService();
    }

    public function testCalculateArea()
    {
        $land = new Land();
        $land->coordinates = [
            [0, 0],
            [0, 10],
            [10, 10],
            [10, 0],
        ];

        $area = $this->landMeasurementService->calculateArea($land);
        $this->assertEquals(100, $area);
    }

    public function testStoreLandMeasurement()
    {
        $data = [
            'name' => 'Test Land',
            'coordinates' => [
                [0, 0],
                [0, 10],
                [10, 10],
                [10, 0],
            ],
        ];

        $land = $this->landMeasurementService->storeLandMeasurement($data);
        $this->assertInstanceOf(Land::class, $land);
        $this->assertEquals('Test Land', $land->name);
    }

    public function testUpdateLandMeasurement()
    {
        $land = Land::factory()->create(['name' => 'Old Land']);
        $data = [
            'name' => 'Updated Land',
            'coordinates' => [
                [0, 0],
                [0, 20],
                [20, 20],
                [20, 0],
            ],
        ];

        $updatedLand = $this->landMeasurementService->updateLandMeasurement($land->id, $data);
        $this->assertEquals('Updated Land', $updatedLand->name);
    }

    public function testDeleteLandMeasurement()
    {
        $land = Land::factory()->create();

        $result = $this->landMeasurementService->deleteLandMeasurement($land->id);
        $this->assertTrue($result);
        $this->assertNull(Land::find($land->id));
    }
}
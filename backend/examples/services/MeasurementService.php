<?php

namespace App\Services\Measurement;

use App\Repositories\Contracts\MeasurementRepositoryInterface;
use App\Services\Measurement\AreaCalculationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Measurement Service
 * 
 * Handles GPS land measurement business logic.
 */
class MeasurementService
{
    public function __construct(
        private MeasurementRepositoryInterface $measurementRepository,
        private AreaCalculationService $areaCalculator
    ) {}

    /**
     * List measurements with filters
     * 
     * @param array $filters
     * @param int $perPage
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15)
    {
        return $this->measurementRepository->list($filters, $perPage);
    }

    /**
     * Create new measurement
     * 
     * @param array $data
     * @return \App\Models\Measurement
     * @throws \Exception
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Extract polygon points
            $polygonPoints = $data['polygon_points'];
            unset($data['polygon_points']);

            // Calculate area and other metrics
            $calculations = $this->areaCalculator->calculate($polygonPoints);

            // Merge calculations into data
            $measurementData = array_merge($data, [
                'organization_id' => auth()->user()->organization_id,
                'measured_by' => auth()->id(),
                'area_acres' => $calculations['area_acres'],
                'area_hectares' => $calculations['area_hectares'],
                'perimeter_meters' => $calculations['perimeter_meters'],
                'center_latitude' => $calculations['center_latitude'],
                'center_longitude' => $calculations['center_longitude'],
                'status' => 'completed',
                'created_by' => auth()->id(),
            ]);

            // Create measurement
            $measurement = $this->measurementRepository->create($measurementData);

            // Store polygon points
            $this->storePolygonPoints($measurement->id, $polygonPoints);

            return $measurement->fresh(['polygonPoints', 'measuredBy']);
        });
    }

    /**
     * Find measurement by ID
     * 
     * @param int $id
     * @return \App\Models\Measurement
     * @throws ModelNotFoundException
     */
    public function findById(int $id)
    {
        $measurement = $this->measurementRepository->findById($id);

        if (!$measurement) {
            throw new ModelNotFoundException('Measurement not found');
        }

        // Ensure user can only access their organization's data
        if ($measurement->organization_id !== auth()->user()->organization_id) {
            throw new ModelNotFoundException('Measurement not found');
        }

        return $measurement->load(['polygonPoints', 'measuredBy']);
    }

    /**
     * Update measurement
     * 
     * @param int $id
     * @param array $data
     * @return \App\Models\Measurement
     */
    public function update(int $id, array $data)
    {
        $measurement = $this->findById($id);

        $data['updated_by'] = auth()->id();

        return $this->measurementRepository->update($id, $data);
    }

    /**
     * Delete measurement (soft delete)
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $measurement = $this->findById($id);

        return $this->measurementRepository->delete($id);
    }

    /**
     * Store polygon points for measurement
     * 
     * @param int $measurementId
     * @param array $points
     * @return void
     */
    private function storePolygonPoints(int $measurementId, array $points): void
    {
        $pointsData = [];

        foreach ($points as $index => $point) {
            $pointsData[] = [
                'measurement_id' => $measurementId,
                'point_order' => $index + 1,
                'latitude' => $point['latitude'],
                'longitude' => $point['longitude'],
                'altitude' => $point['altitude'] ?? null,
                'accuracy' => $point['accuracy'] ?? null,
                'timestamp' => $point['timestamp'],
            ];
        }

        DB::table('measurement_polygons')->insert($pointsData);
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\MeasurementRepositoryInterface;
use App\Models\Measurement;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Measurement Repository
 * 
 * Handles all database operations for measurements.
 * Clean Architecture: Repository layer abstracts data access.
 */
class MeasurementRepository implements MeasurementRepositoryInterface
{
    public function __construct(
        private Measurement $model
    ) {}

    /**
     * List measurements with filters and pagination
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()
            ->where('organization_id', auth()->user()->organization_id)
            ->with(['measuredBy:id,name', 'polygonPoints']);

        // Apply filters
        if (!empty($filters['customer_phone'])) {
            $query->where('customer_phone', $filters['customer_phone']);
        }

        if (!empty($filters['measured_by'])) {
            $query->where('measured_by', $filters['measured_by']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['from_date'])) {
            $query->whereDate('measurement_date', '>=', $filters['from_date']);
        }

        if (!empty($filters['to_date'])) {
            $query->whereDate('measurement_date', '<=', $filters['to_date']);
        }

        return $query->latest('measurement_date')->paginate($perPage);
    }

    /**
     * Find measurement by ID
     * 
     * @param int $id
     * @return Measurement|null
     */
    public function findById(int $id): ?Measurement
    {
        return $this->model->find($id);
    }

    /**
     * Create new measurement
     * 
     * @param array $data
     * @return Measurement
     */
    public function create(array $data): Measurement
    {
        return $this->model->create($data);
    }

    /**
     * Update measurement
     * 
     * @param int $id
     * @param array $data
     * @return Measurement
     */
    public function update(int $id, array $data): Measurement
    {
        $measurement = $this->findById($id);
        $measurement->update($data);
        
        return $measurement->fresh();
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
        
        return $measurement->delete();
    }

    /**
     * Find measurements by customer phone
     * 
     * @param string $phone
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findByCustomerPhone(string $phone)
    {
        return $this->model
            ->where('organization_id', auth()->user()->organization_id)
            ->where('customer_phone', $phone)
            ->latest('measurement_date')
            ->get();
    }

    /**
     * Get measurements created by specific user
     * 
     * @param int $userId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByMeasuredBy(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('organization_id', auth()->user()->organization_id)
            ->where('measured_by', $userId)
            ->latest('measurement_date')
            ->paginate($perPage);
    }

    /**
     * Get statistics for organization
     * 
     * @return array
     */
    public function getStatistics(): array
    {
        $organizationId = auth()->user()->organization_id;

        return [
            'total_measurements' => $this->model->where('organization_id', $organizationId)->count(),
            'total_area_acres' => $this->model->where('organization_id', $organizationId)->sum('area_acres'),
            'total_area_hectares' => $this->model->where('organization_id', $organizationId)->sum('area_hectares'),
            'measurements_this_month' => $this->model
                ->where('organization_id', $organizationId)
                ->whereMonth('measurement_date', now()->month)
                ->whereYear('measurement_date', now()->year)
                ->count(),
        ];
    }
}

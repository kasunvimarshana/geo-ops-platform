<?php

namespace App\Repositories\Contracts;

use App\Models\LandMeasurement;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface LandMeasurementRepositoryInterface
{
    public function findById(int $id): ?LandMeasurement;
    
    public function findByOrganization(int $organizationId): Collection;
    
    public function paginate(int $organizationId, int $perPage = 15): LengthAwarePaginator;
    
    public function create(array $data): LandMeasurement;
    
    public function update(LandMeasurement $measurement, array $data): LandMeasurement;
    
    public function delete(LandMeasurement $measurement): bool;
    
    public function findByCustomer(int $customerId): Collection;
    
    public function findByAreaRange(int $organizationId, float $minAcres, float $maxAcres): Collection;
}

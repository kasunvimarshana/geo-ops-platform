<?php

namespace App\Repositories\Contracts;

use App\Models\Measurement;
use Illuminate\Support\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Measurement Repository Interface
 * 
 * Defines the contract for measurement data access operations.
 */
interface MeasurementRepositoryInterface
{
    public function create(array $data): Measurement;
    
    public function update(int $id, array $data): Measurement;
    
    public function delete(int $id): bool;
    
    public function findById(int $id): Measurement;
    
    public function findByOrganization(int $organizationId, array $filters = []): Collection;
    
    public function paginateByOrganization(int $organizationId, array $filters = [], int $perPage = 15): LengthAwarePaginator;
    
    public function findByLand(int $landId): Collection;
    
    public function findByUser(int $userId): Collection;
    
    public function findUnsynced(int $organizationId): Collection;
    
    public function createBatch(array $measurements): Collection;
}

<?php

namespace App\Repositories\Contracts;

use App\Models\Job;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface JobRepositoryInterface
{
    public function findById(int $id): ?Job;
    
    public function findByOrganization(int $organizationId, array $filters = []): Collection;
    
    public function paginate(int $organizationId, int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function create(array $data): Job;
    
    public function update(Job $job, array $data): Job;
    
    public function delete(Job $job): bool;
    
    public function findByStatus(int $organizationId, string $status): Collection;
    
    public function findByDriver(int $driverId): Collection;
    
    public function findByCustomer(int $customerId): Collection;
    
    public function updateStatus(Job $job, string $status): Job;
    
    public function countByStatus(int $organizationId, string $status): int;
}

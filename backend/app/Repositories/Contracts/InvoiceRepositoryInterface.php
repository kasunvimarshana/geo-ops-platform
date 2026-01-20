<?php

namespace App\Repositories\Contracts;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface InvoiceRepositoryInterface
{
    public function findById(int $id): ?Invoice;
    
    public function findByOrganization(int $organizationId, array $filters = []): Collection;
    
    public function paginate(int $organizationId, int $perPage = 15, array $filters = []): LengthAwarePaginator;
    
    public function create(array $data): Invoice;
    
    public function update(Invoice $invoice, array $data): Invoice;
    
    public function delete(Invoice $invoice): bool;
    
    public function findByJob(int $jobId): ?Invoice;
    
    public function findByCustomer(int $customerId): Collection;
    
    public function findByStatus(int $organizationId, string $status): Collection;
    
    public function updateStatus(Invoice $invoice, string $status): Invoice;
    
    public function getTotalAmountByStatus(int $organizationId, string $status): float;
}

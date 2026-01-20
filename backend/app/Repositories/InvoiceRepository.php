<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function __construct(
        protected Invoice $model
    ) {}

    public function findById(int $id): ?Invoice
    {
        return $this->model->with(['job', 'customer'])->find($id);
    }

    public function findByOrganization(int $organizationId, array $filters = []): Collection
    {
        $query = $this->model
            ->where('organization_id', $organizationId)
            ->with(['job', 'customer']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('invoice_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('invoice_date', '<=', $filters['date_to']);
        }

        return $query->latest('invoice_date')->get();
    }

    public function paginate(int $organizationId, int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = $this->model
            ->where('organization_id', $organizationId)
            ->with(['job', 'customer']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('invoice_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('invoice_date', '<=', $filters['date_to']);
        }

        return $query->latest('invoice_date')->paginate($perPage);
    }

    public function create(array $data): Invoice
    {
        return $this->model->create($data);
    }

    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update($data);
        return $invoice->fresh(['job', 'customer']);
    }

    public function delete(Invoice $invoice): bool
    {
        return $invoice->delete();
    }

    public function findByJob(int $jobId): ?Invoice
    {
        return $this->model
            ->where('job_id', $jobId)
            ->with(['customer'])
            ->first();
    }

    public function findByCustomer(int $customerId): Collection
    {
        return $this->model
            ->where('customer_id', $customerId)
            ->with(['job'])
            ->latest('invoice_date')
            ->get();
    }

    public function findByStatus(int $organizationId, string $status): Collection
    {
        return $this->model
            ->where('organization_id', $organizationId)
            ->where('status', $status)
            ->with(['job', 'customer'])
            ->latest('invoice_date')
            ->get();
    }

    public function updateStatus(Invoice $invoice, string $status): Invoice
    {
        $invoice->update(['status' => $status]);
        return $invoice->fresh(['job', 'customer']);
    }

    public function getTotalAmountByStatus(int $organizationId, string $status): float
    {
        return $this->model
            ->where('organization_id', $organizationId)
            ->where('status', $status)
            ->sum('total_amount');
    }
}

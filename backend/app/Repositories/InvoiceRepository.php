<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function create(array $data): object
    {
        return Invoice::create($data);
    }

    public function findById(int $id): ?object
    {
        return Invoice::with(['job', 'land', 'payments'])->find($id);
    }

    public function findByIdAndOrganization(int $id, int $organizationId): ?object
    {
        return Invoice::with(['job', 'land', 'payments'])
            ->where('id', $id)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function findByOrganization(int $organizationId, array $filters = []): object
    {
        $query = Invoice::with(['job', 'land', 'payments'])
            ->where('organization_id', $organizationId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('invoice_number', 'like', "%{$filters['search']}%")
                    ->orWhere('customer_name', 'like', "%{$filters['search']}%");
            });
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('invoice_date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('invoice_date', '<=', $filters['to_date']);
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('invoice_date', 'desc')->paginate($perPage);
    }

    public function update(int $id, array $data): bool
    {
        $invoice = Invoice::find($id);
        return $invoice ? $invoice->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $invoice = Invoice::find($id);
        return $invoice ? $invoice->delete() : false;
    }

    public function findByOfflineId(string $offlineId, int $organizationId): ?object
    {
        return Invoice::where('offline_id', $offlineId)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function getPendingSync(int $organizationId): array
    {
        return Invoice::where('organization_id', $organizationId)
            ->where('sync_status', 'pending')
            ->get()
            ->toArray();
    }

    public function getNextInvoiceNumber(int $organizationId): string
    {
        $lastInvoice = Invoice::where('organization_id', $organizationId)
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastInvoice) {
            return 'INV-' . date('Y') . '-0001';
        }

        preg_match('/INV-\d{4}-(\d+)/', $lastInvoice->invoice_number, $matches);
        $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;

        return 'INV-' . date('Y') . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function findUnpaid(int $organizationId): array
    {
        return Invoice::where('organization_id', $organizationId)
            ->where('balance', '>', 0)
            ->orderBy('due_date', 'asc')
            ->get()
            ->toArray();
    }
}

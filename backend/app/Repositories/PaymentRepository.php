<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Repositories\Interfaces\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function create(array $data): object
    {
        return Payment::create($data);
    }

    public function findById(int $id): ?object
    {
        return Payment::with(['invoice', 'receiver'])->find($id);
    }

    public function findByIdAndOrganization(int $id, int $organizationId): ?object
    {
        return Payment::with(['invoice', 'receiver'])
            ->where('id', $id)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function findByOrganization(int $organizationId, array $filters = []): object
    {
        $query = Payment::with(['invoice', 'receiver'])
            ->where('organization_id', $organizationId);

        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (isset($filters['invoice_id'])) {
            $query->where('invoice_id', $filters['invoice_id']);
        }

        if (isset($filters['from_date'])) {
            $query->whereDate('payment_date', '>=', $filters['from_date']);
        }

        if (isset($filters['to_date'])) {
            $query->whereDate('payment_date', '<=', $filters['to_date']);
        }

        $perPage = $filters['per_page'] ?? 15;
        return $query->orderBy('payment_date', 'desc')->paginate($perPage);
    }

    public function update(int $id, array $data): bool
    {
        $payment = Payment::find($id);
        return $payment ? $payment->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $payment = Payment::find($id);
        return $payment ? $payment->delete() : false;
    }

    public function findByOfflineId(string $offlineId, int $organizationId): ?object
    {
        return Payment::where('offline_id', $offlineId)
            ->where('organization_id', $organizationId)
            ->first();
    }

    public function getPendingSync(int $organizationId): array
    {
        return Payment::where('organization_id', $organizationId)
            ->where('sync_status', 'pending')
            ->get()
            ->toArray();
    }

    public function findByInvoice(int $invoiceId): array
    {
        return Payment::where('invoice_id', $invoiceId)
            ->orderBy('payment_date', 'desc')
            ->get()
            ->toArray();
    }
}

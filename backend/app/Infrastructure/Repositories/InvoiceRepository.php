<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\InvoiceRepositoryInterface;
use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function findById(int $id): ?Invoice
    {
        return Invoice::with(['organization', 'fieldJob', 'payments'])->find($id);
    }

    public function findByInvoiceNumber(string $invoiceNumber): ?Invoice
    {
        return Invoice::where('invoice_number', $invoiceNumber)
            ->with(['organization', 'fieldJob', 'payments'])
            ->first();
    }

    public function paginateByOrganization(int $organizationId, int $perPage = 15): LengthAwarePaginator
    {
        return Invoice::organization($organizationId)
            ->with(['fieldJob', 'payments'])
            ->orderBy('issued_at', 'desc')
            ->paginate($perPage);
    }

    public function create(array $data): Invoice
    {
        if (!isset($data['invoice_number'])) {
            $data['invoice_number'] = $this->generateInvoiceNumber();
        }
        return Invoice::create($data);
    }

    public function update(int $id, array $data): Invoice
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->update($data);
        return $invoice->fresh(['organization', 'fieldJob', 'payments']);
    }

    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        
        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        // Extract sequence number from invoice format: PREFIX-YYMM-0001
        $sequence = 1;
        if ($lastInvoice && preg_match('/-(\d{4})$/', $lastInvoice->invoice_number, $matches)) {
            $sequence = ((int)$matches[1]) + 1;
        }
        
        return sprintf('%s-%s%s-%04d', $prefix, $year, $month, $sequence);
    }
}

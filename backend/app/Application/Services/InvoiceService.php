<?php

declare(strict_types=1);

namespace App\Application\Services;

use App\Domain\Repositories\InvoiceRepositoryInterface;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function __construct(
        private readonly InvoiceRepositoryInterface $repository
    ) {}

    public function getById(int $id): ?Invoice
    {
        return $this->repository->findById($id);
    }

    public function getAllByOrganization(int $organizationId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->paginateByOrganization($organizationId, $perPage);
    }

    public function create(array $data): Invoice
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Invoice
    {
        return $this->repository->update($id, $data);
    }

    public function generatePdf(int $invoiceId): string
    {
        $invoice = $this->repository->findById($invoiceId);
        
        if (!$invoice) {
            throw new \Exception('Invoice not found');
        }

        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $invoice]);
        
        $filename = "invoices/{$invoice->invoice_number}.pdf";
        Storage::disk('public')->put($filename, $pdf->output());
        
        $pdfUrl = Storage::disk('public')->url($filename);
        
        $this->repository->update($invoiceId, ['pdf_url' => $pdfUrl]);
        
        return $pdfUrl;
    }

    public function downloadPdf(int $invoiceId): \Illuminate\Http\Response
    {
        $invoice = $this->repository->findById($invoiceId);
        
        if (!$invoice) {
            throw new \Exception('Invoice not found');
        }

        $pdf = Pdf::loadView('invoices.pdf', ['invoice' => $invoice]);
        
        return $pdf->download("{$invoice->invoice_number}.pdf");
    }

    public function markAsPaid(int $invoiceId): Invoice
    {
        return $this->repository->update($invoiceId, [
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function markAsOverdue(int $invoiceId): Invoice
    {
        return $this->repository->update($invoiceId, [
            'status' => 'overdue',
        ]);
    }
}

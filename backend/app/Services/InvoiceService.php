<?php

namespace App\Services;

use App\Repositories\InvoiceRepository;
use App\DTOs\InvoiceDTO;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    protected $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function createInvoice(InvoiceDTO $invoiceDTO): Invoice
    {
        return DB::transaction(function () use ($invoiceDTO) {
            $invoice = $this->invoiceRepository->create($invoiceDTO);
            // Additional logic for invoice creation can be added here
            return $invoice;
        });
    }

    public function getInvoiceById(int $id): ?Invoice
    {
        return $this->invoiceRepository->findById($id);
    }

    public function updateInvoice(int $id, InvoiceDTO $invoiceDTO): bool
    {
        return DB::transaction(function () use ($id, $invoiceDTO) {
            return $this->invoiceRepository->update($id, $invoiceDTO);
        });
    }

    public function deleteInvoice(int $id): bool
    {
        return $this->invoiceRepository->delete($id);
    }

    public function generateInvoicePDF(int $id): string
    {
        // Logic to generate PDF for the invoice
        // This could involve a job dispatch to handle PDF generation
        return 'PDF generated for invoice ID: ' . $id;
    }

    public function sendInvoiceEmail(int $id): bool
    {
        // Logic to send invoice email
        // This could involve a job dispatch to handle email sending
        return true;
    }
}
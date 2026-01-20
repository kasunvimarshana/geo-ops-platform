<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class GenerateInvoicePDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoiceId;

    public function __construct($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    public function handle(InvoiceService $invoiceService)
    {
        $invoice = Invoice::findOrFail($this->invoiceId);
        $pdf = $invoiceService->generatePDF($invoice);

        $filePath = 'invoices/' . $invoice->id . '.pdf';
        Storage::put($filePath, $pdf->output());

        $invoice->pdf_path = $filePath;
        $invoice->save();
    }
}
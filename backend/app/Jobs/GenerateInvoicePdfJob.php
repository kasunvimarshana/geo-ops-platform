<?php

namespace App\Jobs;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateInvoicePdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Invoice $invoice
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Generating PDF for invoice', ['invoice_id' => $this->invoice->id]);

            // Load relationships
            $this->invoice->load(['job.customer', 'job.landMeasurement', 'customer', 'organization']);

            // Generate PDF
            $pdf = Pdf::loadView('invoices.pdf', [
                'invoice' => $this->invoice,
                'job' => $this->invoice->job,
                'customer' => $this->invoice->customer,
                'organization' => $this->invoice->organization,
            ]);

            // Save PDF to storage (sanitize invoice number to prevent path traversal)
            $sanitizedInvoiceNumber = preg_replace('/[^a-zA-Z0-9_-]/', '_', $this->invoice->invoice_number);
            $filename = 'invoices/invoice_' . $sanitizedInvoiceNumber . '_' . time() . '.pdf';
            Storage::disk('public')->put($filename, $pdf->output());

            // Update invoice with PDF path
            $this->invoice->update([
                'pdf_path' => $filename,
            ]);

            Log::info('PDF generated successfully', [
                'invoice_id' => $this->invoice->id,
                'pdf_path' => $filename,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate invoice PDF', [
                'invoice_id' => $this->invoice->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateInvoicePdfJob failed permanently', [
            'invoice_id' => $this->invoice->id,
            'error' => $exception->getMessage(),
        ]);
    }
}

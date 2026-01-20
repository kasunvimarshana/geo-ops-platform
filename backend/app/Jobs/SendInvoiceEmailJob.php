<?php

namespace App\Jobs;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

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
            Log::info('Sending invoice email', [
                'invoice_id' => $this->invoice->id,
                'customer_email' => $this->invoice->customer->email,
            ]);

            // Load relationships
            $this->invoice->load(['job', 'customer', 'organization']);

            // Send email
            Mail::to($this->invoice->customer->email)
                ->send(new InvoiceMail($this->invoice));

            Log::info('Invoice email sent successfully', [
                'invoice_id' => $this->invoice->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send invoice email', [
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
        Log::error('SendInvoiceEmailJob failed permanently', [
            'invoice_id' => $this->invoice->id,
            'error' => $exception->getMessage(),
        ]);
    }
}

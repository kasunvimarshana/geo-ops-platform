<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Payment $payment
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            DB::transaction(function () {
                Log::info('Processing payment', [
                    'payment_id' => $this->payment->id,
                    'invoice_id' => $this->payment->invoice_id,
                ]);

                // Load invoice
                $invoice = Invoice::findOrFail($this->payment->invoice_id);

                // Calculate total paid amount for this invoice
                $totalPaid = Payment::where('invoice_id', $invoice->id)->sum('amount');

                // Update invoice status based on payment
                if ($totalPaid >= $invoice->total_amount) {
                    $invoice->update(['status' => 'paid']);
                    Log::info('Invoice fully paid', ['invoice_id' => $invoice->id]);
                } elseif ($totalPaid > 0) {
                    $invoice->update(['status' => 'partial']);
                    Log::info('Invoice partially paid', [
                        'invoice_id' => $invoice->id,
                        'paid' => $totalPaid,
                        'total' => $invoice->total_amount,
                    ]);
                }

                // Update job status if invoice is paid
                if ($invoice->status === 'paid' && $invoice->job) {
                    $invoice->job->update(['status' => 'paid']);
                    Log::info('Job marked as paid', ['job_id' => $invoice->job->id]);
                }

                Log::info('Payment processed successfully', [
                    'payment_id' => $this->payment->id,
                ]);
            });
        } catch (\Exception $e) {
            Log::error('Failed to process payment', [
                'payment_id' => $this->payment->id,
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
        Log::error('ProcessPaymentJob failed permanently', [
            'payment_id' => $this->payment->id,
            'error' => $exception->getMessage(),
        ]);
    }
}

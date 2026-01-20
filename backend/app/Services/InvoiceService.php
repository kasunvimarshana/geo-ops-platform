<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Models\Invoice;
use App\Models\Job;
use App\Models\LandMeasurement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    /**
     * Generate invoice from a job
     */
    public function generateFromJob(Job $job, array $data): Invoice
    {
        return DB::transaction(function () use ($job, $data) {
            $job->load(['landMeasurement', 'customer', 'organization']);
            
            // Calculate amounts
            $ratePerUnit = $data['rate_per_unit'] ?? $job->organization->settings['default_rate'] ?? 5000;
            $area = $job->landMeasurement->area_hectares ?? 0;
            $subtotal = $area * $ratePerUnit;
            $tax = $subtotal * ($data['tax_percentage'] ?? 0) / 100;
            $total = $subtotal + $tax;
            
            // Generate invoice number
            $invoiceNumber = $this->generateInvoiceNumber($job->organization_id);
            
            $invoice = Invoice::create([
                'organization_id' => $job->organization_id,
                'customer_id' => $job->customer_id,
                'job_id' => $job->id,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'status' => Invoice::STATUS_DRAFT,
                'issued_at' => $data['issued_at'] ?? now(),
                'due_at' => $data['due_at'] ?? now()->addDays(30),
            ]);
            
            // Update job invoice_generated flag
            $job->update(['invoice_generated' => true]);
            
            return $invoice;
        });
    }
    
    /**
     * Create invoice manually
     */
    public function create(array $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $invoiceNumber = $this->generateInvoiceNumber($data['organization_id']);
            
            return Invoice::create([
                'organization_id' => $data['organization_id'],
                'customer_id' => $data['customer_id'],
                'job_id' => $data['job_id'] ?? null,
                'invoice_number' => $invoiceNumber,
                'subtotal' => $data['subtotal'],
                'tax' => $data['tax'] ?? 0,
                'total' => $data['total'],
                'status' => $data['status'] ?? Invoice::STATUS_DRAFT,
                'issued_at' => $data['issued_at'] ?? now(),
                'due_at' => $data['due_at'] ?? now()->addDays(30),
            ]);
        });
    }
    
    /**
     * Update invoice
     */
    public function update(Invoice $invoice, array $data): Invoice
    {
        $invoice->update([
            'subtotal' => $data['subtotal'] ?? $invoice->subtotal,
            'tax' => $data['tax'] ?? $invoice->tax,
            'total' => $data['total'] ?? $invoice->total,
            'status' => $data['status'] ?? $invoice->status,
            'issued_at' => $data['issued_at'] ?? $invoice->issued_at,
            'due_at' => $data['due_at'] ?? $invoice->due_at,
        ]);
        
        return $invoice->fresh();
    }
    
    /**
     * Update invoice status
     */
    public function updateStatus(Invoice $invoice, string $status): Invoice
    {
        $validStatuses = [
            Invoice::STATUS_DRAFT,
            Invoice::STATUS_SENT,
            Invoice::STATUS_PAID,
            Invoice::STATUS_OVERDUE,
            Invoice::STATUS_CANCELLED,
        ];
        
        if (!in_array($status, $validStatuses)) {
            throw new \InvalidArgumentException("Invalid status: {$status}");
        }
        
        $invoice->update([
            'status' => $status,
            'paid_at' => $status === Invoice::STATUS_PAID ? now() : null,
        ]);
        
        return $invoice->fresh();
    }
    
    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Invoice $invoice): Invoice
    {
        return $this->updateStatus($invoice, Invoice::STATUS_PAID);
    }
    
    /**
     * Generate PDF for invoice
     */
    public function generatePdf(Invoice $invoice): string
    {
        $invoice->load(['organization', 'customer', 'job.landMeasurement', 'job.driver', 'job.machine']);
        
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'organization' => $invoice->organization,
            'customer' => $invoice->customer,
            'job' => $invoice->job,
        ])->setPaper('a4');
        
        // Generate filename
        $filename = "invoice_{$invoice->invoice_number}.pdf";
        $path = "invoices/{$invoice->organization_id}/{$filename}";
        
        // Store PDF
        Storage::disk('public')->put($path, $pdf->output());
        
        return $path;
    }
    
    /**
     * Get invoice PDF content
     */
    public function getPdfContent(Invoice $invoice): string
    {
        $invoice->load(['organization', 'customer', 'job.landMeasurement', 'job.driver', 'job.machine']);
        
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'organization' => $invoice->organization,
            'customer' => $invoice->customer,
            'job' => $invoice->job,
        ])->setPaper('a4');
        
        return $pdf->output();
    }
    
    /**
     * Send invoice via email
     */
    public function sendEmail(Invoice $invoice): bool
    {
        try {
            // Load necessary relationships
            $invoice->load(['organization', 'customer']);
            
            // Validate customer has email
            if (empty($invoice->customer->email)) {
                Log::warning("Cannot send invoice {$invoice->invoice_number}: Customer has no email");
                return false;
            }
            
            // Generate PDF
            $pdfPath = $this->generatePdf($invoice);
            
            // Send email with invoice attachment
            Mail::to($invoice->customer->email)->send(new InvoiceMail($invoice, $pdfPath));
            
            // Update status to sent
            $this->updateStatus($invoice, Invoice::STATUS_SENT);
            
            Log::info("Invoice {$invoice->invoice_number} sent successfully to {$invoice->customer->email}");
            
            return true;
        } catch (\Illuminate\Mail\Exceptions\MailException $e) {
            Log::error("Failed to send invoice {$invoice->invoice_number}: Mail error - " . $e->getMessage());
            throw $e;
        } catch (\InvalidArgumentException $e) {
            Log::error("Failed to send invoice {$invoice->invoice_number}: Invalid data - " . $e->getMessage());
            throw $e;
        } catch (\Exception $e) {
            Log::error("Failed to send invoice {$invoice->invoice_number}: Unexpected error - " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Delete invoice
     */
    public function delete(Invoice $invoice): bool
    {
        return $invoice->delete();
    }
    
    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber(int $organizationId): string
    {
        $prefix = 'INV';
        $year = date('Y');
        $month = date('m');
        
        // Get last invoice number for this organization
        $lastInvoice = Invoice::where('organization_id', $organizationId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;
        
        return sprintf('%s-%s-%s-%04d', $prefix, $year, $month, $sequence);
    }
    
    /**
     * Calculate balance remaining on invoice
     */
    public function calculateBalance(Invoice $invoice): float
    {
        $totalPaid = $invoice->payments()->sum('amount');
        return max(0, $invoice->total - $totalPaid);
    }
    
    /**
     * Check if invoice is overdue
     */
    public function isOverdue(Invoice $invoice): bool
    {
        return $invoice->status !== Invoice::STATUS_PAID 
            && $invoice->due_at 
            && $invoice->due_at->isPast();
    }
    
    /**
     * Get invoices summary for organization
     */
    public function getSummary(int $organizationId): array
    {
        $invoices = Invoice::forOrganization($organizationId)->get();
        
        return [
            'total_count' => $invoices->count(),
            'draft_count' => $invoices->where('status', Invoice::STATUS_DRAFT)->count(),
            'sent_count' => $invoices->where('status', Invoice::STATUS_SENT)->count(),
            'paid_count' => $invoices->where('status', Invoice::STATUS_PAID)->count(),
            'overdue_count' => $invoices->where('status', Invoice::STATUS_OVERDUE)->count(),
            'total_amount' => $invoices->sum('total'),
            'paid_amount' => $invoices->where('status', Invoice::STATUS_PAID)->sum('total'),
            'outstanding_amount' => $invoices->whereIn('status', [
                Invoice::STATUS_SENT, 
                Invoice::STATUS_OVERDUE
            ])->sum('total'),
        ];
    }
}

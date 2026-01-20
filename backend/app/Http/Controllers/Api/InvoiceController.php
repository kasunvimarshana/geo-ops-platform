<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Job;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService
    ) {}

    /**
     * Get all invoices for the authenticated user's organization
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = Invoice::forOrganization($user->organization_id)
            ->with(['customer', 'job'])
            ->orderBy('created_at', 'desc');
        
        // Filter by status
        if ($request->has('status')) {
            $query->byStatus($request->status);
        }
        
        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('issued_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date')) {
            $query->whereDate('issued_at', '<=', $request->to_date);
        }
        
        $perPage = $request->input('per_page', 15);
        $invoices = $query->paginate($perPage);
        
        return response()->json($invoices);
    }

    /**
     * Get a single invoice
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $invoice = Invoice::forOrganization($user->organization_id)
            ->with(['customer', 'job.landMeasurement', 'job.driver', 'job.machine', 'payments'])
            ->findOrFail($id);
        
        $balance = $this->invoiceService->calculateBalance($invoice);
        
        return response()->json([
            'invoice' => $invoice,
            'balance' => $balance,
            'is_overdue' => $this->invoiceService->isOverdue($invoice),
        ]);
    }

    /**
     * Create a new invoice manually
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'job_id' => 'nullable|exists:jobs,id',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'issued_at' => 'nullable|date',
            'due_at' => 'nullable|date|after:issued_at',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $data = $validator->validated();
        $data['organization_id'] = $user->organization_id;
        
        $invoice = $this->invoiceService->create($data);
        
        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice' => $invoice->load(['customer', 'job'])
        ], 201);
    }

    /**
     * Generate invoice from a job
     */
    public function generateFromJob(Request $request, int $jobId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rate_per_unit' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'issued_at' => 'nullable|date',
            'due_at' => 'nullable|date|after:issued_at',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $job = Job::where('organization_id', $user->organization_id)
            ->findOrFail($jobId);
        
        // Check if invoice already exists for this job
        if ($job->invoice_generated) {
            return response()->json([
                'message' => 'Invoice already exists for this job'
            ], 409);
        }
        
        $invoice = $this->invoiceService->generateFromJob($job, $validator->validated());
        
        return response()->json([
            'message' => 'Invoice generated successfully',
            'invoice' => $invoice->load(['customer', 'job'])
        ], 201);
    }

    /**
     * Update an existing invoice
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subtotal' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:draft,sent,paid,overdue,cancelled',
            'issued_at' => 'nullable|date',
            'due_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $invoice = Invoice::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        // Don't allow editing paid invoices
        if ($invoice->status === Invoice::STATUS_PAID) {
            return response()->json([
                'message' => 'Cannot edit paid invoices'
            ], 403);
        }
        
        $invoice = $this->invoiceService->update($invoice, $validator->validated());
        
        return response()->json([
            'message' => 'Invoice updated successfully',
            'invoice' => $invoice->load(['customer', 'job'])
        ]);
    }

    /**
     * Update invoice status
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:draft,sent,paid,overdue,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $invoice = Invoice::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        $invoice = $this->invoiceService->updateStatus($invoice, $request->status);
        
        return response()->json([
            'message' => 'Invoice status updated successfully',
            'invoice' => $invoice
        ]);
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $invoice = Invoice::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        $invoice = $this->invoiceService->markAsPaid($invoice);
        
        return response()->json([
            'message' => 'Invoice marked as paid',
            'invoice' => $invoice
        ]);
    }

    /**
     * Generate and download PDF for invoice
     */
    public function downloadPdf(Request $request, int $id)
    {
        $user = $request->user();
        
        $invoice = Invoice::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        $pdfContent = $this->invoiceService->getPdfContent($invoice);
        
        $filename = "invoice_{$invoice->invoice_number}.pdf";
        
        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Send invoice via email
     */
    public function sendEmail(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $invoice = Invoice::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        if (!$invoice->customer->email) {
            return response()->json([
                'message' => 'Customer does not have an email address'
            ], 400);
        }
        
        $this->invoiceService->sendEmail($invoice);
        
        return response()->json([
            'message' => 'Invoice sent successfully'
        ]);
    }

    /**
     * Delete an invoice
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $invoice = Invoice::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        // Don't allow deleting paid invoices
        if ($invoice->status === Invoice::STATUS_PAID) {
            return response()->json([
                'message' => 'Cannot delete paid invoices'
            ], 403);
        }
        
        $this->invoiceService->delete($invoice);
        
        return response()->json([
            'message' => 'Invoice deleted successfully'
        ]);
    }

    /**
     * Get invoice summary statistics
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $summary = $this->invoiceService->getSummary($user->organization_id);
        
        return response()->json($summary);
    }
}

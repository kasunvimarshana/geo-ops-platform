<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Get all payments for the authenticated user's organization
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $query = Payment::forOrganization($user->organization_id)
            ->with(['customer', 'invoice'])
            ->orderBy('paid_at', 'desc');
        
        // Filter by customer
        if ($request->has('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }
        
        // Filter by invoice
        if ($request->has('invoice_id')) {
            $query->where('invoice_id', $request->invoice_id);
        }
        
        // Filter by payment method
        if ($request->has('method')) {
            $query->byMethod($request->method);
        }
        
        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('paid_at', '>=', $request->from_date);
        }
        
        if ($request->has('to_date')) {
            $query->whereDate('paid_at', '<=', $request->to_date);
        }
        
        $perPage = $request->input('per_page', 15);
        $payments = $query->paginate($perPage);
        
        return response()->json($payments);
    }

    /**
     * Get a single payment
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $payment = Payment::forOrganization($user->organization_id)
            ->with(['customer', 'invoice', 'recorder'])
            ->findOrFail($id);
        
        return response()->json($payment);
    }

    /**
     * Record a new payment
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'amount' => 'required|numeric|min:0',
            'method' => 'required|in:cash,bank,mobile,credit',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'paid_at' => 'nullable|date',
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
        $data['recorded_by'] = $user->id;
        
        $payment = $this->paymentService->recordPayment($data);
        
        return response()->json([
            'message' => 'Payment recorded successfully',
            'payment' => $payment->load(['customer', 'invoice'])
        ], 201);
    }

    /**
     * Update an existing payment
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'nullable|numeric|min:0',
            'method' => 'nullable|in:cash,bank,mobile,credit',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'paid_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        
        $payment = Payment::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        $payment = $this->paymentService->update($payment, $validator->validated());
        
        return response()->json([
            'message' => 'Payment updated successfully',
            'payment' => $payment->load(['customer', 'invoice'])
        ]);
    }

    /**
     * Delete a payment
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        
        $payment = Payment::forOrganization($user->organization_id)
            ->findOrFail($id);
        
        $this->paymentService->delete($payment);
        
        return response()->json([
            'message' => 'Payment deleted successfully'
        ]);
    }

    /**
     * Get payment summary statistics
     */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user();
        $period = $request->input('period', 'all');
        
        $summary = $this->paymentService->getSummary($user->organization_id, $period);
        
        return response()->json($summary);
    }

    /**
     * Get customer payment history
     */
    public function customerHistory(Request $request, int $customerId): JsonResponse
    {
        $user = $request->user();
        
        $history = $this->paymentService->getCustomerHistory($customerId, $user->organization_id);
        
        return response()->json($history);
    }
}

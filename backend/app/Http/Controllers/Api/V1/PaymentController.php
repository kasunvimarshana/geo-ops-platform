<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Payment\CreatePaymentDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Payment Controller
 *
 * Handles payment management endpoints.
 */
class PaymentController extends Controller
{
    public function __construct(
        protected PaymentService $paymentService
    ) {}

    /**
     * List payments with filters and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'invoice_id' => $request->input('invoice_id'),
                'customer_id' => $request->input('customer_id'),
                'payment_method' => $request->input('payment_method'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'search' => $request->input('search'),
                'sort_by' => $request->input('sort_by', 'created_at'),
                'sort_direction' => $request->input('sort_direction', 'desc'),
            ];

            $perPage = $request->input('per_page', 15);
            $payments = $this->paymentService->getPaymentsPaginated($filters, $perPage);

            return $this->successResponse(
                PaymentResource::collection($payments)->response()->getData(true),
                'Payments retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve payments', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve payments.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Record new payment
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        try {
            $dto = CreatePaymentDTO::fromArray($request->validated());
            $payment = $this->paymentService->recordPayment($dto);

            return $this->successResponse(
                new PaymentResource($payment),
                'Payment recorded successfully.',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            \Log::error('Failed to record payment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to record payment: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get single payment details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $payment = $this->paymentService->getPayment($id);
            $payment->load(['invoice', 'customer']);

            return $this->successResponse(
                new PaymentResource($payment),
                'Payment retrieved successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Payment not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve payment', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve payment: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Soft delete payment (revert invoice balance)
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->paymentService->deletePayment($id);

            return $this->successResponse(
                null,
                'Payment deleted successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Payment not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to delete payment', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to delete payment: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    protected function successResponse(mixed $data, string $message, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(string $message, int $status = Response::HTTP_BAD_REQUEST, ?array $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Requests\Payment\UpdatePaymentRequest;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $organizationId = $request->user()->organization_id;
            
            $payments = $this->paymentRepository->findByOrganization($organizationId, [
                'payment_method' => $request->payment_method,
                'invoice_id' => $request->invoice_id,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'per_page' => $request->per_page ?? 15,
            ]);

            return response()->json([
                'success' => true,
                'data' => $payments->items(),
                'meta' => [
                    'pagination' => [
                        'total' => $payments->total(),
                        'per_page' => $payments->perPage(),
                        'current_page' => $payments->currentPage(),
                        'last_page' => $payments->lastPage(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payments',
            ], 500);
        }
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        try {
            $payment = $this->paymentRepository->create(array_merge(
                $request->validated(),
                [
                    'organization_id' => $request->user()->organization_id,
                    'received_by' => $request->user()->id,
                    'sync_status' => 'synced',
                ]
            ));

            return response()->json([
                'success' => true,
                'data' => $payment,
                'message' => 'Payment created successfully',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $payment = $this->paymentRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $payment,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment',
            ], 500);
        }
    }

    public function update(int $id, UpdatePaymentRequest $request): JsonResponse
    {
        try {
            $payment = $this->paymentRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found',
                ], 404);
            }

            $this->paymentRepository->update($id, $request->validated());

            return response()->json([
                'success' => true,
                'data' => $this->paymentRepository->findById($id),
                'message' => 'Payment updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            $payment = $this->paymentRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not found',
                ], 404);
            }

            $this->paymentRepository->delete($id);

            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}

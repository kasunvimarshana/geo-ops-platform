<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index(Request $request): JsonResponse
    {
        $payments = $this->paymentService->getAllPayments($request->user()->id);
        return response()->json(PaymentResource::collection($payments));
    }

    public function store(CreatePaymentRequest $request): JsonResponse
    {
        $payment = $this->paymentService->createPayment($request->validated());
        return response()->json(new PaymentResource($payment), 201);
    }

    public function show($id): JsonResponse
    {
        $payment = $this->paymentService->getPaymentById($id);
        return response()->json(new PaymentResource($payment));
    }

    public function update(CreatePaymentRequest $request, $id): JsonResponse
    {
        $payment = $this->paymentService->updatePayment($id, $request->validated());
        return response()->json(new PaymentResource($payment));
    }

    public function destroy($id): JsonResponse
    {
        $this->paymentService->deletePayment($id);
        return response()->json(null, 204);
    }
}
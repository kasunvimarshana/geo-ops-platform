<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Customer::with('organization')
                ->where('organization_id', auth()->user()->organization_id);

            // Search
            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $customers = $query->orderBy('name')->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $customers,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to fetch customers',
                ],
            ], 500);
        }
    }

    /**
     * Store a newly created customer.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors(),
                ],
            ], 422);
        }

        try {
            $customer = Customer::create([
                'organization_id' => auth()->user()->organization_id,
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'address' => $request->address,
                'balance' => 0,
            ]);

            return response()->json([
                'success' => true,
                'data' => $customer->load('organization'),
                'message' => 'Customer created successfully',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SERVER_ERROR',
                    'message' => 'Failed to create customer',
                ],
            ], 500);
        }
    }

    /**
     * Display the specified customer.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $customer = Customer::with(['organization', 'jobs', 'invoices', 'payments'])
                ->where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $customer,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Customer not found',
                ],
            ], 404);
        }
    }

    /**
     * Update the specified customer.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'phone' => 'sometimes|required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'VALIDATION_ERROR',
                    'message' => 'Validation failed',
                    'details' => $validator->errors(),
                ],
            ], 422);
        }

        try {
            $customer = Customer::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            $customer->update($request->only(['name', 'phone', 'email', 'address']));

            return response()->json([
                'success' => true,
                'data' => $customer->load('organization'),
                'message' => 'Customer updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Customer not found',
                ],
            ], 404);
        }
    }

    /**
     * Remove the specified customer (soft delete).
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $customer = Customer::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            $customer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Customer not found',
                ],
            ], 404);
        }
    }

    /**
     * Get customer statistics.
     */
    public function statistics(string $id): JsonResponse
    {
        try {
            $customer = Customer::where('organization_id', auth()->user()->organization_id)
                ->findOrFail($id);

            $stats = [
                'total_jobs' => $customer->jobs()->count(),
                'completed_jobs' => $customer->jobs()->where('status', 'completed')->count(),
                'total_invoices' => $customer->invoices()->count(),
                'paid_invoices' => $customer->invoices()->where('status', 'paid')->count(),
                'total_invoiced' => $customer->invoices()->sum('total'),
                'total_paid' => $customer->payments()->sum('amount'),
                'current_balance' => $customer->balance,
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'NOT_FOUND',
                    'message' => 'Customer not found',
                ],
            ], 404);
        }
    }
}

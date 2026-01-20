<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Repositories\CustomerRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    protected $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function index(Request $request): JsonResponse
    {
        $customers = $this->customerRepository->getAllCustomers();
        return response()->json(CustomerResource::collection($customers), 200);
    }

    public function store(CreateCustomerRequest $request): JsonResponse
    {
        $customerData = $request->validated();
        $customer = $this->customerRepository->createCustomer($customerData);
        return response()->json(new CustomerResource($customer), 201);
    }

    public function show($id): JsonResponse
    {
        $customer = $this->customerRepository->findCustomerById($id);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        return response()->json(new CustomerResource($customer), 200);
    }

    public function update(CreateCustomerRequest $request, $id): JsonResponse
    {
        $customerData = $request->validated();
        $customer = $this->customerRepository->updateCustomer($id, $customerData);
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        return response()->json(new CustomerResource($customer), 200);
    }

    public function destroy($id): JsonResponse
    {
        $deleted = $this->customerRepository->deleteCustomer($id);
        if (!$deleted) {
            return response()->json(['message' => 'Customer not found'], 404);
        }
        return response()->json(['message' => 'Customer deleted successfully'], 204);
    }
}
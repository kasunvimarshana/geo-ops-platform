<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\LandMeasurement;
use App\Models\Job;
use App\Models\TrackingLog;
use App\Models\Expense;
use App\Services\LandMeasurementService;

class SyncController extends Controller
{
    protected $landMeasurementService;

    public function __construct(LandMeasurementService $landMeasurementService)
    {
        $this->landMeasurementService = $landMeasurementService;
    }

    /**
     * Push offline data to server
     * Handles conflict resolution with last-write-wins strategy
     */
    public function push(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'measurements' => 'array',
            'measurements.*.id' => 'nullable|integer',
            'measurements.*.name' => 'required|string',
            'measurements.*.coordinates' => 'required|array',
            'measurements.*.area_acres' => 'required|numeric',
            'measurements.*.area_hectares' => 'required|numeric',
            'measurements.*.measured_at' => 'required|date',
            'measurements.*.client_id' => 'required|string',
            
            'jobs' => 'array',
            'jobs.*.id' => 'nullable|integer',
            'jobs.*.customer_id' => 'required|integer',
            'jobs.*.land_measurement_id' => 'nullable|integer',
            'jobs.*.status' => 'required|string',
            'jobs.*.client_id' => 'required|string',
            
            'tracking' => 'array',
            'tracking.*.driver_id' => 'nullable|integer',
            'tracking.*.job_id' => 'nullable|integer',
            'tracking.*.latitude' => 'required|numeric',
            'tracking.*.longitude' => 'required|numeric',
            'tracking.*.recorded_at' => 'required|date',
            
            'expenses' => 'array',
            'expenses.*.id' => 'nullable|integer',
            'expenses.*.category' => 'required|string',
            'expenses.*.amount' => 'required|numeric',
            'expenses.*.date' => 'required|date',
            'expenses.*.client_id' => 'required|string',
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
            DB::beginTransaction();
            
            $results = [
                'measurements' => [],
                'jobs' => [],
                'tracking' => [],
                'expenses' => [],
                'conflicts' => [],
            ];

            // Process measurements
            if ($request->has('measurements')) {
                foreach ($request->measurements as $measurementData) {
                    try {
                        $measurement = $this->syncMeasurement($measurementData);
                        $results['measurements'][] = [
                            'client_id' => $measurementData['client_id'],
                            'server_id' => $measurement->id,
                            'status' => 'synced',
                        ];
                    } catch (\Exception $e) {
                        $results['conflicts'][] = [
                            'type' => 'measurement',
                            'client_id' => $measurementData['client_id'],
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }

            // Process jobs
            if ($request->has('jobs')) {
                foreach ($request->jobs as $jobData) {
                    try {
                        $job = $this->syncJob($jobData);
                        $results['jobs'][] = [
                            'client_id' => $jobData['client_id'],
                            'server_id' => $job->id,
                            'status' => 'synced',
                        ];
                    } catch (\Exception $e) {
                        $results['conflicts'][] = [
                            'type' => 'job',
                            'client_id' => $jobData['client_id'],
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }

            // Process tracking logs (always create new)
            if ($request->has('tracking')) {
                foreach ($request->tracking as $trackingData) {
                    TrackingLog::create([
                        'driver_id' => $trackingData['driver_id'] ?? null,
                        'job_id' => $trackingData['job_id'] ?? null,
                        'latitude' => $trackingData['latitude'],
                        'longitude' => $trackingData['longitude'],
                        'accuracy' => $trackingData['accuracy'] ?? null,
                        'speed' => $trackingData['speed'] ?? null,
                        'heading' => $trackingData['heading'] ?? null,
                        'recorded_at' => $trackingData['recorded_at'],
                    ]);
                }
                $results['tracking']['count'] = count($request->tracking);
            }

            // Process expenses
            if ($request->has('expenses')) {
                foreach ($request->expenses as $expenseData) {
                    try {
                        $expense = $this->syncExpense($expenseData);
                        $results['expenses'][] = [
                            'client_id' => $expenseData['client_id'],
                            'server_id' => $expense->id,
                            'status' => 'synced',
                        ];
                    } catch (\Exception $e) {
                        $results['conflicts'][] = [
                            'type' => 'expense',
                            'client_id' => $expenseData['client_id'],
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $results,
                'message' => 'Data synced successfully',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SYNC_ERROR',
                    'message' => 'Sync failed: ' . $e->getMessage(),
                ],
            ], 500);
        }
    }

    /**
     * Pull latest data from server
     */
    public function pull(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'last_sync_at' => 'nullable|date',
            'include' => 'array',
            'include.*' => 'in:measurements,jobs,customers,drivers,machines,invoices',
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
            $lastSyncAt = $request->last_sync_at 
                ? \Carbon\Carbon::parse($request->last_sync_at) 
                : null;
            
            $include = $request->get('include', ['measurements', 'jobs', 'customers', 'drivers', 'machines']);
            
            $data = [];
            $organizationId = auth()->user()->organization_id;

            // Get measurements
            if (in_array('measurements', $include)) {
                $query = LandMeasurement::where('organization_id', $organizationId);
                if ($lastSyncAt) {
                    $query->where('updated_at', '>', $lastSyncAt);
                }
                $data['measurements'] = $query->get();
            }

            // Get jobs
            if (in_array('jobs', $include)) {
                $query = Job::with(['customer', 'driver', 'machine', 'landMeasurement'])
                    ->where('organization_id', $organizationId);
                if ($lastSyncAt) {
                    $query->where('updated_at', '>', $lastSyncAt);
                }
                $data['jobs'] = $query->get();
            }

            // Get customers
            if (in_array('customers', $include)) {
                $query = \App\Models\Customer::where('organization_id', $organizationId);
                if ($lastSyncAt) {
                    $query->where('updated_at', '>', $lastSyncAt);
                }
                $data['customers'] = $query->get();
            }

            // Get drivers
            if (in_array('drivers', $include)) {
                $query = \App\Models\Driver::with('user')
                    ->where('organization_id', $organizationId);
                if ($lastSyncAt) {
                    $query->where('updated_at', '>', $lastSyncAt);
                }
                $data['drivers'] = $query->get();
            }

            // Get machines
            if (in_array('machines', $include)) {
                $query = \App\Models\Machine::where('organization_id', $organizationId);
                if ($lastSyncAt) {
                    $query->where('updated_at', '>', $lastSyncAt);
                }
                $data['machines'] = $query->get();
            }

            // Get invoices
            if (in_array('invoices', $include)) {
                $query = \App\Models\Invoice::with(['customer', 'job'])
                    ->where('organization_id', $organizationId);
                if ($lastSyncAt) {
                    $query->where('updated_at', '>', $lastSyncAt);
                }
                $data['invoices'] = $query->get();
            }

            return response()->json([
                'success' => true,
                'data' => $data,
                'sync_timestamp' => now()->toIso8601String(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'SYNC_ERROR',
                    'message' => 'Failed to pull data: ' . $e->getMessage(),
                ],
            ], 500);
        }
    }

    /**
     * Sync measurement with conflict resolution
     */
    private function syncMeasurement(array $data)
    {
        $organizationId = auth()->user()->organization_id;
        $organization = \App\Models\Organization::findOrFail($organizationId);
        
        if (isset($data['id']) && $data['id']) {
            // Update existing
            $measurement = LandMeasurement::where('organization_id', $organizationId)
                ->findOrFail($data['id']);
            
            $updateData = [
                'name' => $data['name'],
                'area_acres' => $data['area_acres'],
                'area_hectares' => $data['area_hectares'],
                'measured_at' => $data['measured_at'],
            ];
            
            if (isset($data['coordinates'])) {
                $updateData['coordinates'] = $data['coordinates'];
            }
            
            return $this->landMeasurementService->update($measurement, $updateData);
        } else {
            // Create new
            return $this->landMeasurementService->create($organization, [
                'name' => $data['name'],
                'coordinates' => $data['coordinates'],
                'area_acres' => $data['area_acres'],
                'area_hectares' => $data['area_hectares'],
                'measured_by' => auth()->id(),
                'measured_at' => $data['measured_at'],
            ]);
        }
    }

    /**
     * Sync job with conflict resolution
     */
    private function syncJob(array $data)
    {
        $organizationId = auth()->user()->organization_id;
        
        if (isset($data['id']) && $data['id']) {
            // Update existing
            $job = Job::where('organization_id', $organizationId)
                ->findOrFail($data['id']);
            
            $job->update([
                'status' => $data['status'],
                'notes' => $data['notes'] ?? $job->notes,
            ]);
            
            return $job;
        } else {
            // Create new
            return Job::create([
                'organization_id' => $organizationId,
                'customer_id' => $data['customer_id'],
                'land_measurement_id' => $data['land_measurement_id'] ?? null,
                'status' => $data['status'],
                'scheduled_date' => $data['scheduled_date'] ?? now(),
                'notes' => $data['notes'] ?? null,
            ]);
        }
    }

    /**
     * Sync expense with conflict resolution
     */
    private function syncExpense(array $data)
    {
        $organizationId = auth()->user()->organization_id;
        
        if (isset($data['id']) && $data['id']) {
            // Update existing
            $expense = Expense::where('organization_id', $organizationId)
                ->findOrFail($data['id']);
            
            $expense->update([
                'amount' => $data['amount'],
                'description' => $data['description'] ?? $expense->description,
            ]);
            
            return $expense;
        } else {
            // Create new
            return Expense::create([
                'organization_id' => $organizationId,
                'category' => $data['category'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'description' => $data['description'] ?? null,
                'driver_id' => $data['driver_id'] ?? null,
                'machine_id' => $data['machine_id'] ?? null,
                'status' => 'pending',
            ]);
        }
    }
}

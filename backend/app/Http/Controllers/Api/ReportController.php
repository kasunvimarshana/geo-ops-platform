<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function financial(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
            ]);

            $report = $this->reportService->generateFinancialReport(
                $request->user()->organization_id,
                $validated
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate financial report',
            ], 500);
        }
    }

    public function ledger(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'customer_id' => 'nullable|string',
            ]);

            $report = $this->reportService->generateLedgerReport(
                $request->user()->organization_id,
                $validated
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate ledger report',
            ], 500);
        }
    }

    public function machinePerformance(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'from_date' => 'nullable|date',
                'to_date' => 'nullable|date|after_or_equal:from_date',
                'machine_id' => 'nullable|exists:machines,id',
            ]);

            $report = $this->reportService->generateMachinePerformanceReport(
                $request->user()->organization_id,
                $validated
            );

            return response()->json([
                'success' => true,
                'data' => $report,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate machine performance report',
            ], 500);
        }
    }
}

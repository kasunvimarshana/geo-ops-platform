<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function getJobReports(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $reports = $this->reportService->generateJobReports($validated['start_date'], $validated['end_date']);

        return response()->json($reports);
    }

    public function getInvoiceReports(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $reports = $this->reportService->generateInvoiceReports($validated['start_date'], $validated['end_date']);

        return response()->json($reports);
    }

    public function getPaymentReports(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $reports = $this->reportService->generatePaymentReports($validated['start_date'], $validated['end_date']);

        return response()->json($reports);
    }
}
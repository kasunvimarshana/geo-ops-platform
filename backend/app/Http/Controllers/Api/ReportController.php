<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Job;
use App\Models\Driver;
use App\Models\TrackingLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Get financial summary report
     */
    public function financial(Request $request): JsonResponse
    {
        $user = $request->user();
        $organizationId = $user->organization_id;
        
        // Get date range
        $fromDate = $request->input('from_date', now()->startOfMonth());
        $toDate = $request->input('to_date', now()->endOfMonth());
        
        // Income (from invoices)
        $invoices = Invoice::forOrganization($organizationId)
            ->whereBetween('issued_at', [$fromDate, $toDate])
            ->get();
        
        $totalInvoiced = $invoices->sum('total');
        $totalPaid = $invoices->where('status', Invoice::STATUS_PAID)->sum('total');
        $totalOutstanding = $invoices->whereIn('status', [
            Invoice::STATUS_SENT, 
            Invoice::STATUS_OVERDUE
        ])->sum('total');
        
        // Payments received
        $payments = Payment::where('organization_id', $organizationId)
            ->whereBetween('paid_at', [$fromDate, $toDate])
            ->get();
        
        $totalReceived = $payments->sum('amount');
        $paymentsByMethod = [
            'cash' => $payments->where('method', 'cash')->sum('amount'),
            'bank' => $payments->where('method', 'bank')->sum('amount'),
            'mobile' => $payments->where('method', 'mobile')->sum('amount'),
            'credit' => $payments->where('method', 'credit')->sum('amount'),
        ];
        
        // Expenses
        $expenses = Expense::forOrganization($organizationId)
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->where('status', Expense::STATUS_APPROVED)
            ->get();
        
        $totalExpenses = $expenses->sum('amount');
        $expensesByCategory = [
            'fuel' => $expenses->where('category', Expense::CATEGORY_FUEL)->sum('amount'),
            'parts' => $expenses->where('category', Expense::CATEGORY_PARTS)->sum('amount'),
            'maintenance' => $expenses->where('category', Expense::CATEGORY_MAINTENANCE)->sum('amount'),
            'labor' => $expenses->where('category', Expense::CATEGORY_LABOR)->sum('amount'),
            'other' => $expenses->where('category', Expense::CATEGORY_OTHER)->sum('amount'),
        ];
        
        // Profit calculation
        $profit = $totalReceived - $totalExpenses;
        $profitMargin = $totalReceived > 0 ? ($profit / $totalReceived) * 100 : 0;
        
        return response()->json([
            'period' => [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            'income' => [
                'total_invoiced' => $totalInvoiced,
                'total_paid' => $totalPaid,
                'total_outstanding' => $totalOutstanding,
                'total_received' => $totalReceived,
                'payments_by_method' => $paymentsByMethod,
            ],
            'expenses' => [
                'total' => $totalExpenses,
                'by_category' => $expensesByCategory,
            ],
            'profitability' => [
                'revenue' => $totalReceived,
                'expenses' => $totalExpenses,
                'profit' => $profit,
                'profit_margin_percentage' => round($profitMargin, 2),
            ],
        ]);
    }
    
    /**
     * Get jobs report
     */
    public function jobs(Request $request): JsonResponse
    {
        $user = $request->user();
        $organizationId = $user->organization_id;
        
        // Get date range
        $fromDate = $request->input('from_date', now()->startOfMonth());
        $toDate = $request->input('to_date', now()->endOfMonth());
        
        $jobs = Job::forOrganization($organizationId)
            ->whereBetween('created_at', [$fromDate, $toDate])
            ->with(['driver', 'machine'])
            ->get();
        
        $jobsByStatus = [
            'pending' => $jobs->where('status', Job::STATUS_PENDING)->count(),
            'assigned' => $jobs->where('status', Job::STATUS_ASSIGNED)->count(),
            'in_progress' => $jobs->where('status', Job::STATUS_IN_PROGRESS)->count(),
            'completed' => $jobs->where('status', Job::STATUS_COMPLETED)->count(),
            'billed' => $jobs->where('status', Job::STATUS_BILLED)->count(),
            'paid' => $jobs->where('status', Job::STATUS_PAID)->count(),
        ];
        
        // Driver performance
        $driverStats = $jobs->groupBy('driver_id')->map(function ($driverJobs) {
            $driver = $driverJobs->first()->driver;
            return [
                'driver_id' => $driver?->id,
                'driver_name' => $driver?->name,
                'total_jobs' => $driverJobs->count(),
                'completed_jobs' => $driverJobs->where('status', Job::STATUS_COMPLETED)->count(),
                'in_progress_jobs' => $driverJobs->where('status', Job::STATUS_IN_PROGRESS)->count(),
            ];
        })->values();
        
        // Machine utilization
        $machineStats = $jobs->groupBy('machine_id')->map(function ($machineJobs) {
            $machine = $machineJobs->first()->machine;
            return [
                'machine_id' => $machine?->id,
                'machine_name' => $machine?->name,
                'machine_type' => $machine?->type,
                'total_jobs' => $machineJobs->count(),
                'completed_jobs' => $machineJobs->where('status', Job::STATUS_COMPLETED)->count(),
            ];
        })->values();
        
        // Completion rate
        $totalJobs = $jobs->count();
        $completedJobs = $jobs->whereIn('status', [
            Job::STATUS_COMPLETED,
            Job::STATUS_BILLED,
            Job::STATUS_PAID
        ])->count();
        $completionRate = $totalJobs > 0 ? ($completedJobs / $totalJobs) * 100 : 0;
        
        return response()->json([
            'period' => [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            'summary' => [
                'total_jobs' => $totalJobs,
                'completed_jobs' => $completedJobs,
                'completion_rate_percentage' => round($completionRate, 2),
                'jobs_by_status' => $jobsByStatus,
            ],
            'driver_performance' => $driverStats,
            'machine_utilization' => $machineStats,
        ]);
    }
    
    /**
     * Get expenses report
     */
    public function expenses(Request $request): JsonResponse
    {
        $user = $request->user();
        $organizationId = $user->organization_id;
        
        // Get date range
        $fromDate = $request->input('from_date', now()->startOfMonth());
        $toDate = $request->input('to_date', now()->endOfMonth());
        
        $expenses = Expense::forOrganization($organizationId)
            ->whereBetween('expense_date', [$fromDate, $toDate])
            ->with(['machine', 'driver'])
            ->get();
        
        $totalExpenses = $expenses->sum('amount');
        $approvedExpenses = $expenses->where('status', Expense::STATUS_APPROVED)->sum('amount');
        $pendingExpenses = $expenses->where('status', Expense::STATUS_PENDING)->sum('amount');
        
        $byCategory = [
            'fuel' => $expenses->where('category', Expense::CATEGORY_FUEL)->sum('amount'),
            'parts' => $expenses->where('category', Expense::CATEGORY_PARTS)->sum('amount'),
            'maintenance' => $expenses->where('category', Expense::CATEGORY_MAINTENANCE)->sum('amount'),
            'labor' => $expenses->where('category', Expense::CATEGORY_LABOR)->sum('amount'),
            'other' => $expenses->where('category', Expense::CATEGORY_OTHER)->sum('amount'),
        ];
        
        // Machine expenses
        $machineExpenses = $expenses->whereNotNull('machine_id')
            ->groupBy('machine_id')
            ->map(function ($machineExpenses) {
                $machine = $machineExpenses->first()->machine;
                return [
                    'machine_id' => $machine?->id,
                    'machine_name' => $machine?->name,
                    'total_expenses' => $machineExpenses->sum('amount'),
                    'expense_count' => $machineExpenses->count(),
                ];
            })->values();
        
        // Driver expenses
        $driverExpenses = $expenses->whereNotNull('driver_id')
            ->groupBy('driver_id')
            ->map(function ($driverExpenses) {
                $driver = $driverExpenses->first()->driver;
                return [
                    'driver_id' => $driver?->id,
                    'driver_name' => $driver?->name,
                    'total_expenses' => $driverExpenses->sum('amount'),
                    'expense_count' => $driverExpenses->count(),
                ];
            })->values();
        
        return response()->json([
            'period' => [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            'summary' => [
                'total_expenses' => $totalExpenses,
                'approved_expenses' => $approvedExpenses,
                'pending_expenses' => $pendingExpenses,
                'by_category' => $byCategory,
            ],
            'machine_expenses' => $machineExpenses,
            'driver_expenses' => $driverExpenses,
        ]);
    }
    
    /**
     * Get dashboard overview
     */
    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();
        $organizationId = $user->organization_id;
        
        // Current month data
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        // Jobs this month
        $jobsThisMonth = Job::forOrganization($organizationId)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();
        
        $completedJobsThisMonth = Job::forOrganization($organizationId)
            ->whereBetween('completed_at', [$startOfMonth, $endOfMonth])
            ->whereIn('status', [Job::STATUS_COMPLETED, Job::STATUS_BILLED, Job::STATUS_PAID])
            ->count();
        
        // Revenue this month
        $revenueThisMonth = Payment::where('organization_id', $organizationId)
            ->whereBetween('paid_at', [$startOfMonth, $endOfMonth])
            ->sum('amount');
        
        // Expenses this month
        $expensesThisMonth = Expense::forOrganization($organizationId)
            ->whereBetween('expense_date', [$startOfMonth, $endOfMonth])
            ->where('status', Expense::STATUS_APPROVED)
            ->sum('amount');
        
        // Outstanding invoices
        $outstandingInvoices = Invoice::forOrganization($organizationId)
            ->whereIn('status', [Invoice::STATUS_SENT, Invoice::STATUS_OVERDUE])
            ->sum('total');
        
        // Active drivers count - using Eloquent with proper relationships
        $activeDriverIds = TrackingLog::where('recorded_at', '>', now()->subHours(2))
            ->distinct('driver_id')
            ->pluck('driver_id');
        
        $activeDrivers = Driver::where('organization_id', $organizationId)
            ->whereIn('id', $activeDriverIds)
            ->count();
        
        return response()->json([
            'current_month' => [
                'jobs_created' => $jobsThisMonth,
                'jobs_completed' => $completedJobsThisMonth,
                'revenue' => $revenueThisMonth,
                'expenses' => $expensesThisMonth,
                'profit' => $revenueThisMonth - $expensesThisMonth,
            ],
            'outstanding' => [
                'invoices_amount' => $outstandingInvoices,
            ],
            'current_status' => [
                'active_drivers' => $activeDrivers,
            ],
        ]);
    }
}

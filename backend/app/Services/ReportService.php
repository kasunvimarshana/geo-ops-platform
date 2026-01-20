<?php

namespace App\Services;

use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\Interfaces\ExpenseRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use App\Repositories\Interfaces\JobRepositoryInterface;
use App\Repositories\Interfaces\LandRepositoryInterface;
use App\Repositories\Interfaces\MachineRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private ExpenseRepositoryInterface $expenseRepository,
        private PaymentRepositoryInterface $paymentRepository,
        private JobRepositoryInterface $jobRepository,
        private LandRepositoryInterface $landRepository,
        private MachineRepositoryInterface $machineRepository
    ) {}

    public function generateFinancialReport(int $organizationId, array $filters): array
    {
        $fromDate = $filters['from_date'] ?? now()->startOfMonth();
        $toDate = $filters['to_date'] ?? now();

        $invoices = $this->invoiceRepository->findByOrganization($organizationId, [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'per_page' => 9999,
        ])->items();

        $expenses = $this->expenseRepository->findByOrganization($organizationId, [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'per_page' => 9999,
        ])->items();

        $payments = $this->paymentRepository->findByOrganization($organizationId, [
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'per_page' => 9999,
        ])->items();

        $totalRevenue = collect($invoices)->sum('total_amount');
        $totalPaid = collect($invoices)->sum('paid_amount');
        $totalExpenses = collect($expenses)->sum('amount');
        $netProfit = $totalPaid - $totalExpenses;

        return [
            'period' => [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            'summary' => [
                'total_revenue' => $totalRevenue,
                'total_paid' => $totalPaid,
                'outstanding' => $totalRevenue - $totalPaid,
                'total_expenses' => $totalExpenses,
                'net_profit' => $netProfit,
            ],
            'invoices' => [
                'total_count' => count($invoices),
                'paid_count' => collect($invoices)->where('status', 'paid')->count(),
                'pending_count' => collect($invoices)->whereIn('status', ['draft', 'sent'])->count(),
            ],
            'expenses' => [
                'total_count' => count($expenses),
                'by_category' => collect($expenses)->groupBy('category')->map(fn($items) => [
                    'count' => $items->count(),
                    'total' => $items->sum('amount'),
                ])->toArray(),
            ],
            'payments' => [
                'total_count' => count($payments),
                'by_method' => collect($payments)->groupBy('payment_method')->map(fn($items) => [
                    'count' => $items->count(),
                    'total' => $items->sum('amount'),
                ])->toArray(),
            ],
        ];
    }

    public function generateLedgerReport(int $organizationId, array $filters): array
    {
        $fromDate = $filters['from_date'] ?? now()->startOfMonth();
        $toDate = $filters['to_date'] ?? now();
        $customerId = $filters['customer_id'] ?? null;

        $query = DB::table('invoices')
            ->where('organization_id', $organizationId)
            ->whereBetween('invoice_date', [$fromDate, $toDate]);

        if ($customerId) {
            $query->where('customer_name', $customerId);
        }

        $invoices = $query->get();

        $ledgerEntries = [];

        foreach ($invoices as $invoice) {
            $ledgerEntries[] = [
                'date' => $invoice->invoice_date,
                'type' => 'invoice',
                'reference' => $invoice->invoice_number,
                'customer' => $invoice->customer_name,
                'debit' => $invoice->total_amount,
                'credit' => 0,
                'balance' => $invoice->balance,
            ];

            $payments = DB::table('payments')
                ->where('invoice_id', $invoice->id)
                ->get();

            foreach ($payments as $payment) {
                $ledgerEntries[] = [
                    'date' => $payment->payment_date,
                    'type' => 'payment',
                    'reference' => $payment->reference_number,
                    'customer' => $invoice->customer_name,
                    'debit' => 0,
                    'credit' => $payment->amount,
                    'balance' => null,
                ];
            }
        }

        usort($ledgerEntries, fn($a, $b) => $a['date'] <=> $b['date']);

        return [
            'period' => [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            'entries' => $ledgerEntries,
            'summary' => [
                'total_debit' => collect($ledgerEntries)->sum('debit'),
                'total_credit' => collect($ledgerEntries)->sum('credit'),
            ],
        ];
    }

    public function generateMachinePerformanceReport(int $organizationId, array $filters): array
    {
        $fromDate = $filters['from_date'] ?? now()->startOfMonth();
        $toDate = $filters['to_date'] ?? now();
        $machineId = $filters['machine_id'] ?? null;

        $machines = $machineId 
            ? [$this->machineRepository->findByIdAndOrganization($machineId, $organizationId)]
            : $this->machineRepository->findActive($organizationId);

        $report = [];

        foreach ($machines as $machine) {
            if (!$machine) continue;

            $jobs = DB::table('jobs')
                ->where('machine_id', $machine->id)
                ->whereBetween('job_date', [$fromDate, $toDate])
                ->get();

            $expenses = DB::table('expenses')
                ->where('machine_id', $machine->id)
                ->whereBetween('expense_date', [$fromDate, $toDate])
                ->get();

            $totalJobs = $jobs->count();
            $completedJobs = $jobs->where('status', 'completed')->count();
            $totalDuration = $jobs->sum('duration_minutes');
            $totalExpenses = $expenses->sum('amount');

            $lands = DB::table('lands')
                ->whereIn('id', $jobs->pluck('land_id'))
                ->get();
            
            $totalAreaWorked = $lands->sum('area_acres');

            $report[] = [
                'machine' => [
                    'id' => $machine->id,
                    'name' => $machine->name,
                    'type' => $machine->machine_type,
                    'registration' => $machine->registration_number,
                ],
                'performance' => [
                    'total_jobs' => $totalJobs,
                    'completed_jobs' => $completedJobs,
                    'completion_rate' => $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100, 2) : 0,
                    'total_duration_minutes' => $totalDuration,
                    'average_duration_minutes' => $totalJobs > 0 ? round($totalDuration / $totalJobs, 2) : 0,
                    'total_area_worked_acres' => $totalAreaWorked,
                ],
                'financials' => [
                    'total_expenses' => $totalExpenses,
                    'average_expense_per_job' => $totalJobs > 0 ? round($totalExpenses / $totalJobs, 2) : 0,
                    'expense_breakdown' => $expenses->groupBy('category')->map(fn($items) => $items->sum('amount'))->toArray(),
                ],
            ];
        }

        return [
            'period' => [
                'from' => $fromDate,
                'to' => $toDate,
            ],
            'machines' => $report,
        ];
    }
}

<?php

namespace App\Services;

use App\Models\Job;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Generate a report of completed jobs within a specified date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getCompletedJobsReport(string $startDate, string $endDate): array
    {
        return Job::where('status', 'completed')
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->with('land', 'driver')
            ->get()
            ->toArray();
    }

    /**
     * Generate a financial report for a specified date range.
     *
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getFinancialReport(string $startDate, string $endDate): array
    {
        $invoices = Invoice::whereBetween('created_at', [$startDate, $endDate])->get();
        $payments = Payment::whereBetween('created_at', [$startDate, $endDate])->get();
        $expenses = Expense::whereBetween('created_at', [$startDate, $endDate])->get();

        $totalIncome = $invoices->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $totalPayments = $payments->sum('amount');

        return [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'net_profit' => $totalIncome - $totalExpenses,
            'total_payments' => $totalPayments,
        ];
    }

    /**
     * Generate a report of expenses categorized by type.
     *
     * @return array
     */
    public function getExpensesByCategoryReport(): array
    {
        return DB::table('expenses')
            ->select('category', DB::raw('SUM(amount) as total_amount'))
            ->groupBy('category')
            ->get()
            ->toArray();
    }
}
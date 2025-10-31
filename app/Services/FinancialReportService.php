<?php

namespace App\Services;

use App\Models\FinancialReport;
use App\Models\Payment;
use App\Models\Refund;
use App\Models\Transaction;
use Carbon\Carbon;

class FinancialReportService
{
    /**
     * Generate monthly report
     */
    public function generateMonthlyReport($year, $month)
    {
        $startDate = Carbon::createFromDate($year, $month, 1);
        $endDate = $startDate->clone()->endOfMonth();

        return $this->generateReport('monthly', $startDate, $endDate);
    }

    /**
     * Generate quarterly report
     */
    public function generateQuarterlyReport($year, $quarter)
    {
        $startMonth = ($quarter - 1) * 3 + 1;
        $startDate = Carbon::createFromDate($year, $startMonth, 1);
        $endDate = $startDate->clone()->addMonths(3)->endOfMonth();

        return $this->generateReport('quarterly', $startDate, $endDate);
    }

    /**
     * Generate annual report
     */
    public function generateAnnualReport($year)
    {
        $startDate = Carbon::createFromDate($year, 1, 1);
        $endDate = $startDate->clone()->endOfYear();

        return $this->generateReport('annual', $startDate, $endDate);
    }

    /**
     * Generate custom period report
     */
    public function generateCustomReport($startDate, $endDate, $type = 'custom')
    {
        return $this->generateReport($type, $startDate, $endDate);
    }

    /**
     * Generate report
     */
    private function generateReport($type, $startDate, $endDate)
    {
        $payments = Payment::whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $refunds = Refund::whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $transactions = Transaction::whereBetween('created_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->get();

        $totalRevenue = $payments->sum('amount');
        $totalRefunds = $refunds->sum('amount');
        $totalExpenses = 0; // Can be extended based on expense tracking
        $netIncome = $totalRevenue - $totalExpenses - $totalRefunds;

        $breakdownByType = $payments->groupBy('payment_type')
            ->map(fn ($group) => $group->sum('amount'))
            ->toArray();

        $breakdownByMethod = $payments->groupBy('payment_method')
            ->map(fn ($group) => $group->sum('amount'))
            ->toArray();

        $successfulPayments = Payment::whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'completed')
            ->count();

        $failedPayments = Payment::whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', 'failed')
            ->count();

        $report = FinancialReport::create([
            'report_type' => $type,
            'period_start' => $startDate->toDateString(),
            'period_end' => $endDate->toDateString(),
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'total_refunds' => $totalRefunds,
            'net_income' => $netIncome,
            'transaction_count' => $transactions->count(),
            'successful_payments' => $successfulPayments,
            'failed_payments' => $failedPayments,
            'breakdown_by_type' => $breakdownByType,
            'breakdown_by_method' => $breakdownByMethod,
        ]);

        return $report;
    }

    /**
     * Get report by ID
     */
    public function getReport($reportId)
    {
        return FinancialReport::findOrFail($reportId);
    }

    /**
     * Get recent reports
     */
    public function getRecentReports($limit = 12)
    {
        return FinancialReport::orderBy('period_end', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get report by type
     */
    public function getReportsByType($type, $limit = 12)
    {
        return FinancialReport::byType($type)
            ->orderBy('period_end', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Export report to CSV
     */
    public function exportToCsv($reportId)
    {
        $report = $this->getReport($reportId);

        $filename = "financial-report-{$report->report_type}-{$report->period_start}.csv";

        return response()->streamDownload(function () use ($report) {
            echo "Financial Report - {$report->report_type}\n";
            echo "Period: {$report->period_start} to {$report->period_end}\n\n";
            echo "Total Revenue,Total Expenses,Total Refunds,Net Income\n";
            echo "{$report->total_revenue},{$report->total_expenses},{$report->total_refunds},{$report->net_income}\n\n";
            echo "Transactions,Successful,Failed\n";
            echo "{$report->transaction_count},{$report->successful_payments},{$report->failed_payments}\n";
        }, $filename);
    }
}

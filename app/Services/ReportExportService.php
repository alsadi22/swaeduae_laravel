<?php

namespace App\Services;

use App\Models\User;
use App\Models\Organization;
use App\Models\Event;
use App\Models\Application;
use App\Models\Certificate;
use App\Models\Attendance;
use Carbon\Carbon;
use PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;

class ReportExportService
{
    /**
     * Export report data in the specified format
     *
     * @param array $reportData
     * @param string $format
     * @param string $filename
     * @return mixed
     */
    public function export(array $reportData, string $format, string $filename)
    {
        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($reportData, $filename);
            case 'excel':
                return $this->exportToExcel($reportData, $filename);
            case 'csv':
                return $this->exportToCsv($reportData, $filename);
            default:
                return $this->exportToPdf($reportData, $filename);
        }
    }

    /**
     * Export report data to PDF
     *
     * @param array $reportData
     * @param string $filename
     * @return mixed
     */
    private function exportToPdf(array $reportData, string $filename)
    {
        $pdf = PDF::loadView('admin.analytics.exports.pdf', [
            'reportData' => $reportData,
            'generatedAt' => Carbon::now()
        ]);

        return $pdf->download($filename . '.pdf');
    }

    /**
     * Export report data to Excel
     *
     * @param array $reportData
     * @param string $filename
     * @return mixed
     */
    private function exportToExcel(array $reportData, string $filename)
    {
        return Excel::download(new ReportExport($reportData), $filename . '.xlsx');
    }

    /**
     * Export report data to CSV
     *
     * @param array $reportData
     * @param string $filename
     * @return mixed
     */
    private function exportToCsv(array $reportData, string $filename)
    {
        return Excel::download(new ReportExport($reportData), $filename . '.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromArray, WithHeadings, WithStyles
{
    protected $reportData;

    public function __construct(array $reportData)
    {
        $this->reportData = $reportData;
    }

    public function array(): array
    {
        $rows = [];

        // Add report title as first row
        $rows[] = [$this->reportData['report_title'] ?? 'Analytics Report'];
        $rows[] = []; // Empty row

        // Add metrics if available
        if (isset($this->reportData['metrics'])) {
            $rows[] = ['Metrics']; // Header
            foreach ($this->reportData['metrics'] as $label => $value) {
                $rows[] = [$label, $value];
            }
            $rows[] = []; // Empty row
        }

        // Add chart data if available
        if (isset($this->reportData['chart_data'])) {
            $rows[] = ['Data Summary']; // Header
            $rows[] = ['Category', 'Value']; // Column headers
            foreach ($this->reportData['chart_data']['labels'] as $index => $label) {
                $value = $this->reportData['chart_data']['values'][$index] ?? 0;
                $rows[] = [$label, $value];
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            // Headings are included in the array method
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true, 'size' => 14]],
            // Style metric headers
            3 => ['font' => ['bold' => true]],
            // Style data summary headers
            (isset($this->reportData['metrics']) ? count($this->reportData['metrics']) + 5 : 3) => ['font' => ['bold' => true]],
        ];
    }
}
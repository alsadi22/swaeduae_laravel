<?php

namespace App\Services;

use App\Models\KpiMetric;
use App\Models\KpiValue;
use App\Models\MetricsAggregation;
use Illuminate\Support\Facades\DB;

class MetricsService
{
    /**
     * Create KPI metric
     */
    public function createKpiMetric($name, $displayName, $category, $calculationMethod, $formula = null)
    {
        return KpiMetric::create([
            'metric_name' => $name,
            'display_name' => $displayName,
            'calculation_method' => $calculationMethod,
            'formula' => $formula,
            'category' => $category,
        ]);
    }

    /**
     * Record KPI value
     */
    public function recordKpiValue($metricName, $date, $value, $targetValue = null, $breakdown = null)
    {
        $metric = KpiMetric::where('metric_name', $metricName)->firstOrFail();

        // Get previous value for trend
        $previousValue = KpiValue::where('kpi_metric_id', $metric->id)
            ->latest('date')
            ->first();

        $changePercentage = null;
        $trend = null;

        if ($previousValue) {
            $changePercentage = (($value - $previousValue->value) / $previousValue->value) * 100;
            $trend = $value > $previousValue->value ? 'up' : ($value < $previousValue->value ? 'down' : 'neutral');
        }

        return KpiValue::create([
            'kpi_metric_id' => $metric->id,
            'date' => $date,
            'value' => $value,
            'target_value' => $targetValue,
            'previous_value' => $previousValue?->value,
            'change_percentage' => $changePercentage,
            'trend' => $trend,
            'breakdown' => $breakdown,
        ]);
    }

    /**
     * Get KPI for date range
     */
    public function getKpiByDateRange($metricName, $startDate, $endDate)
    {
        $metric = KpiMetric::where('metric_name', $metricName)->firstOrFail();

        return KpiValue::where('kpi_metric_id', $metric->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->get();
    }

    /**
     * Get KPI summary
     */
    public function getKpiSummary($category = null)
    {
        $query = KpiMetric::with('values');

        if ($category) {
            $query->where('category', $category);
        }

        return $query->get()->map(function ($metric) {
            $latestValue = $metric->values->sortByDesc('date')->first();

            return [
                'name' => $metric->display_name,
                'value' => $latestValue?->value ?? 0,
                'target' => $latestValue?->target_value,
                'trend' => $latestValue?->trend,
                'change' => $latestValue?->change_percentage,
                'unit' => $metric->unit,
            ];
        });
    }

    /**
     * Aggregate metrics
     */
    public function aggregateMetrics($metricKey, $date, $granularity, $value, $dimensions = null, $breakdown = null)
    {
        return MetricsAggregation::updateOrCreate(
            [
                'metric_key' => $metricKey,
                'date' => $date,
                'granularity' => $granularity,
            ],
            [
                'dimensions' => $dimensions,
                'value' => $value,
                'breakdown' => $breakdown,
            ]
        );
    }

    /**
     * Get metric trend
     */
    public function getMetricTrend($metricName, $days = 30)
    {
        $metric = KpiMetric::where('metric_name', $metricName)->firstOrFail();

        return KpiValue::where('kpi_metric_id', $metric->id)
            ->where('date', '>=', now()->subDays($days)->format('Y-m-d'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Calculate growth rate
     */
    public function calculateGrowthRate($metricName, $period = 'month')
    {
        $metric = KpiMetric::where('metric_name', $metricName)->firstOrFail();

        $currentStart = match($period) {
            'week' => now()->subWeek(),
            'month' => now()->subMonth(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $previousStart = match($period) {
            'week' => now()->subWeeks(2),
            'month' => now()->subMonths(2),
            'year' => now()->subYears(2),
            default => now()->subMonths(2),
        };

        $currentPeriodEnd = $currentStart->copy()->add($period);
        $previousPeriodEnd = $previousStart->copy()->add($period);

        $currentValue = KpiValue::where('kpi_metric_id', $metric->id)
            ->whereBetween('date', [$currentStart, $currentPeriodEnd])
            ->sum('value');

        $previousValue = KpiValue::where('kpi_metric_id', $metric->id)
            ->whereBetween('date', [$previousStart, $previousPeriodEnd])
            ->sum('value');

        if ($previousValue === 0) return 0;

        return (($currentValue - $previousValue) / $previousValue) * 100;
    }

    /**
     * Get vs target
     */
    public function getVsTarget($metricName, $date)
    {
        $metric = KpiMetric::where('metric_name', $metricName)->firstOrFail();

        $value = KpiValue::where('kpi_metric_id', $metric->id)
            ->where('date', $date)
            ->first();

        if (!$value || !$value->target_value) return null;

        $variance = $value->value - $value->target_value;
        $variancePercentage = ($variance / $value->target_value) * 100;

        return [
            'actual' => $value->value,
            'target' => $value->target_value,
            'variance' => $variance,
            'variance_percentage' => $variancePercentage,
            'status' => $variance >= 0 ? 'above' : 'below',
        ];
    }
}

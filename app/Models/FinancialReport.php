<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialReport extends Model
{
    protected $fillable = [
        'report_type',
        'period_start',
        'period_end',
        'total_revenue',
        'total_expenses',
        'total_refunds',
        'net_income',
        'transaction_count',
        'successful_payments',
        'failed_payments',
        'breakdown_by_type',
        'breakdown_by_method',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'breakdown_by_type' => 'array',
        'breakdown_by_method' => 'array',
    ];

    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function scopeByPeriod($query, $start, $end)
    {
        return $query->whereBetween('period_start', [$start, $end]);
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->transaction_count === 0) {
            return 0;
        }

        return ($this->successful_payments / $this->transaction_count) * 100;
    }
}

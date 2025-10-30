<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'format',
        'frequency',
        'time',
        'recipients',
        'parameters',
        'is_active',
        'last_run_at',
        'next_run_at',
    ];

    protected $casts = [
        'recipients' => 'array',
        'parameters' => 'array',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active scheduled reports.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the next run time for this scheduled report.
     */
    public function getNextRunTimeAttribute()
    {
        return $this->next_run_at ?? $this->calculateNextRunTime();
    }

    /**
     * Calculate the next run time based on frequency.
     */
    public function calculateNextRunTime()
    {
        $now = now();
        $runTime = $this->time ? strtotime($this->time) : strtotime('00:00');

        switch ($this->frequency) {
            case 'daily':
                $nextRun = $now->copy()->startOfDay()->addSeconds($runTime);
                if ($nextRun->isPast()) {
                    $nextRun->addDay();
                }
                break;
            case 'weekly':
                $nextRun = $now->copy()->startOfWeek()->addSeconds($runTime);
                if ($nextRun->isPast()) {
                    $nextRun->addWeek();
                }
                break;
            case 'monthly':
                $nextRun = $now->copy()->startOfMonth()->addSeconds($runTime);
                if ($nextRun->isPast()) {
                    $nextRun->addMonth();
                }
                break;
            default:
                $nextRun = $now->copy()->addDay();
        }

        return $nextRun;
    }
}
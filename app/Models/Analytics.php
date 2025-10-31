<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AnalyticsEvent extends Model
{
    protected $fillable = [
        'user_id', 'event_type', 'event_category', 'event_label', 'event_data',
        'page_url', 'referrer', 'device_type', 'browser', 'os', 'country',
        'city', 'ip_address', 'session_duration_seconds',
    ];

    protected $casts = ['event_data' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function scopeByType($q, $type) { return $q->where('event_type', $type); }
    public function scopeByCategory($q, $cat) { return $q->where('event_category', $cat); }
}

class DashboardWidget extends Model
{
    protected $fillable = [
        'user_id', 'widget_type', 'metric_name', 'title', 'description',
        'configuration', 'position', 'size', 'is_visible', 'filters',
    ];

    protected $casts = ['configuration' => 'array', 'filters' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

class KpiMetric extends Model
{
    protected $fillable = [
        'metric_name', 'display_name', 'unit', 'calculation_method',
        'sql_query', 'formula', 'category', 'description',
    ];

    protected $casts = ['formula' => 'array'];

    public function values(): HasMany { return $this->hasMany(KpiValue::class); }
    public function scopeByCategory($q, $cat) { return $q->where('category', $cat); }
}

class KpiValue extends Model
{
    protected $fillable = [
        'kpi_metric_id', 'date', 'value', 'target_value', 'previous_value',
        'change_percentage', 'trend', 'breakdown',
    ];

    protected $casts = ['breakdown' => 'array'];

    public function metric(): BelongsTo { return $this->belongsTo(KpiMetric::class); }
}

class Report extends Model
{
    protected $fillable = [
        'name', 'description', 'report_type', 'created_by', 'status',
        'sections', 'filters', 'frequency', 'scheduled_for',
    ];

    protected $casts = ['sections' => 'array', 'filters' => 'array', 'scheduled_for' => 'datetime'];

    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
    public function instances(): HasMany { return $this->hasMany(ReportInstance::class); }
    public function filterRules(): HasMany { return $this->hasMany(ReportFilter::class); }
}

class ReportInstance extends Model
{
    protected $fillable = [
        'report_id', 'date_generated', 'period_start', 'period_end', 'data',
        'format', 'file_path', 'status', 'error_message', 'download_count', 'expires_at',
    ];

    protected $casts = ['data' => 'array', 'expires_at' => 'datetime'];

    public function report(): BelongsTo { return $this->belongsTo(Report::class); }
}

class ScheduledReport extends Model
{
    protected $fillable = [
        'report_id', 'recipients', 'frequency', 'scheduled_time', 'days_of_week',
        'day_of_month', 'is_active', 'last_sent_at', 'next_send_at',
    ];

    protected $casts = ['recipients' => 'array', 'days_of_week' => 'array', 'scheduled_time' => 'datetime', 'last_sent_at' => 'datetime', 'next_send_at' => 'datetime'];

    public function report(): BelongsTo { return $this->belongsTo(Report::class); }
}

class ReportFilter extends Model
{
    protected $fillable = [
        'report_id', 'filter_name', 'filter_type', 'filter_options', 'default_value', 'is_required',
    ];

    protected $casts = ['filter_options' => 'array', 'default_value' => 'array'];

    public function report(): BelongsTo { return $this->belongsTo(Report::class); }
}

class MetricsAggregation extends Model
{
    protected $fillable = [
        'metric_key', 'date', 'granularity', 'dimensions', 'value', 'breakdown',
    ];

    protected $casts = ['dimensions' => 'array', 'breakdown' => 'array'];
}

class CohortAnalysis extends Model
{
    protected $fillable = [
        'cohort_name', 'cohort_date', 'cohort_size', 'period_number',
        'active_users', 'retention_percentage', 'revenue',
    ];
}

class FunnelAnalytic extends Model
{
    protected $table = 'funnel_analytics';
    protected $fillable = [
        'funnel_name', 'description', 'steps', 'date', 'total_users',
        'step_data', 'completion_rate',
    ];

    protected $casts = ['steps' => 'array', 'step_data' => 'array'];
}

class CustomEvent extends Model
{
    protected $table = 'custom_events';
    protected $fillable = [
        'event_name', 'description', 'properties', 'is_active',
    ];

    protected $casts = ['properties' => 'array'];
}

class EventTracking extends Model
{
    protected $fillable = [
        'user_id', 'custom_event_id', 'event_name', 'properties', 'context',
    ];

    protected $casts = ['properties' => 'array', 'context' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function event(): BelongsTo { return $this->belongsTo(CustomEvent::class, 'custom_event_id'); }
}

class SessionAnalytic extends Model
{
    protected $table = 'session_analytics';
    protected $fillable = [
        'user_id', 'session_id', 'session_start', 'session_end', 'duration_seconds',
        'page_views', 'events', 'entry_page', 'exit_page', 'device_type',
        'browser', 'os', 'pages_visited',
    ];

    protected $casts = ['session_start' => 'datetime', 'session_end' => 'datetime', 'pages_visited' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

class Goal extends Model
{
    protected $fillable = [
        'name', 'description', 'goal_type', 'conditions', 'is_active',
    ];

    protected $casts = ['conditions' => 'array'];

    public function conversions(): HasMany { return $this->hasMany(GoalConversion::class); }
}

class GoalConversion extends Model
{
    protected $fillable = [
        'goal_id', 'user_id', 'session_id', 'conversion_data',
    ];

    protected $casts = ['conversion_data' => 'array'];

    public function goal(): BelongsTo { return $this->belongsTo(Goal::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

class DashboardView extends Model
{
    protected $fillable = [
        'user_id', 'view_name', 'widgets', 'is_default', 'filters',
    ];

    protected $casts = ['widgets' => 'array', 'filters' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

class AnalyticsExport extends Model
{
    protected $fillable = [
        'user_id', 'export_name', 'export_type', 'filters', 'file_path',
        'status', 'error_message', 'record_count', 'expires_at', 'download_count',
    ];

    protected $casts = ['filters' => 'array', 'expires_at' => 'datetime'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}

class AlertRule extends Model
{
    protected $fillable = [
        'name', 'description', 'metric_name', 'condition', 'threshold',
        'frequency', 'recipients', 'is_active', 'last_triggered_at',
    ];

    protected $casts = ['recipients' => 'array', 'last_triggered_at' => 'datetime'];

    public function notifications(): HasMany { return $this->hasMany(AlertNotification::class); }
}

class AlertNotification extends Model
{
    protected $fillable = [
        'alert_rule_id', 'title', 'message', 'alert_data', 'severity',
        'status', 'acknowledged_at', 'resolved_at',
    ];

    protected $casts = ['alert_data' => 'array', 'acknowledged_at' => 'datetime', 'resolved_at' => 'datetime'];

    public function rule(): BelongsTo { return $this->belongsTo(AlertRule::class); }
}

class DataQualityMetric extends Model
{
    protected $fillable = [
        'metric_name', 'date', 'completeness', 'accuracy', 'consistency',
        'timeliness', 'overall_score', 'issues',
    ];

    protected $casts = ['issues' => 'array'];
}

class BenchmarkComparison extends Model
{
    protected $fillable = [
        'benchmark_name', 'metric_name', 'date', 'actual_value',
        'benchmark_value', 'variance', 'variance_percentage', 'status',
    ];
}

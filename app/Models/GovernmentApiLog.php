<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GovernmentApiLog extends Model
{
    protected $fillable = [
        'service',
        'endpoint',
        'method',
        'request_payload',
        'response_payload',
        'response_code',
        'status',
        'error_message',
        'response_time_ms',
        'ip_address',
    ];

    protected $casts = [
        'response_time_ms' => 'decimal:2',
    ];

    public function scopeByService($query, $service)
    {
        return $query->where('service', $service);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', '!=', 'success');
    }

    public function scopeRecent($query, $minutes = 60)
    {
        return $query->where('created_at', '>=', now()->subMinutes($minutes));
    }
}

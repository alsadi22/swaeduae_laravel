<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiCallLog extends Model
{
    protected $fillable = [
        'integration_name',
        'endpoint',
        'method',
        'request_payload',
        'response_code',
        'response_payload',
        'status',
        'response_time_ms',
        'error_message',
        'ip_address',
    ];

    protected $casts = [
        'response_time_ms' => 'decimal:2',
    ];

    public function scopeByIntegration($query, $name)
    {
        return $query->where('integration_name', $name);
    }

    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', '!=', 'success');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiIntegration extends Model
{
    protected $fillable = [
        'name',
        'service_type',
        'api_key',
        'api_secret',
        'api_url',
        'status',
        'description',
        'settings',
        'last_tested_at',
        'last_error',
    ];

    protected $casts = [
        'settings' => 'array',
        'last_tested_at' => 'datetime',
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}

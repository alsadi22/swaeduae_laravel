<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayConfig extends Model
{
    protected $fillable = [
        'gateway',
        'mode',
        'api_key',
        'secret_key',
        'webhook_secret',
        'is_active',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
    ];

    protected $hidden = [
        'api_key',
        'secret_key',
        'webhook_secret',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByGateway($query, $gateway)
    {
        return $query->where('gateway', $gateway);
    }

    public function scopeLive($query)
    {
        return $query->where('mode', 'live');
    }

    public function scopeTest($query)
    {
        return $query->where('mode', 'test');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WebhookSubscription extends Model
{
    protected $fillable = [
        'webhook_url',
        'event_type',
        'is_active',
        'filters',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'filters' => 'array',
    ];

    public function deliveryLogs(): HasMany
    {
        return $this->hasMany(WebhookDeliveryLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByEventType($query, $type)
    {
        return $query->where('event_type', $type);
    }
}

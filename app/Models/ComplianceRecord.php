<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ComplianceRecord extends Model
{
    protected $fillable = [
        'user_id',
        'check_type',
        'status',
        'details',
        'recommendations',
        'authority',
        'checked_at',
        'expires_at',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCompliant($query)
    {
        return $query->where('status', 'compliant');
    }

    public function scopeNonCompliant($query)
    {
        return $query->where('status', 'non_compliant');
    }

    public function scopeByCheckType($query, $type)
    {
        return $query->where('check_type', $type);
    }

    public function isCompliant(): bool
    {
        return $this->status === 'compliant';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }
}

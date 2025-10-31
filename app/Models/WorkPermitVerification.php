<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkPermitVerification extends Model
{
    protected $fillable = [
        'user_id',
        'work_permit_number',
        'sponsor_name',
        'sponsor_trade_license',
        'occupation',
        'status',
        'issue_date',
        'expiry_date',
        'permit_data',
        'error_message',
        'verified_at',
    ];

    protected $casts = [
        'permit_data' => 'array',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeValid($query)
    {
        return $query->where('status', 'valid')
                    ->where('expiry_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere('expiry_date', '<', now());
    }

    public function isValid(): bool
    {
        return $this->status === 'valid' && $this->expiry_date >= now();
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || $this->expiry_date < now();
    }
}

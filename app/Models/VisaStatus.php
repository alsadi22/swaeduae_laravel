<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisaStatus extends Model
{
    protected $fillable = [
        'user_id',
        'visa_type',
        'visa_number',
        'passport_number',
        'issue_date',
        'expiry_date',
        'status',
        'entry_point',
        'last_entry_date',
        'additional_data',
        'last_verified_at',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'last_entry_date' => 'date',
        'last_verified_at' => 'datetime',
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

    public function daysUntilExpiry(): ?int
    {
        if (!$this->expiry_date) {
            return null;
        }

        return now()->diffInDays($this->expiry_date, false);
    }
}

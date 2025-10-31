<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CriminalRecordCheck extends Model
{
    protected $fillable = [
        'user_id',
        'reference_number',
        'status',
        'result_message',
        'error_message',
        'response_data',
        'verified_at',
        'expires_at',
    ];

    protected $casts = [
        'response_data' => 'array',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeClear($query)
    {
        return $query->where('status', 'clear');
    }

    public function scopeNotClear($query)
    {
        return $query->where('status', 'not_clear');
    }

    public function isClear(): bool
    {
        return $this->status === 'clear';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at < now();
    }
}

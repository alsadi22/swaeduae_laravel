<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmiratesIdVerification extends Model
{
    protected $fillable = [
        'user_id',
        'emirates_id',
        'first_name_en',
        'last_name_en',
        'first_name_ar',
        'last_name_ar',
        'nationality',
        'date_of_birth',
        'gender',
        'status',
        'verification_data',
        'verification_error',
        'verified_at',
        'expires_at',
    ];

    protected $casts = [
        'verification_data' => 'array',
        'date_of_birth' => 'date',
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                    ->orWhere('expires_at', '<', now());
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || ($this->expires_at && $this->expires_at < now());
    }
}

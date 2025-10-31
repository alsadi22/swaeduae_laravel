<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'payment_method',
        'amount',
        'currency',
        'transaction_id',
        'status',
        'payment_type',
        'reference_id',
        'reference_type',
        'metadata',
        'failure_reason',
        'receipt_number',
        'completed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('payment_type', $type);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function canBeRefunded(): bool
    {
        return $this->status === 'completed' && !$this->refunds()->where('status', 'completed')->exists();
    }
}

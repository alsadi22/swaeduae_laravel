<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'balance',
        'total_spent',
        'total_received',
        'currency',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'total_spent' => 'decimal:2',
        'total_received' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function addBalance($amount)
    {
        $this->increment('balance', $amount);
        $this->increment('total_received', $amount);
    }

    public function deductBalance($amount)
    {
        if ($this->balance >= $amount) {
            $this->decrement('balance', $amount);
            $this->increment('total_spent', $amount);
            return true;
        }
        return false;
    }

    public function hassufficientBalance($amount): bool
    {
        return $this->balance >= $amount;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    protected $fillable = [
        'user_id',
        'stripe_payment_method_id',
        'type',
        'card_brand',
        'card_last_four',
        'card_expiry',
        'card_holder_name',
        'is_default',
        'is_active',
        'added_at',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'added_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function isExpired(): bool
    {
        if (!$this->card_expiry) {
            return false;
        }

        [$month, $year] = explode('/', $this->card_expiry);
        $expiryDate = \Carbon\Carbon::createFromDate(20 . $year, $month, 28);

        return $expiryDate->isPast();
    }
}

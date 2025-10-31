<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoiVerification extends Model
{
    protected $table = 'moi_verifications';

    protected $fillable = [
        'user_id',
        'reference_number',
        'name',
        'passport_number',
        'date_of_birth',
        'status',
        'response_data',
        'error_message',
        'verified_at',
    ];

    protected $casts = [
        'response_data' => 'array',
        'date_of_birth' => 'date',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVerified($query)
    {
        return $query->where('status', 'verified');
    }
}

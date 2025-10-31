<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PersonalizedRecommendation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'recommendation_type', 'item_id', 'item_type', 'reason',
        'ml_model_id', 'score', 'rank', 'explanation', 'clicked', 'clicked_at',
        'converted', 'converted_at',
    ];

    protected $casts = [
        'explanation' => 'array', 'clicked' => 'boolean', 'clicked_at' => 'datetime',
        'converted' => 'boolean', 'converted_at' => 'datetime',
    ];

    public function user(): BelongsTo 
    { 
        return $this->belongsTo(User::class); 
    }

    public function model(): BelongsTo 
    { 
        return $this->belongsTo(MlModel::class, 'ml_model_id'); 
    }

    public function scopeUnclicked($q) 
    { 
        return $q->where('clicked', false); 
    }
}

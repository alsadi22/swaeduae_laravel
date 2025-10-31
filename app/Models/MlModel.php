<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MlModel extends Model
{
    protected $table = 'ml_models';
    
    protected $fillable = [
        'name', 'type', 'version', 'status', 'accuracy', 'precision', 'recall',
        'training_samples', 'hyperparameters', 'description', 'trained_at', 'last_used_at',
    ];

    protected $casts = [
        'hyperparameters' => 'array', 
        'trained_at' => 'datetime', 
        'last_used_at' => 'datetime'
    ];

    public function scopeActive($q) 
    { 
        return $q->where('status', 'active'); 
    }
}

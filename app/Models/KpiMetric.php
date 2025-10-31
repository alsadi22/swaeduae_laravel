<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiMetric extends Model
{
    protected $fillable = [
        'name', 'display_name', 'category', 'calculation_method', 'formula',
        'is_active', 'unit', 'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function values()
    {
        return $this->hasMany(KpiValue::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}

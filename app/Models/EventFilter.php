<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventFilter extends Model
{
    protected $fillable = [
        'name',
        'value',
        'label',
        'usage_count',
    ];

    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }

    public function scopePopular($query, $limit = 20)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }
}

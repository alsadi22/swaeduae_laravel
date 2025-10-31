<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SearchSuggestion extends Model
{
    protected $fillable = [
        'query',
        'type',
        'target_id',
        'popularity',
    ];

    public function scopeByQuery($query, $search)
    {
        return $query->where('query', 'like', $search . '%');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('popularity', 'desc');
    }

    public static function incrementPopularity($query, $type, $targetId = null)
    {
        self::updateOrCreate(
            [
                'query' => $query,
                'type' => $type,
                'target_id' => $targetId,
            ],
            ['popularity' => \DB::raw('popularity + 1')]
        );
    }
}

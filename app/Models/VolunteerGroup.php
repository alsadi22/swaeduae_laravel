<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class VolunteerGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'slug',
        'image',
        'status',
        'member_count',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->slug = Str::slug($model->name);
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_memberships')
                    ->withPivot(['role', 'joined_at'])
                    ->withTimestamps();
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(GroupInvitation::class, 'group_id');
    }

    public function pendingInvitations(): HasMany
    {
        return $this->invitations()->where('status', 'pending');
    }
}

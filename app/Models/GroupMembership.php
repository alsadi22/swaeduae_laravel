<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GroupMembership extends Pivot
{
    protected $fillable = ['group_id', 'user_id', 'role', 'joined_at'];
    protected $casts = ['joined_at' => 'datetime'];
}

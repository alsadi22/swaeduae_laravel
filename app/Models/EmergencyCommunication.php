<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmergencyCommunication extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'organization_id',
        'created_by',
        'title',
        'content',
        'priority',
        'send_sms',
        'send_email',
        'send_push',
        'recipient_filters',
        'sent_at',
    ];

    protected $casts = [
        'send_sms' => 'boolean',
        'send_email' => 'boolean',
        'send_push' => 'boolean',
        'recipient_filters' => 'array',
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
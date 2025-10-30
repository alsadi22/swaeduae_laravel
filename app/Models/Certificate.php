<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_number',
        'user_id',
        'event_id',
        'organization_id',
        'type',
        'title',
        'description',
        'hours_completed',
        'event_date',
        'issued_date',
        'template',
        'custom_fields',
        'file_path',
        'verification_code',
        'is_verified',
        'verified_at',
        'verified_by',
        'is_public',
        'metadata',
    ];

    protected $casts = [
        'custom_fields' => 'array',
        'metadata' => 'array',
        'event_date' => 'date',
        'issued_date' => 'date',
        'verified_at' => 'datetime',
        'is_verified' => 'boolean',
        'is_public' => 'boolean',
        'hours_completed' => 'decimal:2',
    ];

    /**
     * Get the user that owns the certificate.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event associated with the certificate.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the organization that issued the certificate.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the user who verified the certificate.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Scope a query to only include verified certificates.
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope a query to only include public certificates.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Generate a unique certificate number.
     */
    public static function generateCertificateNumber()
    {
        do {
            $number = 'CERT-' . date('Y') . '-' . strtoupper(uniqid());
        } while (self::where('certificate_number', $number)->exists());

        return $number;
    }

    /**
     * Generate a unique verification code.
     */
    public static function generateVerificationCode()
    {
        do {
            $code = strtoupper(substr(md5(uniqid()), 0, 8));
        } while (self::where('verification_code', $code)->exists());

        return $code;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Send notification when certificate is created
        static::created(function ($certificate) {
            // Send notification to the user
            $certificate->user->sendCertificateNotification($certificate);
        });
        
        // Send notification when certificate is verified
        static::updated(function ($certificate) {
            // Check if certificate was verified
            if ($certificate->isDirty('is_verified') && $certificate->is_verified) {
                // Send notification to the user
                $certificate->user->sendCertificateNotification($certificate);
            }
        });
    }
}
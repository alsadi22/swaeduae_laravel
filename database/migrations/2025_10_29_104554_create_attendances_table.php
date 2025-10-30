<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            
            // Check-in information
            $table->timestamp('checked_in_at')->nullable();
            $table->decimal('checkin_latitude', 10, 8)->nullable();
            $table->decimal('checkin_longitude', 11, 8)->nullable();
            $table->string('checkin_qr_code')->nullable();
            $table->string('checkin_device_info')->nullable();
            $table->text('checkin_notes')->nullable();
            
            // Check-out information
            $table->timestamp('checked_out_at')->nullable();
            $table->decimal('checkout_latitude', 10, 8)->nullable();
            $table->decimal('checkout_longitude', 11, 8)->nullable();
            $table->string('checkout_qr_code')->nullable();
            $table->string('checkout_device_info')->nullable();
            $table->text('checkout_notes')->nullable();
            
            // Attendance validation
            $table->boolean('is_valid_checkin')->default(true);
            $table->boolean('is_valid_checkout')->default(true);
            $table->decimal('distance_from_event', 8, 2)->nullable(); // in meters
            $table->integer('total_hours')->nullable(); // calculated hours
            $table->decimal('actual_hours', 5, 2)->nullable(); // actual calculated hours
            
            // Status and verification
            $table->enum('status', ['checked_in', 'checked_out', 'no_show', 'late', 'early_departure'])->default('checked_in');
            $table->boolean('verified_by_organizer')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            
            // Additional tracking
            $table->json('metadata')->nullable(); // For additional tracking data
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['user_id', 'event_id']);
            $table->index(['event_id', 'status']);
            $table->index('checked_in_at');
            $table->index('checked_out_at');
            
            // Unique constraint to prevent duplicate attendance records
            $table->unique(['user_id', 'event_id', 'application_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

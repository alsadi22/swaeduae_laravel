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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'approved', 'rejected', 'waitlisted', 'cancelled'])->default('pending');
            $table->text('motivation')->nullable(); // Why they want to volunteer
            $table->json('skills')->nullable(); // Skills they bring
            $table->json('availability')->nullable(); // Their availability
            $table->text('experience')->nullable(); // Previous volunteer experience
            $table->json('custom_responses')->nullable(); // Responses to custom fields
            $table->json('documents')->nullable(); // Uploaded documents
            $table->text('rejection_reason')->nullable();
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->boolean('attended')->default(false);
            $table->datetime('checked_in_at')->nullable();
            $table->datetime('checked_out_at')->nullable();
            $table->decimal('hours_completed', 5, 2)->nullable();
            $table->integer('rating')->nullable(); // Organization rating of volunteer (1-5)
            $table->text('feedback')->nullable(); // Organization feedback
            $table->text('volunteer_feedback')->nullable(); // Volunteer feedback about event
            $table->integer('volunteer_rating')->nullable(); // Volunteer rating of event (1-5)
            $table->timestamps();
            
            $table->unique(['user_id', 'event_id']); // Prevent duplicate applications
            $table->index(['status', 'applied_at']);
            $table->index('event_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('volunteer'); // volunteer, achievement, completion
            $table->string('title');
            $table->text('description');
            $table->decimal('hours_completed', 5, 2);
            $table->date('event_date');
            $table->date('issued_date');
            $table->string('template')->nullable(); // Certificate template used
            $table->json('custom_fields')->nullable(); // Additional certificate data
            $table->string('file_path')->nullable(); // Generated PDF path
            $table->string('verification_code')->unique(); // For public verification
            $table->boolean('is_verified')->default(true);
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->boolean('is_public')->default(true); // Can be shared publicly
            $table->json('metadata')->nullable(); // Additional metadata
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index('verification_code');
            $table->index('certificate_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};

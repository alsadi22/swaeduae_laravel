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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('category');
            $table->json('tags')->nullable();
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('location');
            $table->text('address');
            $table->string('city');
            $table->string('emirate');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->integer('max_volunteers');
            $table->integer('min_age')->default(16);
            $table->integer('max_age')->nullable();
            $table->json('skills_required')->nullable();
            $table->decimal('volunteer_hours', 5, 2); // Hours volunteers will earn
            $table->string('image')->nullable();
            $table->json('gallery')->nullable(); // Additional images
            $table->enum('status', ['draft', 'pending', 'approved', 'published', 'cancelled', 'completed'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->boolean('is_featured')->default(false);
            $table->boolean('requires_application')->default(true);
            $table->datetime('application_deadline')->nullable();
            $table->text('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->json('custom_fields')->nullable(); // For additional application fields
            $table->timestamps();
            
            $table->index(['status', 'start_date']);
            $table->index(['emirate', 'category']);
            $table->index('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};

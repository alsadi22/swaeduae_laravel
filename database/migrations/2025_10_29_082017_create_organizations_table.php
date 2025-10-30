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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->text('address');
            $table->string('city');
            $table->string('emirate');
            $table->string('postal_code')->nullable();
            $table->string('logo')->nullable();
            $table->json('documents')->nullable(); // Store uploaded documents
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->boolean('is_verified')->default(false);
            $table->json('social_media')->nullable(); // Store social media links
            $table->text('mission_statement')->nullable();
            $table->integer('founded_year')->nullable();
            $table->enum('organization_type', ['ngo', 'charity', 'government', 'educational', 'corporate', 'community'])->default('ngo');
            $table->json('focus_areas')->nullable(); // Areas of work (environment, education, health, etc.)
            $table->timestamps();
            
            $table->index(['status', 'is_verified']);
            $table->index('emirate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};

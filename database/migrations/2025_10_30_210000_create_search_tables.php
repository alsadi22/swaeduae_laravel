<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create search history table
        Schema::create('search_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('query');
            $table->string('search_type'); // event, user, organization, all
            $table->integer('results_count')->default(0);
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['query']);
        });

        // Create saved searches table
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('search_type'); // event, user, organization, all
            $table->json('filters'); // Store search filters
            $table->string('query')->nullable();
            $table->boolean('notify_on_match')->default(false);
            $table->integer('notification_count')->default(0);
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'name']);
        });

        // Create favorites/bookmarks table
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('favoritable_type'); // Event, User, Organization
            $table->unsignedBigInteger('favoritable_id');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'favoritable_type', 'favoritable_id']);
            $table->index(['user_id', 'created_at']);
        });

        // Create event filters metadata table
        Schema::create('event_filters', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // event_type, difficulty, distance, etc.
            $table->string('value');
            $table->string('label');
            $table->integer('usage_count')->default(0);
            $table->timestamps();
            
            $table->unique(['name', 'value']);
        });

        // Create recommendations table
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('recommended_type'); // Event, User, Organization
            $table->unsignedBigInteger('recommended_id');
            $table->string('reason'); // interest_match, skill_match, location_match, etc.
            $table->decimal('score', 5, 2)->default(0); // Recommendation score
            $table->json('metadata')->nullable();
            $table->boolean('clicked')->default(false);
            $table->timestamp('clicked_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'clicked']);
        });

        // Create search suggestions table for autocomplete
        Schema::create('search_suggestions', function (Blueprint $table) {
            $table->id();
            $table->string('query');
            $table->string('type'); // event, organization, volunteer, tag
            $table->unsignedBigInteger('target_id')->nullable();
            $table->integer('popularity')->default(0);
            $table->timestamps();
            
            $table->unique(['query', 'type', 'target_id']);
            $table->index(['query']);
            $table->index(['popularity']);
        });

        // Create user search preferences
        Schema::create('search_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->json('preferred_categories')->nullable();
            $table->json('preferred_locations')->nullable();
            $table->json('preferred_skills')->nullable();
            $table->json('excluded_categories')->nullable();
            $table->json('excluded_locations')->nullable();
            $table->integer('max_distance_km')->nullable();
            $table->boolean('auto_recommendations')->default(true);
            $table->string('recommendation_frequency')->default('weekly'); // daily, weekly, monthly
            $table->timestamps();
            
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_preferences');
        Schema::dropIfExists('search_suggestions');
        Schema::dropIfExists('recommendations');
        Schema::dropIfExists('event_filters');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('saved_searches');
        Schema::dropIfExists('search_histories');
    }
};

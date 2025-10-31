<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // User behavior tracking
        Schema::create('user_behaviors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action_type'); // view, click, share, apply, complete
            $table->string('entity_type'); // Event, Badge, Certificate, User, Organization
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->json('metadata')->nullable();
            $table->decimal('engagement_score', 5, 2)->default(0);
            $table->string('device_type')->nullable();
            $table->integer('duration_seconds')->nullable();
            $table->string('referrer')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['entity_type', 'entity_id']);
            $table->index(['action_type']);
        });

        // User preferences learning
        Schema::create('user_preference_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->json('preferred_event_types')->nullable();
            $table->json('preferred_skills')->nullable();
            $table->json('preferred_locations')->nullable();
            $table->json('preferred_organizations')->nullable();
            $table->json('disliked_categories')->nullable();
            $table->decimal('average_engagement_score', 5, 2)->default(0);
            $table->integer('total_interactions')->default(0);
            $table->timestamp('last_updated_at');
            $table->timestamps();
        });

        // ML model training data
        Schema::create('ml_training_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('feature_set'); // event_features, user_features, etc.
            $table->json('features');
            $table->json('labels')->nullable();
            $table->string('model_type'); // recommendation, clustering, etc.
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'model_type']);
        });

        // ML model metadata
        Schema::create('ml_models', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('type'); // recommendation, clustering, prediction
            $table->string('version');
            $table->enum('status', ['training', 'active', 'archived', 'error'])->default('training');
            $table->decimal('accuracy', 5, 2)->nullable();
            $table->decimal('precision', 5, 2)->nullable();
            $table->decimal('recall', 5, 2)->nullable();
            $table->integer('training_samples')->default(0);
            $table->json('hyperparameters')->nullable();
            $table->text('description')->nullable();
            $table->timestamp('trained_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        // Personalized recommendations
        Schema::create('personalized_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('recommendation_type'); // event, volunteer, organization, content
            $table->unsignedBigInteger('item_id');
            $table->string('item_type');
            $table->string('reason'); // behavior_based, preference_based, collaborative_filtering
            $table->foreignId('ml_model_id')->nullable()->constrained('ml_models')->onDelete('set null');
            $table->decimal('score', 5, 2);
            $table->integer('rank')->nullable();
            $table->json('explanation')->nullable();
            $table->boolean('clicked')->default(false);
            $table->timestamp('clicked_at')->nullable();
            $table->boolean('converted')->default(false);
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['clicked']);
            $table->index(['converted']);
        });

        // Content personalization
        Schema::create('content_personalizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('content_type'); // event, opportunity, challenge
            $table->unsignedBigInteger('content_id');
            $table->string('variant'); // A/B test variant
            $table->text('personalized_title')->nullable();
            $table->text('personalized_description')->nullable();
            $table->json('personalized_metadata')->nullable();
            $table->boolean('is_shown')->default(false);
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('conversions')->default(0);
            $table->timestamps();
        });

        // A/B testing
        Schema::create('ab_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('entity_type'); // event, ui, notification
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled'])->default('active');
            $table->json('variants');
            $table->integer('total_users')->default(0);
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        // A/B test results
        Schema::create('ab_test_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ab_test_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('variant');
            $table->integer('impressions')->default(0);
            $table->integer('conversions')->default(0);
            $table->decimal('conversion_rate', 5, 2)->nullable();
            $table->json('metrics')->nullable();
            $table->timestamps();
            
            $table->index(['ab_test_id', 'user_id']);
        });

        // User insights
        Schema::create('user_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->integer('engagement_level')->default(1); // 1-5 scale
            $table->integer('activity_frequency')->default(0); // actions per week
            $table->string('volunteer_type')->nullable(); // type based on behavior
            $table->integer('estimated_lifetime_value')->default(0);
            $table->json('behavior_patterns')->nullable();
            $table->json('risk_indicators')->nullable();
            $table->json('opportunities')->nullable();
            $table->timestamp('last_analyzed_at')->nullable();
            $table->timestamps();
        });

        // Predictive models results
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('prediction_type'); // churn_risk, conversion_probability, retention_score
            $table->decimal('predicted_value', 5, 2);
            $table->decimal('confidence', 5, 2);
            $table->json('factors')->nullable();
            $table->boolean('actual_result')->nullable();
            $table->timestamp('prediction_date');
            $table->timestamp('result_date')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'prediction_type']);
        });

        // Content similarity cache
        Schema::create('content_similarities', function (Blueprint $table) {
            $table->id();
            $table->string('content_type'); // event, course, badge
            $table->unsignedBigInteger('content_id_1');
            $table->unsignedBigInteger('content_id_2');
            $table->decimal('similarity_score', 5, 2);
            $table->timestamps();
            
            $table->unique(['content_type', 'content_id_1', 'content_id_2']);
            $table->index(['content_type']);
        });

        // Feature flags for personalization
        Schema::create('feature_flags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->string('target_group')->nullable(); // all, users, organizations
            $table->json('conditions')->nullable();
            $table->decimal('rollout_percentage', 5, 2)->default(100);
            $table->timestamps();
        });

        // User cohorts for segmentation
        Schema::create('user_cohorts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->json('criteria');
            $table->integer('user_count')->default(0);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        // User cohort assignments
        Schema::create('cohort_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cohort_id')->constrained('user_cohorts')->onDelete('cascade');
            $table->timestamp('assigned_at');
            $table->timestamps();
            
            $table->unique(['user_id', 'cohort_id']);
        });

        // Engagement metrics
        Schema::create('engagement_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('events_viewed')->default(0);
            $table->integer('events_applied')->default(0);
            $table->integer('events_completed')->default(0);
            $table->integer('badges_earned')->default(0);
            $table->integer('messages_sent')->default(0);
            $table->decimal('hours_volunteered', 8, 2)->default(0);
            $table->integer('login_count')->default(0);
            $table->decimal('daily_engagement_score', 5, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['user_id', 'date']);
            $table->index(['user_id', 'date']);
        });

        // Churn prediction tracking
        Schema::create('churn_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('churn_probability', 5, 2);
            $table->json('risk_factors')->nullable();
            $table->string('risk_level'); // low, medium, high
            $table->text('recommended_action')->nullable();
            $table->boolean('intervened')->default(false);
            $table->string('intervention_type')->nullable();
            $table->timestamp('intervention_date')->nullable();
            $table->boolean('churned')->nullable();
            $table->timestamp('churned_date')->nullable();
            $table->timestamps();
            
            $table->index(['user_id']);
            $table->index(['risk_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('churn_predictions');
        Schema::dropIfExists('engagement_metrics');
        Schema::dropIfExists('cohort_assignments');
        Schema::dropIfExists('user_cohorts');
        Schema::dropIfExists('feature_flags');
        Schema::dropIfExists('content_similarities');
        Schema::dropIfExists('predictions');
        Schema::dropIfExists('user_insights');
        Schema::dropIfExists('ab_test_results');
        Schema::dropIfExists('ab_tests');
        Schema::dropIfExists('content_personalizations');
        Schema::dropIfExists('personalized_recommendations');
        Schema::dropIfExists('ml_models');
        Schema::dropIfExists('ml_training_data');
        Schema::dropIfExists('user_preference_profiles');
        Schema::dropIfExists('user_behaviors');
    }
};

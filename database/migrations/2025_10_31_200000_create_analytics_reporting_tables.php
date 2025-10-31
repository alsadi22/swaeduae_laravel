<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Analytics events
        Schema::create('analytics_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('event_type'); // page_view, event_apply, badge_earned, etc.
            $table->string('event_category');
            $table->string('event_label')->nullable();
            $table->json('event_data')->nullable();
            $table->string('page_url')->nullable();
            $table->string('referrer')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('ip_address')->nullable();
            $table->unsignedInteger('session_duration_seconds')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['event_type', 'created_at']);
            $table->index(['event_category']);
            $table->index(['created_at']);
        });

        // Dashboard widgets
        Schema::create('dashboard_widgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('widget_type'); // kpi, chart, table, gauge
            $table->string('metric_name');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('configuration')->nullable();
            $table->integer('position')->nullable();
            $table->string('size')->default('md'); // sm, md, lg, xl
            $table->boolean('is_visible')->default(true);
            $table->json('filters')->nullable();
            $table->timestamps();
            
            $table->index(['user_id']);
        });

        // KPI metrics
        Schema::create('kpi_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_name')->unique();
            $table->string('display_name');
            $table->string('unit')->nullable();
            $table->string('calculation_method'); // sum, count, average, percentage
            $table->text('sql_query')->nullable();
            $table->json('formula')->nullable();
            $table->string('category'); // engagement, growth, revenue, quality
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // KPI values (time-series data)
        Schema::create('kpi_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_metric_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('value', 12, 2);
            $table->decimal('target_value', 12, 2)->nullable();
            $table->decimal('previous_value', 12, 2)->nullable();
            $table->decimal('change_percentage', 8, 2)->nullable();
            $table->string('trend')->nullable(); // up, down, neutral
            $table->json('breakdown')->nullable();
            $table->timestamps();
            
            $table->unique(['kpi_metric_id', 'date']);
            $table->index(['kpi_metric_id', 'date']);
        });

        // Reports
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('report_type'); // executive, detailed, financial, operational
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->json('sections')->nullable();
            $table->json('filters')->nullable();
            $table->string('frequency')->nullable(); // one_time, daily, weekly, monthly
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamps();
        });

        // Report instances (generated reports)
        Schema::create('report_instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->date('date_generated');
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->json('data')->nullable();
            $table->string('format')->default('pdf'); // pdf, excel, json
            $table->string('file_path')->nullable();
            $table->enum('status', ['generating', 'ready', 'failed', 'expired'])->default('generating');
            $table->text('error_message')->nullable();
            $table->integer('download_count')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['report_id', 'date_generated']);
        });

        // Scheduled reports - skip if already exists
        if (!Schema::hasTable('scheduled_reports')) {
            Schema::create('scheduled_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('report_id')->constrained()->onDelete('cascade');
                $table->json('recipients')->nullable();
                $table->string('frequency'); // daily, weekly, monthly, quarterly
                $table->time('scheduled_time');
                $table->json('days_of_week')->nullable(); // for weekly
                $table->integer('day_of_month')->nullable(); // for monthly
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_sent_at')->nullable();
                $table->timestamp('next_send_at')->nullable();
                $table->timestamps();
            });
        }

        // Report filters
        Schema::create('report_filters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->string('filter_name');
            $table->string('filter_type'); // date_range, category, status, user
            $table->json('filter_options')->nullable();
            $table->json('default_value')->nullable();
            $table->boolean('is_required')->default(false);
            $table->timestamps();
        });

        // Metrics aggregation (cached)
        Schema::create('metrics_aggregations', function (Blueprint $table) {
            $table->id();
            $table->string('metric_key');
            $table->date('date');
            $table->string('granularity'); // hourly, daily, weekly, monthly
            $table->json('dimensions')->nullable();
            $table->decimal('value', 12, 2);
            $table->json('breakdown')->nullable();
            $table->timestamps();
            
            $table->unique(['metric_key', 'date', 'granularity']);
            $table->index(['metric_key', 'date']);
        });

        // Cohort analysis
        Schema::create('cohort_analysis', function (Blueprint $table) {
            $table->id();
            $table->string('cohort_name');
            $table->date('cohort_date');
            $table->integer('cohort_size');
            $table->integer('period_number');
            $table->integer('active_users')->default(0);
            $table->decimal('retention_percentage', 5, 2)->nullable();
            $table->decimal('revenue', 12, 2)->nullable();
            $table->timestamps();
            
            $table->unique(['cohort_name', 'cohort_date', 'period_number']);
        });

        // Funnel analysis
        Schema::create('funnel_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('funnel_name')->unique();
            $table->text('description')->nullable();
            $table->json('steps');
            $table->date('date');
            $table->integer('total_users')->default(0);
            $table->json('step_data')->nullable(); // step -> user_count, drop_off
            $table->decimal('completion_rate', 5, 2)->nullable();
            $table->timestamps();
            
            $table->index(['funnel_name', 'date']);
        });

        // Custom events
        Schema::create('custom_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_name')->unique();
            $table->text('description')->nullable();
            $table->json('properties')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Event tracking
        Schema::create('event_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('custom_event_id')->nullable()->constrained('custom_events')->onDelete('set null');
            $table->string('event_name');
            $table->json('properties')->nullable();
            $table->json('context')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['event_name', 'created_at']);
        });

        // Session analytics
        Schema::create('session_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->timestamp('session_start');
            $table->timestamp('session_end')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->integer('page_views')->default(0);
            $table->integer('events')->default(0);
            $table->string('entry_page')->nullable();
            $table->string('exit_page')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->json('pages_visited')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'session_start']);
            $table->index(['session_start']);
        });

        // Goal tracking
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->string('goal_type'); // event, pageview, duration, ecommerce
            $table->json('conditions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Goal conversions
        Schema::create('goal_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('goal_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('session_id')->nullable();
            $table->json('conversion_data')->nullable();
            $table->timestamps();
            
            $table->index(['goal_id', 'created_at']);
            $table->index(['user_id']);
        });

        // Dashboard views
        Schema::create('dashboard_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('view_name');
            $table->json('widgets')->nullable();
            $table->boolean('is_default')->default(false);
            $table->json('filters')->nullable();
            $table->timestamps();
            
            $table->index(['user_id']);
        });

        // Analytics exports
        Schema::create('analytics_exports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('export_name');
            $table->string('export_type'); // csv, excel, json, pdf
            $table->json('filters')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('status', ['pending', 'processing', 'ready', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->integer('record_count')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('download_count')->default(0);
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
        });

        // Alert rules
        Schema::create('alert_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('metric_name');
            $table->string('condition'); // above, below, equals, changes
            $table->decimal('threshold', 12, 2)->nullable();
            $table->string('frequency'); // immediate, daily, weekly
            $table->json('recipients')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();
        });

        // Alert notifications
        Schema::create('alert_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_rule_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->json('alert_data')->nullable();
            $table->enum('severity', ['info', 'warning', 'critical'])->default('warning');
            $table->enum('status', ['pending', 'sent', 'acknowledged', 'resolved'])->default('pending');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['alert_rule_id', 'created_at']);
            $table->index(['status']);
        });

        // Data quality metrics
        Schema::create('data_quality_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('metric_name');
            $table->date('date');
            $table->decimal('completeness', 5, 2);
            $table->decimal('accuracy', 5, 2);
            $table->decimal('consistency', 5, 2);
            $table->decimal('timeliness', 5, 2);
            $table->decimal('overall_score', 5, 2);
            $table->json('issues')->nullable();
            $table->timestamps();
            
            $table->unique(['metric_name', 'date']);
        });

        // Benchmark comparisons
        Schema::create('benchmark_comparisons', function (Blueprint $table) {
            $table->id();
            $table->string('benchmark_name');
            $table->string('metric_name');
            $table->date('date');
            $table->decimal('actual_value', 12, 2);
            $table->decimal('benchmark_value', 12, 2);
            $table->decimal('variance', 12, 2);
            $table->decimal('variance_percentage', 8, 2);
            $table->string('status'); // above, below, equal
            $table->timestamps();
            
            $table->index(['benchmark_name', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('benchmark_comparisons');
        Schema::dropIfExists('data_quality_metrics');
        Schema::dropIfExists('alert_notifications');
        Schema::dropIfExists('alert_rules');
        Schema::dropIfExists('analytics_exports');
        Schema::dropIfExists('dashboard_views');
        Schema::dropIfExists('goal_conversions');
        Schema::dropIfExists('goals');
        Schema::dropIfExists('session_analytics');
        Schema::dropIfExists('event_tracking');
        Schema::dropIfExists('custom_events');
        Schema::dropIfExists('funnel_analytics');
        Schema::dropIfExists('cohort_analysis');
        Schema::dropIfExists('metrics_aggregations');
        Schema::dropIfExists('report_filters');
        Schema::dropIfExists('scheduled_reports');
        Schema::dropIfExists('report_instances');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('kpi_values');
        Schema::dropIfExists('kpi_metrics');
        Schema::dropIfExists('dashboard_widgets');
        Schema::dropIfExists('analytics_events');
    }
};

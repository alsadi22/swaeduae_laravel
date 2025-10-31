<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SMS notifications log
        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('phone_number');
            $table->text('message');
            $table->string('provider'); // twilio, aws_sns, local
            $table->string('message_id')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'delivered'])->default('pending');
            $table->string('delivery_status')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['status']);
        });

        // WhatsApp notifications log
        Schema::create('whatsapp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('phone_number');
            $table->string('message_type'); // text, media, template
            $table->text('content');
            $table->string('provider'); // twilio, aws_sns, local
            $table->string('message_id')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'read'])->default('pending');
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['status']);
        });

        // Email logs
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('recipient_email');
            $table->string('subject');
            $table->string('email_type'); // verification, notification, report, etc.
            $table->text('body');
            $table->string('provider')->default('zoho'); // zoho, sendgrid, mailgun
            $table->string('message_id')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed', 'bounced'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->integer('open_count')->default(0);
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['status']);
        });

        // Push notifications log
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_token');
            $table->string('title');
            $table->text('body');
            $table->string('notification_type'); // event, message, reminder, badge
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->boolean('is_read')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['status']);
        });

        // Device tokens for push notifications
        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('token')->unique();
            $table->string('device_type'); // ios, android, web
            $table->string('device_name')->nullable();
            $table->string('app_version')->nullable();
            $table->string('os_version')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'is_active']);
        });

        // Notification preferences
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->boolean('email_notifications')->default(true);
            $table->boolean('sms_notifications')->default(true);
            $table->boolean('whatsapp_notifications')->default(true);
            $table->boolean('push_notifications')->default(true);
            $table->boolean('event_notifications')->default(true);
            $table->boolean('marketing_notifications')->default(false);
            $table->boolean('reminder_notifications')->default(true);
            $table->boolean('digest_notifications')->default(true);
            $table->string('digest_frequency')->default('weekly'); // daily, weekly, monthly
            $table->json('notification_categories')->nullable();
            $table->time('quiet_hours_start')->nullable();
            $table->time('quiet_hours_end')->nullable();
            $table->timestamps();
        });

        // Real-time messages - skip if already exists from previous migration
        if (!Schema::hasTable('messages')) {
            Schema::create('messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');
                $table->text('content');
                $table->string('message_type')->default('text'); // text, media, system
                $table->string('media_url')->nullable();
                $table->string('media_type')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->softDeletes();
                $table->timestamps();
                
                $table->index(['sender_id', 'recipient_id', 'created_at']);
                $table->index(['recipient_id', 'is_read']);
            });
        }

        // Message conversations
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id_1')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id_2')->constrained('users')->onDelete('cascade');
            $table->text('last_message')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->integer('unread_count_1')->default(0);
            $table->integer('unread_count_2')->default(0);
            $table->timestamps();
            
            $table->unique(['user_id_1', 'user_id_2']);
            $table->index(['user_id_1', 'last_message_at']);
        });

        // API integrations configuration
        Schema::create('api_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // google_maps, weather, news, etc.
            $table->string('service_type');
            $table->string('api_key')->encrypted()->nullable();
            $table->string('api_secret')->encrypted()->nullable();
            $table->string('api_url');
            $table->enum('status', ['active', 'inactive', 'error'])->default('active');
            $table->text('description')->nullable();
            $table->json('settings')->nullable();
            $table->timestamp('last_tested_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });

        // API call logs
        Schema::create('api_call_logs', function (Blueprint $table) {
            $table->id();
            $table->string('integration_name');
            $table->string('endpoint');
            $table->string('method'); // GET, POST, PUT, DELETE
            $table->text('request_payload')->nullable();
            $table->integer('response_code')->nullable();
            $table->text('response_payload')->nullable();
            $table->enum('status', ['success', 'failed', 'timeout', 'error'])->default('error');
            $table->decimal('response_time_ms', 8, 2)->nullable();
            $table->text('error_message')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['integration_name', 'created_at']);
            $table->index(['status']);
        });

        // Webhook subscriptions
        Schema::create('webhook_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('webhook_url');
            $table->string('event_type'); // user.created, event.updated, etc.
            $table->boolean('is_active')->default(true);
            $table->json('filters')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            
            $table->index(['event_type', 'is_active']);
        });

        // Webhook delivery logs
        Schema::create('webhook_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('webhook_subscription_id')->constrained()->onDelete('cascade');
            $table->string('event_type');
            $table->json('payload');
            $table->integer('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->enum('status', ['pending', 'delivered', 'failed', 'retrying'])->default('pending');
            $table->integer('attempt_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();
            
            $table->index(['webhook_subscription_id', 'created_at']);
            $table->index(['status']);
        });

        // Integration service logs
        Schema::create('service_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->string('action');
            $table->enum('status', ['success', 'failed', 'warning', 'info'])->default('info');
            $table->text('message');
            $table->json('context')->nullable();
            $table->timestamps();
            
            $table->index(['service_name', 'created_at']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_logs');
        Schema::dropIfExists('webhook_delivery_logs');
        Schema::dropIfExists('webhook_subscriptions');
        Schema::dropIfExists('api_call_logs');
        Schema::dropIfExists('api_integrations');
        Schema::dropIfExists('conversations');
        if (Schema::hasTable('messages')) {
            Schema::dropIfExists('messages');
        }
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('push_notifications');
        Schema::dropIfExists('email_logs');
        Schema::dropIfExists('whatsapp_logs');
        Schema::dropIfExists('sms_logs');
    }
};

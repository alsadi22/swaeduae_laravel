<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add social auth columns to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable()->unique()->after('id');
            $table->string('facebook_id')->nullable()->unique()->after('google_id');
            $table->string('apple_id')->nullable()->unique()->after('facebook_id');
        });

        // Create social auth tokens table
        Schema::create('social_auth_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider'); // google, facebook, apple
            $table->string('provider_id')->unique();
            $table->json('provider_data')->nullable();
            $table->string('access_token')->nullable();
            $table->string('refresh_token')->nullable();
            $table->timestamp('token_expires_at')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'provider']);
        });

        // Create activity feed table
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('action'); // joined_event, earned_badge, completed_event, etc.
            $table->string('subject_type')->nullable(); // Event, Badge, Certificate, etc.
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('data')->nullable();
            $table->text('description')->nullable();
            $table->string('visibility')->default('public'); // public, private, followers
            $table->timestamp('created_at');
            $table->index(['user_id', 'created_at']);
            $table->index(['created_at']);
        });

        // Create volunteer groups table
        Schema::create('volunteer_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('slug')->unique();
            $table->text('image')->nullable();
            $table->enum('status', ['active', 'inactive', 'archived'])->default('active');
            $table->integer('member_count')->default(1);
            $table->timestamps();
        });

        // Create group membership table
        Schema::create('group_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('volunteer_groups')->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('role', ['admin', 'moderator', 'member'])->default('member');
            $table->timestamp('joined_at');
            $table->timestamps();
            
            $table->unique(['group_id', 'user_id']);
        });

        // Create group invitations table
        Schema::create('group_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('volunteer_groups')->onDelete('cascade');
            $table->foreignId('invited_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('invited_user')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('email')->nullable(); // For inviting non-users
            $table->enum('status', ['pending', 'accepted', 'rejected', 'expired'])->default('pending');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();
        });

        // Create social shares table for tracking achievements
        Schema::create('social_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('content_type'); // certificate, badge, milestone
            $table->unsignedBigInteger('content_id');
            $table->string('platform'); // twitter, linkedin, whatsapp, facebook
            $table->json('share_data')->nullable();
            $table->timestamp('shared_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_shares');
        Schema::dropIfExists('group_invitations');
        Schema::dropIfExists('group_memberships');
        Schema::dropIfExists('volunteer_groups');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('social_auth_providers');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['google_id', 'facebook_id', 'apple_id']);
        });
    }
};

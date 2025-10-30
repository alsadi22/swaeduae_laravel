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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->enum('gender', ['male', 'female', 'other', 'prefer_not_to_say'])->nullable()->after('date_of_birth');
            $table->string('nationality')->nullable()->after('gender');
            $table->string('emirates_id')->nullable()->unique()->after('nationality');
            $table->text('address')->nullable()->after('emirates_id');
            $table->string('city')->nullable()->after('address');
            $table->string('emirate')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('emirate');
            $table->string('emergency_contact_name')->nullable()->after('postal_code');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_phone');
            $table->json('skills')->nullable()->after('emergency_contact_relationship');
            $table->json('interests')->nullable()->after('skills');
            $table->text('bio')->nullable()->after('interests');
            $table->string('avatar')->nullable()->after('bio');
            $table->json('languages')->nullable()->after('avatar');
            $table->string('education_level')->nullable()->after('languages');
            $table->string('occupation')->nullable()->after('education_level');
            $table->boolean('has_transportation')->default(false)->after('occupation');
            $table->json('availability')->nullable()->after('has_transportation'); // Days/times available
            $table->decimal('total_volunteer_hours', 8, 2)->default(0)->after('availability');
            $table->integer('total_events_attended')->default(0)->after('total_volunteer_hours');
            $table->integer('points')->default(0)->after('total_events_attended'); // Gamification points
            $table->boolean('is_verified')->default(false)->after('points');
            $table->timestamp('verified_at')->nullable()->after('is_verified');
            $table->boolean('profile_completed')->default(false)->after('verified_at');
            $table->json('notification_preferences')->nullable()->after('profile_completed');
            $table->json('privacy_settings')->nullable()->after('notification_preferences');
            $table->timestamp('last_active_at')->nullable()->after('privacy_settings');
            
            $table->index(['emirate', 'city']);
            $table->index('total_volunteer_hours');
            $table->index('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'date_of_birth', 'gender', 'nationality', 'emirates_id',
                'address', 'city', 'emirate', 'postal_code',
                'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_relationship',
                'skills', 'interests', 'bio', 'avatar', 'languages',
                'education_level', 'occupation', 'has_transportation', 'availability',
                'total_volunteer_hours', 'total_events_attended', 'points',
                'is_verified', 'verified_at', 'profile_completed',
                'notification_preferences', 'privacy_settings', 'last_active_at'
            ]);
        });
    }
};

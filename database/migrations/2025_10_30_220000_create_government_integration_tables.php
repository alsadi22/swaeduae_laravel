<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Emirates ID verification table
        Schema::create('emirates_id_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('emirates_id')->unique();
            $table->string('first_name_en');
            $table->string('last_name_en');
            $table->string('first_name_ar')->nullable();
            $table->string('last_name_ar')->nullable();
            $table->string('nationality');
            $table->date('date_of_birth');
            $table->string('gender')->nullable(); // M, F
            $table->enum('status', ['pending', 'verified', 'failed', 'expired'])->default('pending');
            $table->json('verification_data')->nullable();
            $table->text('verification_error')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });

        // MOI verification (Ministry of Interior)
        Schema::create('moi_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->string('name');
            $table->string('passport_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected', 'not_found'])->default('pending');
            $table->json('response_data')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });

        // MOHRE work permit verification
        Schema::create('work_permit_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('work_permit_number')->unique();
            $table->string('sponsor_name');
            $table->string('sponsor_trade_license');
            $table->string('occupation');
            $table->enum('status', ['pending', 'valid', 'expired', 'cancelled', 'not_found'])->default('pending');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->json('permit_data')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['expiry_date']);
        });

        // Visa status tracking
        Schema::create('visa_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('visa_type'); // tourist, residence, transit, visit
            $table->string('visa_number')->nullable();
            $table->string('passport_number');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['valid', 'expired', 'cancelled', 'pending', 'unknown'])->default('pending');
            $table->string('entry_point')->nullable();
            $table->date('last_entry_date')->nullable();
            $table->json('additional_data')->nullable();
            $table->timestamp('last_verified_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['expiry_date']);
        });

        // Criminal record check
        Schema::create('criminal_record_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('reference_number')->unique();
            $table->enum('status', ['pending', 'clear', 'not_clear', 'error', 'not_applicable'])->default('pending');
            $table->text('result_message')->nullable();
            $table->text('error_message')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });

        // Sponsorship status
        Schema::create('sponsorships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('sponsor_type'); // employer, family, government, other
            $table->string('sponsor_name');
            $table->string('sponsor_id')->nullable(); // Trade license, ID, etc.
            $table->enum('status', ['active', 'inactive', 'suspended', 'cancelled', 'unknown'])->default('active');
            $table->json('sponsor_data')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });

        // Government compliance records
        Schema::create('compliance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('check_type'); // criminal_record, work_permit, visa, sponsorship, eid
            $table->enum('status', ['compliant', 'non_compliant', 'pending', 'error'])->default('pending');
            $table->text('details')->nullable();
            $table->text('recommendations')->nullable();
            $table->string('authority'); // MOI, MOHRE, General Directorate, etc.
            $table->timestamp('checked_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'check_type', 'status']);
        });

        // API integration logs
        Schema::create('government_api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service'); // emirates_id, moi, mohre, visa, etc.
            $table->string('endpoint');
            $table->string('method'); // GET, POST
            $table->text('request_payload')->nullable();
            $table->text('response_payload')->nullable();
            $table->integer('response_code')->nullable();
            $table->enum('status', ['success', 'failed', 'timeout', 'error'])->default('error');
            $table->text('error_message')->nullable();
            $table->decimal('response_time_ms', 8, 2)->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
            
            $table->index(['service', 'created_at']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('government_api_logs');
        Schema::dropIfExists('compliance_records');
        Schema::dropIfExists('sponsorships');
        Schema::dropIfExists('criminal_record_checks');
        Schema::dropIfExists('visa_statuses');
        Schema::dropIfExists('work_permit_verifications');
        Schema::dropIfExists('moi_verifications');
        Schema::dropIfExists('emirates_id_verifications');
    }
};

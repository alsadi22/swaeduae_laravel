<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Payments table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('payment_method'); // stripe, credit_card, debit_card, bank_transfer
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('AED');
            $table->string('transaction_id')->unique()->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'refunded', 'cancelled'])->default('pending');
            $table->string('payment_type'); // donation, event_fee, course_fee, registration
            $table->unsignedBigInteger('reference_id')->nullable(); // Event ID, Course ID, etc.
            $table->string('reference_type')->nullable(); // Event, Course, Application
            $table->json('metadata')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('receipt_number')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['transaction_id']);
            $table->index(['created_at']);
        });

        // Payment methods storage
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_payment_method_id')->unique();
            $table->string('type'); // card, bank_account, wallet
            $table->string('card_brand')->nullable(); // visa, mastercard, etc.
            $table->string('card_last_four')->nullable();
            $table->string('card_expiry')->nullable(); // MM/YY format
            $table->string('card_holder_name')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('added_at');
            $table->timestamps();
            
            $table->index(['user_id', 'is_default']);
        });

        // Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency')->default('AED');
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->date('paid_date')->nullable();
            $table->text('description')->nullable();
            $table->json('line_items')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['invoice_number']);
        });

        // Transactions log
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('transaction_type'); // payment, refund, fee, credit
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('AED');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending');
            $table->string('reference_type')->nullable(); // Payment, Refund, Event, etc.
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'created_at']);
            $table->index(['transaction_type', 'status']);
        });

        // Refunds
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('AED');
            $table->string('stripe_refund_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->text('reason');
            $table->json('metadata')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['payment_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });

        // Subscriptions
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('stripe_subscription_id')->unique();
            $table->string('stripe_price_id');
            $table->string('plan_name'); // monthly, yearly
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('AED');
            $table->enum('status', ['active', 'paused', 'cancelled', 'expired'])->default('active');
            $table->integer('billing_cycle_anchor')->nullable(); // Day of month
            $table->date('current_period_start')->nullable();
            $table->date('current_period_end')->nullable();
            $table->date('cancelled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });

        // Wallet/Balance
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->decimal('balance', 10, 2)->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->decimal('total_received', 10, 2)->default(0);
            $table->string('currency')->default('AED');
            $table->timestamps();
        });

        // Wallet transactions
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('type'); // credit, debit
            $table->string('reason'); // payment, refund, earning, etc.
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['wallet_id', 'created_at']);
        });

        // Billing information
        Schema::create('billing_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->unique();
            $table->string('billing_name');
            $table->string('email');
            $table->string('phone');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('state')->nullable();
            $table->string('postal_code');
            $table->string('country');
            $table->string('tax_id')->nullable();
            $table->json('additional_info')->nullable();
            $table->timestamps();
        });

        // Financial reports
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_type'); // monthly, quarterly, annual
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('total_revenue', 10, 2);
            $table->decimal('total_expenses', 10, 2);
            $table->decimal('total_refunds', 10, 2);
            $table->decimal('net_income', 10, 2);
            $table->integer('transaction_count');
            $table->integer('successful_payments');
            $table->integer('failed_payments');
            $table->json('breakdown_by_type')->nullable();
            $table->json('breakdown_by_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['report_type', 'period_start']);
        });

        // Payment gateway configuration
        Schema::create('payment_gateway_configs', function (Blueprint $table) {
            $table->id();
            $table->string('gateway'); // stripe, paypal, etc.
            $table->string('mode'); // live, test
            $table->string('api_key')->encrypted();
            $table->string('secret_key')->encrypted();
            $table->string('webhook_secret')->encrypted()->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
            
            $table->unique(['gateway', 'mode']);
        });

        // Payment webhooks log
        Schema::create('payment_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('webhook_id');
            $table->string('event_type');
            $table->string('resource_type');
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->json('payload');
            $table->enum('status', ['received', 'processed', 'failed', 'ignored'])->default('received');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            $table->index(['webhook_id']);
            $table->index(['event_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_logs');
        Schema::dropIfExists('payment_gateway_configs');
        Schema::dropIfExists('financial_reports');
        Schema::dropIfExists('billing_information');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('payments');
    }
};

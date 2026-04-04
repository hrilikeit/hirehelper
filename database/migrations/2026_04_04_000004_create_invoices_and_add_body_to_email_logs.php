<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_offer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('invoice_number')->unique();
            $table->string('type'); // weekly, bonus
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->decimal('hours', 8, 2)->nullable();
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->string('status')->default('paid'); // paid, pending, refunded
            $table->string('payment_method')->nullable(); // paypal, card
            $table->string('paypal_transaction_id')->nullable();
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('client_project_id');
        });

        Schema::table('email_logs', function (Blueprint $table) {
            $table->longText('body')->nullable()->after('to_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');

        Schema::table('email_logs', function (Blueprint $table) {
            $table->dropColumn('body');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('weekly_amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->string('status', 30)->default('pending')->index();
            $table->string('paypal_product_id', 64)->nullable();
            $table->string('paypal_plan_id', 64)->nullable()->index();
            $table->string('paypal_subscription_id', 64)->nullable()->index();
            $table->string('paypal_subscription_status', 40)->nullable();
            $table->string('paypal_payer_email')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('paypal_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_subscriptions');
    }
};

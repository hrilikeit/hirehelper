<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('monthly_price', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->unsignedInteger('active_users')->default(0);
            $table->decimal('star_rating', 3, 2)->default(5.00);
            $table->string('status')->default('active'); // active, inactive
            $table->timestamps();
        });

        Schema::create('service_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('pending'); // pending, active, paused, cancelled
            $table->string('paypal_order_id')->nullable();
            $table->string('paypal_capture_id')->nullable();
            $table->string('paypal_subscription_id')->nullable();
            $table->string('paypal_subscription_status')->nullable();
            $table->string('paypal_payer_email')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('next_billing_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->json('paypal_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_subscriptions');
        Schema::dropIfExists('services');
    }
};

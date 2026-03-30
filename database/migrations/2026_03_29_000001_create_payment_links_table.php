<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_links', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('freelancer_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->text('description');
            $table->string('slug', 32)->unique();
            $table->string('status', 20)->default('open')->index();
            $table->string('paypal_order_id', 64)->nullable()->index();
            $table->string('paypal_order_status', 40)->nullable();
            $table->string('paypal_capture_id', 64)->nullable();
            $table->string('paypal_capture_status', 40)->nullable();
            $table->string('paypal_payer_email')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_links');
    }
};

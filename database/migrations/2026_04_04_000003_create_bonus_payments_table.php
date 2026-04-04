<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('bonus_payments')) {
            Schema::create('bonus_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('project_offer_id')->constrained()->cascadeOnDelete();
                $table->decimal('amount', 10, 2);
                $table->text('note')->nullable();
                $table->string('status', 20)->default('pending'); // pending, approved, captured, failed, cancelled
                $table->string('paypal_order_id')->nullable();
                $table->string('paypal_capture_id')->nullable();
                $table->json('paypal_payload')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'project_offer_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bonus_payments');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Primary PayPal account');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_live')->default(false);
            $table->text('api_username')->nullable();
            $table->text('api_password')->nullable();
            $table->text('client_id');
            $table->text('client_secret');
            $table->text('webhook_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_settings');
    }
};

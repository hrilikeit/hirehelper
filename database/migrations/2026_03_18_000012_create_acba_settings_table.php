<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acba_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Primary ACBA gateway');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_live')->default(false);
            $table->string('test_base_url')->default('https://ipaytest.arca.am:8445/payment/rest');
            $table->text('test_username')->nullable();
            $table->text('test_password')->nullable();
            $table->string('live_base_url')->default('https://ipay.arca.am/payment/rest');
            $table->text('live_username')->nullable();
            $table->text('live_password')->nullable();
            $table->decimal('verification_amount', 10, 2)->default(0.01);
            $table->string('verification_currency', 3)->default('USD');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acba_settings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('paypal_settings', function (Blueprint $table): void {
            $table->string('payment_mode', 20)->default('recurring')->after('webhook_id');
        });
    }

    public function down(): void
    {
        Schema::table('paypal_settings', function (Blueprint $table): void {
            $table->dropColumn('payment_mode');
        });
    }
};

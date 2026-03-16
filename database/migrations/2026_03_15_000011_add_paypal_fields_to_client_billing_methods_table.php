<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_billing_methods', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('is_default');
            $table->string('provider_customer_id')->nullable()->after('provider');
            $table->string('provider_payer_id')->nullable()->after('provider_customer_id');
            $table->string('provider_email')->nullable()->after('provider_payer_id');
            $table->string('provider_setup_token_id')->nullable()->after('provider_email');
            $table->string('provider_payment_token_id')->nullable()->after('provider_setup_token_id');
            $table->json('provider_payload')->nullable()->after('provider_payment_token_id');
            $table->timestamp('verified_at')->nullable()->after('provider_payload');
        });
    }

    public function down(): void
    {
        Schema::table('client_billing_methods', function (Blueprint $table) {
            $table->dropColumn([
                'provider',
                'provider_customer_id',
                'provider_payer_id',
                'provider_email',
                'provider_setup_token_id',
                'provider_payment_token_id',
                'provider_payload',
                'verified_at',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add country and last_login_at to users table
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'country')) {
                $table->string('country', 100)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('remember_token');
            }
            if (! Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }
        });

        // Create email_logs table
        if (! Schema::hasTable('email_logs')) {
            Schema::create('email_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('client_project_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('project_offer_id')->nullable()->constrained()->nullOnDelete();
                $table->string('email_type', 80);
                $table->string('subject');
                $table->string('to_email');
                $table->string('status', 20)->default('sent');
                $table->timestamp('opened_at')->nullable();
                $table->string('message_id')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'created_at']);
                $table->index('client_project_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');

        Schema::table('users', function (Blueprint $table) {
            foreach (['country', 'last_login_at', 'last_login_ip'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

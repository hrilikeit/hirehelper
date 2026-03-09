<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role', 32)->default('client')->after('password');
            }

            if (! Schema::hasColumn('users', 'company')) {
                $table->string('company')->nullable()->after('email');
            }

            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone', 50)->nullable()->after('company');
            }

            if (! Schema::hasColumn('users', 'notify_messages')) {
                $table->boolean('notify_messages')->default(true)->after('remember_token');
            }

            if (! Schema::hasColumn('users', 'notify_reports')) {
                $table->boolean('notify_reports')->default(true)->after('notify_messages');
            }

            if (! Schema::hasColumn('users', 'reminder_frequency')) {
                $table->string('reminder_frequency', 32)->default('weekly')->after('notify_reports');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['reminder_frequency', 'notify_reports', 'notify_messages', 'phone', 'company', 'role'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

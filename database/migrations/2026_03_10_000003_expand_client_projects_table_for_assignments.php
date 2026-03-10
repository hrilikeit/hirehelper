<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_projects', function (Blueprint $table) {
            if (! Schema::hasColumn('client_projects', 'sales_manager_id')) {
                $table->foreignId('sales_manager_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('client_projects', 'project_manager_id')) {
                $table->foreignId('project_manager_id')->nullable()->after('sales_manager_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('client_projects', 'external_reference')) {
                $table->string('external_reference')->nullable()->after('specialty');
            }

            if (! Schema::hasColumn('client_projects', 'acceptance_notes')) {
                $table->text('acceptance_notes')->nullable()->after('status');
            }

            if (! Schema::hasColumn('client_projects', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('acceptance_notes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('client_projects', function (Blueprint $table) {
            foreach (['accepted_at', 'acceptance_notes', 'external_reference', 'project_manager_id', 'sales_manager_id'] as $column) {
                if (Schema::hasColumn('client_projects', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

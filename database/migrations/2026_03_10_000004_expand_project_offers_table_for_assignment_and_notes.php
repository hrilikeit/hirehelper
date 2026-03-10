<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_offers', function (Blueprint $table) {
            if (! Schema::hasColumn('project_offers', 'sales_manager_id')) {
                $table->foreignId('sales_manager_id')->nullable()->after('freelancer_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('project_offers', 'project_manager_id')) {
                $table->foreignId('project_manager_id')->nullable()->after('sales_manager_id')->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('project_offers', 'payment_status')) {
                $table->string('payment_status', 50)->default('non_active')->after('status');
            }

            if (! Schema::hasColumn('project_offers', 'external_reference')) {
                $table->string('external_reference')->nullable()->after('billing_method');
            }

            if (! Schema::hasColumn('project_offers', 'notes')) {
                $table->text('notes')->nullable()->after('external_reference');
            }

            if (! Schema::hasColumn('project_offers', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_offers', function (Blueprint $table) {
            foreach (['accepted_at', 'notes', 'external_reference', 'payment_status', 'project_manager_id', 'sales_manager_id'] as $column) {
                if (Schema::hasColumn('project_offers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

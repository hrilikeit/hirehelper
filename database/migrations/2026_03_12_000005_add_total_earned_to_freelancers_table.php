<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            if (! Schema::hasColumn('freelancers', 'total_earned')) {
                $table->decimal('total_earned', 12, 2)->default(0)->after('hourly_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            if (Schema::hasColumn('freelancers', 'total_earned')) {
                $table->dropColumn('total_earned');
            }
        });
    }
};

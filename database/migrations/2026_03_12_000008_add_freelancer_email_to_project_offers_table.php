<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_offers', function (Blueprint $table) {
            if (! Schema::hasColumn('project_offers', 'freelancer_email')) {
                $table->string('freelancer_email')->nullable()->after('freelancer_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_offers', function (Blueprint $table) {
            if (Schema::hasColumn('project_offers', 'freelancer_email')) {
                $table->dropColumn('freelancer_email');
            }
        });
    }
};

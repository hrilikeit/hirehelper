<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            if (! Schema::hasColumn('freelancers', 'contact_email')) {
                $table->string('contact_email')->nullable()->after('name');
            }

            if (! Schema::hasColumn('freelancers', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            if (Schema::hasColumn('freelancers', 'contact_email')) {
                $table->dropColumn('contact_email');
            }

            if (Schema::hasColumn('freelancers', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};

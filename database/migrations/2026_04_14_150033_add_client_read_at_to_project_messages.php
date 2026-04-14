<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('project_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('project_messages', 'client_read_at')) {
                $table->timestamp('client_read_at')->nullable()->after('sent_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('project_messages', function (Blueprint $table) {
            if (Schema::hasColumn('project_messages', 'client_read_at')) {
                $table->dropColumn('client_read_at');
            }
        });
    }
};

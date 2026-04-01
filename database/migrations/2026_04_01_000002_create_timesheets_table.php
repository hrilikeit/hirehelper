<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_offer_id')->constrained('project_offers')->cascadeOnDelete();
            $table->date('week_start');
            $table->decimal('sun', 5, 2)->default(0);
            $table->decimal('mon', 5, 2)->default(0);
            $table->decimal('tue', 5, 2)->default(0);
            $table->decimal('wed', 5, 2)->default(0);
            $table->decimal('thu', 5, 2)->default(0);
            $table->decimal('fri', 5, 2)->default(0);
            $table->decimal('sat', 5, 2)->default(0);
            $table->decimal('total_hours', 6, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('status', 20)->default('pending'); // pending, paid, discarded
            $table->timestamps();

            $table->unique(['project_offer_id', 'week_start']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timesheets');
    }
};

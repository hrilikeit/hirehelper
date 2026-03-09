<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('description');
            $table->string('experience_level', 50)->default('Intermediate');
            $table->string('timeframe', 100)->default('Less than 1 month');
            $table->string('specialty', 100)->default('Full stack development');
            $table->string('status', 32)->default('draft');
            $table->timestamp('last_saved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_projects');
    }
};

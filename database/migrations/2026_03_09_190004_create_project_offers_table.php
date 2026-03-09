<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_project_id')->constrained('client_projects')->cascadeOnDelete();
            $table->foreignId('freelancer_id')->constrained()->restrictOnDelete();
            $table->string('role');
            $table->decimal('hourly_rate', 10, 2)->default(0);
            $table->unsignedInteger('weekly_limit')->default(20);
            $table->boolean('manual_time')->default(true);
            $table->boolean('multi_offer')->default(false);
            $table->string('status', 32)->default('pending');
            $table->string('billing_method')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_offers');
    }
};

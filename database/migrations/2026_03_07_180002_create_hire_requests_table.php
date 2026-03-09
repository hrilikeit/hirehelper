<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hire_requests', function (Blueprint $table) {
            $table->id();
            $table->string('category')->index();
            $table->string('project_title');
            $table->longText('needs');
            $table->string('outcome');
            $table->string('timeline');
            $table->string('budget');
            $table->string('team');
            $table->text('context')->nullable();
            $table->string('name');
            $table->string('email')->index();
            $table->string('company')->nullable();
            $table->string('website')->nullable();
            $table->string('source')->nullable();
            $table->string('status')->default('new')->index();
            $table->text('admin_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hire_requests');
    }
};

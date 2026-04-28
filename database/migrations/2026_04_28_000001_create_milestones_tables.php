<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('milestone_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // client (assigned after registration)
            $table->foreignId('freelancer_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('token', 64)->unique(); // unique link token
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status')->default('draft'); // draft, active, completed, cancelled
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('milestone_project_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('status')->default('pending'); // pending, funded, released
            $table->string('paypal_order_id')->nullable();
            $table->string('paypal_capture_id')->nullable();
            $table->timestamp('funded_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('milestones');
        Schema::dropIfExists('milestone_projects');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('labels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('color', 7)->default('#6b7280'); // hex color
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('client_project_label', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('label_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['client_project_id', 'label_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_project_label');
        Schema::dropIfExists('labels');
    }
};

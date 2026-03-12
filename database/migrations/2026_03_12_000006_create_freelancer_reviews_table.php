<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('freelancer_reviews')) {
            return;
        }

        Schema::create('freelancer_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('freelancer_id')->constrained('freelancers')->cascadeOnDelete();
            $table->string('review_title');
            $table->date('date_from');
            $table->date('date_to');
            $table->unsignedTinyInteger('stars');
            $table->unsignedInteger('hours');
            $table->decimal('rate', 10, 2)->default(0);
            $table->text('review_text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freelancer_reviews');
    }
};

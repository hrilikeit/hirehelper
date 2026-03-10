<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            if (! Schema::hasColumn('freelancers', 'headline')) {
                $table->string('headline')->nullable()->after('title');
            }

            if (! Schema::hasColumn('freelancers', 'specialization')) {
                $table->string('specialization')->nullable()->after('headline');
            }

            if (! Schema::hasColumn('freelancers', 'country')) {
                $table->string('country', 100)->nullable()->after('location');
            }

            if (! Schema::hasColumn('freelancers', 'city')) {
                $table->string('city', 100)->nullable()->after('country');
            }

            if (! Schema::hasColumn('freelancers', 'english_level')) {
                $table->string('english_level', 50)->nullable()->after('city');
            }

            if (! Schema::hasColumn('freelancers', 'timezone')) {
                $table->string('timezone', 100)->nullable()->after('english_level');
            }

            if (! Schema::hasColumn('freelancers', 'availability')) {
                $table->string('availability', 100)->nullable()->after('timezone');
            }

            if (! Schema::hasColumn('freelancers', 'years_experience')) {
                $table->unsignedInteger('years_experience')->nullable()->after('availability');
            }

            if (! Schema::hasColumn('freelancers', 'average_rating')) {
                $table->decimal('average_rating', 3, 2)->default(0)->after('years_experience');
            }

            if (! Schema::hasColumn('freelancers', 'review_count')) {
                $table->unsignedInteger('review_count')->default(0)->after('average_rating');
            }

            if (! Schema::hasColumn('freelancers', 'completed_jobs')) {
                $table->unsignedInteger('completed_jobs')->default(0)->after('review_count');
            }

            if (! Schema::hasColumn('freelancers', 'tools')) {
                $table->json('tools')->nullable()->after('skills');
            }

            if (! Schema::hasColumn('freelancers', 'portfolio_url')) {
                $table->string('portfolio_url')->nullable()->after('avatar');
            }

            if (! Schema::hasColumn('freelancers', 'linkedin_url')) {
                $table->string('linkedin_url')->nullable()->after('portfolio_url');
            }

            if (! Schema::hasColumn('freelancers', 'github_url')) {
                $table->string('github_url')->nullable()->after('linkedin_url');
            }

            if (! Schema::hasColumn('freelancers', 'intro_video_url')) {
                $table->string('intro_video_url')->nullable()->after('github_url');
            }

            if (! Schema::hasColumn('freelancers', 'bio')) {
                $table->text('bio')->nullable()->after('overview');
            }

            if (! Schema::hasColumn('freelancers', 'internal_notes')) {
                $table->text('internal_notes')->nullable()->after('bio');
            }

            if (! Schema::hasColumn('freelancers', 'added_by_user_id')) {
                $table->foreignId('added_by_user_id')->nullable()->after('internal_notes')->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('freelancers', function (Blueprint $table) {
            foreach ([
                'added_by_user_id',
                'internal_notes',
                'bio',
                'intro_video_url',
                'github_url',
                'linkedin_url',
                'portfolio_url',
                'tools',
                'completed_jobs',
                'review_count',
                'average_rating',
                'years_experience',
                'availability',
                'timezone',
                'english_level',
                'city',
                'country',
                'specialization',
                'headline',
            ] as $column) {
                if (Schema::hasColumn('freelancers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};

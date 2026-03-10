<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminBootstrapSeeder extends Seeder
{
    public function run(): void
    {
        if (User::query()->whereIn('role', UserRole::internalValues())->exists()) {
            return;
        }

        $firstUser = User::query()->orderBy('id')->first();

        if (! $firstUser) {
            return;
        }

        $firstUser->forceFill([
            'role' => UserRole::SuperAdmin->value,
            'is_active' => true,
            'job_title' => $firstUser->job_title ?: 'Founder',
        ])->save();
    }
}

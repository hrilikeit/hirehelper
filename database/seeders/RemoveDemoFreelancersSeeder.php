<?php

namespace Database\Seeders;

use App\Models\Freelancer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RemoveDemoFreelancersSeeder extends Seeder
{
    public function run(): void
    {
        $demoNames = [
            'Ava Petrosyan',
            'Jade Collins',
            'Leo Martinez',
            'Nora Kim',
        ];

        $deletedCount = Freelancer::query()
            ->whereIn('slug', array_map(fn (string $name): string => Str::slug($name), $demoNames))
            ->whereDoesntHave('offers')
            ->delete();

        $this->command?->info("Removed {$deletedCount} demo freelancer record(s).");
    }
}

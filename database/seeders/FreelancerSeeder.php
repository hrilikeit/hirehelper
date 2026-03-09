<?php

namespace Database\Seeders;

use App\Models\Freelancer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FreelancerSeeder extends Seeder
{
    public function run(): void
    {
        $freelancers = [
            [
                'name' => 'Ava Petrosyan',
                'title' => 'Full stack developer',
                'hourly_rate' => 35,
                'overview' => 'Builds calm, modern Laravel applications and polished client dashboards.',
                'skills' => ['Laravel', 'PHP', 'MySQL', 'UI implementation'],
                'location' => 'Armenia',
                'avatar' => 'avatar-ava.svg',
                'is_featured' => true,
            ],
            [
                'name' => 'Jade Collins',
                'title' => 'UI/UX designer',
                'hourly_rate' => 32,
                'overview' => 'Turns product briefs into clean flows, intuitive wireframes, and trustworthy interfaces.',
                'skills' => ['UX', 'UI systems', 'Figma', 'Product strategy'],
                'location' => 'United Kingdom',
                'avatar' => 'avatar-jade.svg',
                'is_featured' => true,
            ],
            [
                'name' => 'Leo Martinez',
                'title' => 'Mobile app developer',
                'hourly_rate' => 40,
                'overview' => 'Ships reliable cross-platform apps with strong performance and clear communication.',
                'skills' => ['Flutter', 'API integration', 'Mobile architecture'],
                'location' => 'Spain',
                'avatar' => 'avatar-leo.svg',
                'is_featured' => true,
            ],
            [
                'name' => 'Nora Kim',
                'title' => 'E-commerce engineer',
                'hourly_rate' => 38,
                'overview' => 'Optimizes storefronts, checkout experiences, and conversion-focused builds.',
                'skills' => ['Shopify', 'WooCommerce', 'Analytics', 'Performance'],
                'location' => 'South Korea',
                'avatar' => 'avatar-nora.svg',
                'is_featured' => true,
            ],
        ];

        foreach ($freelancers as $freelancer) {
            Freelancer::updateOrCreate(
                ['slug' => Str::slug($freelancer['name'])],
                array_merge($freelancer, [
                    'slug' => Str::slug($freelancer['name']),
                    'status' => 'active',
                ])
            );
        }
    }
}

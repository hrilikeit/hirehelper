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
                'headline' => 'Laravel engineer for internal tools and calm delivery',
                'specialization' => 'Full Stack Development',
                'hourly_rate' => 35,
                'overview' => 'Builds calm, modern Laravel applications and polished client dashboards.',
                'bio' => 'Ava has spent the last several years delivering Laravel products for service businesses and internal operations teams. She is strong on handoff, communication, and production readiness.',
                'skills' => ['Laravel', 'PHP', 'MySQL', 'UI implementation'],
                'tools' => ['Filament', 'Livewire', 'GitHub'],
                'country' => 'Armenia',
                'city' => 'Yerevan',
                'location' => 'Armenia',
                'english_level' => 'Professional',
                'timezone' => 'UTC+4',
                'availability' => 'Available this week',
                'years_experience' => 7,
                'average_rating' => 4.9,
                'review_count' => 18,
                'completed_jobs' => 26,
                'portfolio_url' => 'https://example.com/ava',
                'linkedin_url' => 'https://linkedin.com/in/ava-petrosyan',
                'avatar' => 'avatar-ava.svg',
                'is_featured' => true,
            ],
            [
                'name' => 'Jade Collins',
                'title' => 'UI/UX designer',
                'headline' => 'Product designer focused on trust, clarity, and conversion',
                'specialization' => 'UI/UX Design',
                'hourly_rate' => 32,
                'overview' => 'Turns product briefs into clean flows, intuitive wireframes, and trustworthy interfaces.',
                'bio' => 'Jade works with B2B and marketplace products, helping teams simplify dense workflows and improve perceived quality.',
                'skills' => ['UX', 'UI systems', 'Figma', 'Product strategy'],
                'tools' => ['Figma', 'Maze', 'Notion'],
                'country' => 'United Kingdom',
                'city' => 'Manchester',
                'location' => 'United Kingdom',
                'english_level' => 'Native',
                'timezone' => 'UTC+0',
                'availability' => 'Available next week',
                'years_experience' => 6,
                'average_rating' => 4.8,
                'review_count' => 24,
                'completed_jobs' => 31,
                'portfolio_url' => 'https://example.com/jade',
                'linkedin_url' => 'https://linkedin.com/in/jade-collins',
                'avatar' => 'avatar-jade.svg',
                'is_featured' => true,
            ],
            [
                'name' => 'Leo Martinez',
                'title' => 'Mobile app developer',
                'headline' => 'Cross-platform engineer for product launches and iterations',
                'specialization' => 'Mobile Development',
                'hourly_rate' => 40,
                'overview' => 'Ships reliable cross-platform apps with strong performance and clear communication.',
                'bio' => 'Leo helps startups and product teams launch Flutter apps with API integrations, release management, and support.',
                'skills' => ['Flutter', 'API integration', 'Mobile architecture'],
                'tools' => ['Flutter', 'Firebase', 'Fastlane'],
                'country' => 'Spain',
                'city' => 'Barcelona',
                'location' => 'Spain',
                'english_level' => 'Professional',
                'timezone' => 'UTC+1',
                'availability' => 'Available this week',
                'years_experience' => 8,
                'average_rating' => 4.9,
                'review_count' => 14,
                'completed_jobs' => 22,
                'portfolio_url' => 'https://example.com/leo',
                'linkedin_url' => 'https://linkedin.com/in/leo-martinez',
                'avatar' => 'avatar-leo.svg',
                'is_featured' => true,
            ],
            [
                'name' => 'Nora Kim',
                'title' => 'E-commerce engineer',
                'headline' => 'Storefront and conversion specialist for growth-focused teams',
                'specialization' => 'E-commerce Development',
                'hourly_rate' => 38,
                'overview' => 'Optimizes storefronts, checkout experiences, and conversion-focused builds.',
                'bio' => 'Nora improves commerce performance across storefront UX, operations, and analytics with a strong bias for practical wins.',
                'skills' => ['Shopify', 'WooCommerce', 'Analytics', 'Performance'],
                'tools' => ['Shopify', 'GA4', 'Hotjar'],
                'country' => 'South Korea',
                'city' => 'Seoul',
                'location' => 'South Korea',
                'english_level' => 'Professional',
                'timezone' => 'UTC+9',
                'availability' => 'Available in 2 weeks',
                'years_experience' => 9,
                'average_rating' => 4.7,
                'review_count' => 16,
                'completed_jobs' => 29,
                'portfolio_url' => 'https://example.com/nora',
                'linkedin_url' => 'https://linkedin.com/in/nora-kim',
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
                ]),
            );
        }
    }
}

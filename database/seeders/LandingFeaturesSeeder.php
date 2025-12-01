<?php

namespace Database\Seeders;

use App\Models\LandingFeature;
use Illuminate\Database\Seeder;

class LandingFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        $features = [
            [
                'title' => 'Student Management',
                'description' => 'Comprehensive student profiles, enrollment tracking, and academic records management.',
                'icon' => 'bi-people-fill',
                'icon_color' => 'text-primary',
                'icon_bg_color' => 'rgba(79, 70, 229, 0.1)',
                'sort_order' => 1,
            ],
            [
                'title' => 'Attendance & Timetable',
                'description' => 'Digital attendance tracking and smart timetable generation with conflict detection.',
                'icon' => 'bi-calendar-check',
                'icon_color' => 'var(--secondary-color)',
                'icon_bg_color' => 'rgba(6, 182, 212, 0.1)',
                'sort_order' => 2,
            ],
            [
                'title' => 'Academic Management',
                'description' => 'Grade books, report cards, and comprehensive academic performance analytics.',
                'icon' => 'bi-journal-text',
                'icon_color' => 'var(--accent-color)',
                'icon_bg_color' => 'rgba(245, 158, 11, 0.15)',
                'sort_order' => 3,
            ],
            [
                'title' => 'Fee Management',
                'description' => 'Automated fee collection, receipts, and financial reporting made simple.',
                'icon' => 'bi-cash-stack',
                'icon_color' => 'text-primary',
                'icon_bg_color' => 'rgba(79, 70, 229, 0.1)',
                'sort_order' => 4,
            ],
            [
                'title' => 'Communication Hub',
                'description' => 'SMS, email, and in-app messaging to keep everyone connected.',
                'icon' => 'bi-chat-dots',
                'icon_color' => 'var(--secondary-color)',
                'icon_bg_color' => 'rgba(6, 182, 212, 0.1)',
                'sort_order' => 5,
            ],
            [
                'title' => 'Analytics & Reports',
                'description' => 'Insightful dashboards and customizable reports for data-driven decisions.',
                'icon' => 'bi-graph-up',
                'icon_color' => 'var(--accent-color)',
                'icon_bg_color' => 'rgba(245, 158, 11, 0.15)',
                'sort_order' => 6,
            ],
        ];

        foreach ($features as $feature) {
            LandingFeature::create($feature);
        }
    }
}

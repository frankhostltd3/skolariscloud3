<?php

namespace Database\Seeders;

use App\Models\LandingFaq;
use App\Models\LandingSection;
use App\Models\LandingStat;
use App\Models\LandingTestimonial;
use Illuminate\Database\Seeder;

class LandingCmsSeeder extends Seeder
{
    public function run()
    {
        // 1. Seed Stats
        $stats = [
            ['value' => '500+', 'label' => 'Schools Trust Us', 'sort_order' => 1],
            ['value' => '50K+', 'label' => 'Active Students', 'sort_order' => 2],
            ['value' => '99.9%', 'label' => 'Uptime Guaranteed', 'sort_order' => 3],
            ['value' => '4.9/5', 'label' => 'Customer Rating', 'sort_order' => 4],
        ];

        foreach ($stats as $stat) {
            LandingStat::create($stat);
        }

        // 2. Seed Testimonials
        $testimonials = [
            [
                'name' => 'Jane Doe',
                'role' => 'Principal, Greenwood High',
                'content' => '"SMATCAMPUS has revolutionized how we manage our school. The interface is intuitive and the support team is exceptional."',
                'rating' => 5,
                'sort_order' => 1,
            ],
            [
                'name' => 'Michael Smith',
                'role' => 'Administrator, Valley School',
                'content' => '"The automation features saved us countless hours. Parent communication has never been easier!"',
                'rating' => 5,
                'sort_order' => 2,
            ],
            [
                'name' => 'Sarah Johnson',
                'role' => 'Director, Riverside Academy',
                'content' => '"Outstanding platform! The analytics help us make informed decisions about our academic programs."',
                'rating' => 5,
                'sort_order' => 3,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            LandingTestimonial::create($testimonial);
        }

        // 3. Seed FAQs
        $faqs = [
            [
                'question' => 'How quickly can we get started?',
                'answer' => 'You can start using SMATCAMPUS immediately after signup. Our team will help you import your existing data and train your staff within days.',
                'sort_order' => 1,
            ],
            [
                'question' => 'Is my data secure?',
                'answer' => 'Absolutely! We use bank-level encryption, regular backups, and comply with international data protection standards including GDPR.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Can I customize the system for my school?',
                'answer' => 'Yes! Our system is highly customizable. You can configure workflows, reports, and even request custom features for Enterprise plans.',
                'sort_order' => 3,
            ],
            [
                'question' => 'What kind of support do you offer?',
                'answer' => 'We provide email support for all plans, priority support for Professional, and 24/7 dedicated support for Enterprise customers.',
                'sort_order' => 4,
            ],
            [
                'question' => 'Is there a free trial?',
                'answer' => 'Yes! We offer a 14-day free trial with full access to all features. No credit card required to start.',
                'sort_order' => 5,
            ],
        ];

        foreach ($faqs as $faq) {
            LandingFaq::create($faq);
        }

        // 4. Seed Sections
        $sections = [
            ['name' => 'Hero Section', 'component' => 'landing.hero', 'sort_order' => 1],
            ['name' => 'Stats Section', 'component' => 'landing.stats', 'sort_order' => 2],
            ['name' => 'Features Section', 'component' => 'landing.features', 'sort_order' => 3],
            ['name' => 'Pricing Section', 'component' => 'landing.pricing', 'sort_order' => 4],
            ['name' => 'Testimonials Section', 'component' => 'landing.testimonials', 'sort_order' => 5],
            ['name' => 'FAQ Section', 'component' => 'landing.faq', 'sort_order' => 6],
            ['name' => 'CTA Section', 'component' => 'landing.cta', 'sort_order' => 7],
        ];

        foreach ($sections as $section) {
            LandingSection::create($section);
        }
    }
}

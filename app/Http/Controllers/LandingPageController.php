<?php

namespace App\Http\Controllers;

use App\Models\BillingPlan;
use Illuminate\Contracts\View\View;

class LandingPageController extends Controller
{
    public function __invoke(): View
    {
        $features = [
            [
                'icon' => 'bi-mortarboard',
                'title' => __('All-in-one school office'),
                'description' => __('Admissions, attendance, grading, transport, and extracurriculars run from a single dashboard for your entire network.'),
            ],
            [
                'icon' => 'bi-cash-stack',
                'title' => __('Faster fee collection'),
                'description' => __('Automated reminders and online payment options help bursars close fee gaps and see cash flow in real time.'),
            ],
            [
                'icon' => 'bi-chat-heart',
                'title' => __('Delighted families'),
                'description' => __('Parent portals and mobile updates keep guardians informed in their preferred language with zero extra admin work.'),
            ],
            [
                'icon' => 'bi-graph-up-arrow',
                'title' => __('Growth-ready analytics'),
                'description' => __('School leaders track performance, retention, and compliance across campuses with board-ready reports.'),
            ],
        ];

        $testimonials = [
            [
                'quote' => __('"Families finally get updates in real time and our admin queue is half what it used to be."'),
                'name' => __('Grace Mwangi'),
                'role' => __('Headteacher'),
                'school' => __('Imani International School, Nairobi'),
                'rating' => 5,
                'brand_color' => 'primary',
            ],
            [
                'quote' => __('"Fee reminders and mobile money links helped us collect 92% of invoices before midterm for the first time."'),
                'name' => __('Kwame Adusei'),
                'role' => __('Finance Director'),
                'school' => __('Unity Academy, Accra'),
                'rating' => 4,
                'brand_color' => 'success',
            ],
            [
                'quote' => __('"Rolling out a new campus took days instead of months—teachers were live with schedules in one weekend."'),
                'name' => __('Thandiwe Ndlovu'),
                'role' => __('School Group COO'),
                'school' => __('FuturePath Schools, Johannesburg'),
                'rating' => 5,
                'brand_color' => 'info',
            ],
        ];

        $plans = BillingPlan::query()->active()->ordered()->get();

        return view('welcome', compact('features', 'testimonials', 'plans'));
    }
}

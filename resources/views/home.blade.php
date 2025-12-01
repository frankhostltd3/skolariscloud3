@extends('layouts.app')

@section('content')
    @if (isset($sections) && $sections->count() > 0)
        @foreach ($sections as $section)
            @if ($section->component == 'landing.hero' || $section->component == 'hero')
                <!-- Hero Section -->
                @if (isset($slides) && $slides->count() > 0)
                    <section class="p-0 hero-section position-relative" style="min-height: 80vh; background-color: #000;">
                        <div id="heroCarousel" class="carousel slide carousel-fade h-100" data-bs-ride="carousel"
                            data-bs-interval="5000">
                            <div class="carousel-inner h-100" style="min-height: 80vh;">
                                @foreach ($slides as $index => $slide)
                                    <div class="carousel-item h-100 {{ $index === 0 ? 'active' : '' }}">
                                        <div class="d-flex align-items-center justify-content-center h-100 position-relative"
                                            style="min-height: 80vh;">
                                            <!-- Background Image -->
                                            <div class="position-absolute top-0 start-0 w-100 h-100"
                                                style="background-image: url('{{ asset('storage/' . $slide->image_path) }}'); 
                                                background-size: cover; 
                                                background-position: center;">
                                                <div class="position-absolute top-0 start-0 w-100 h-100"
                                                    style="background: rgba(0,0,0,0.4);"></div>
                                            </div>

                                            <!-- Content -->
                                            <div class="container position-relative z-index-1 text-center text-white px-4">
                                                @if ($slide->title)
                                                    <h1 class="display-3 fw-bold mb-4">{{ $slide->title }}</h1>
                                                @endif
                                                @if ($slide->subtitle)
                                                    <p class="lead mb-5 fs-3">{{ $slide->subtitle }}</p>
                                                @endif
                                                @if ($slide->cta_text && $slide->cta_link)
                                                    <a href="{{ $slide->cta_link }}"
                                                        class="btn btn-primary btn-lg px-5 py-3 fw-bold">{{ $slide->cta_text }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @if ($slides->count() > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel"
                                    data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Previous</span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel"
                                    data-bs-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="visually-hidden">Next</span>
                                </button>
                            @endif
                        </div>
                    </section>
                @else
                    <section class="py-5 hero-section"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-left: 0; margin-right: 0; width: 100%; min-height: 80vh;">
                        <div class="container-fluid px-5">
                            <div class="row align-items-center h-100">
                                <div class="col-lg-6 mb-4 mb-lg-0 px-4 px-lg-5">
                                    <h1 class="display-5 fw-semibold mb-4">Transform Your School Management</h1>
                                    <p class="lead mb-4">Streamline operations, enhance learning, and empower your
                                        institution with our
                                        comprehensive school management system.</p>
                                    <div class="d-flex gap-3">
                                        <a href="#contact" class="btn btn-primary btn-lg">Contact Sales</a>
                                        <a href="#features" class="btn btn-outline-light btn-lg">Learn More</a>
                                    </div>
                                </div>
                                <div class="col-lg-6 px-4 px-lg-5">
                                    <div class="dashboard-mockup bg-white rounded shadow-lg p-4">
                                        <!-- Dashboard Header -->
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h6 class="text-dark mb-0 fw-bold">School Dashboard</h6>
                                            <span class="badge bg-success">Live</span>
                                        </div>

                                        <!-- Stats Cards -->
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <div class="card border-0"
                                                    style="background: linear-gradient(45deg, #4f46e5, #06b6d4);">
                                                    <div class="card-body p-3 text-white">
                                                        <small>Students</small>
                                                        <h5 class="mb-0">1,247</h5>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card border-0"
                                                    style="background: linear-gradient(45deg, #059669, #10b981);">
                                                    <div class="card-body p-3 text-white">
                                                        <small>Teachers</small>
                                                        <h5 class="mb-0">89</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Chart Placeholder -->
                                        <div class="bg-light p-3 mb-3" style="border-radius: 6px;">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <small class="text-muted">Attendance Rate</small>
                                                <small class="text-success fw-bold">96.8%</small>
                                            </div>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-gradient"
                                                    style="width: 96.8%; background: linear-gradient(90deg, #4f46e5, #06b6d4);">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Recent Activity -->
                                        <div>
                                            <small class="text-muted">Recent Activity</small>
                                            <div class="mt-2">
                                                <div class="d-flex align-items-center mb-1">
                                                    <div class="bg-primary rounded-circle me-2"
                                                        style="width: 8px; height: 8px;"></div>
                                                    <small class="text-dark">New student registration</small>
                                                </div>
                                                <div class="d-flex align-items-center mb-1">
                                                    <div class="bg-success rounded-circle me-2"
                                                        style="width: 8px; height: 8px;"></div>
                                                    <small class="text-dark">Grade reports submitted</small>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="bg-warning rounded-circle me-2"
                                                        style="width: 8px; height: 8px;"></div>
                                                    <small class="text-dark">Parent meeting scheduled</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif
            @elseif($section->component == 'landing.stats' || $section->component == 'stats')
                <!-- Stats Section -->
                <section class="py-5 bg-light">
                    <div class="container">
                        <div class="row g-4">
                            @if (isset($stats) && $stats->count() > 0)
                                @foreach ($stats as $stat)
                                    <div class="col-6 col-lg-3">
                                        <div class="card h-100 border-0 shadow-sm text-center stats-card">
                                            <div class="card-body py-4">
                                                <h2 class="display-5 fw-bold text-primary mb-2">{{ $stat->value }}</h2>
                                                <p class="text-muted mb-0">{{ $stat->label }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <!-- Fallback Stats -->
                                <div class="col-6 col-lg-3">
                                    <div class="card h-100 border-0 shadow-sm text-center stats-card">
                                        <div class="card-body py-4">
                                            <h2 class="display-5 fw-bold text-primary mb-2">500+</h2>
                                            <p class="text-muted mb-0">Schools Trust Us</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="card h-100 border-0 shadow-sm text-center stats-card">
                                        <div class="card-body py-4">
                                            <h2 class="display-5 fw-bold text-primary mb-2">50K+</h2>
                                            <p class="text-muted mb-0">Active Students</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="card h-100 border-0 shadow-sm text-center stats-card">
                                        <div class="card-body py-4">
                                            <h2 class="display-5 fw-bold text-primary mb-2">99.9%</h2>
                                            <p class="text-muted mb-0">Uptime Guaranteed</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6 col-lg-3">
                                    <div class="card h-100 border-0 shadow-sm text-center stats-card">
                                        <div class="card-body py-4">
                                            <h2 class="display-5 fw-bold text-primary mb-2">4.9/5</h2>
                                            <p class="text-muted mb-0">Customer Rating</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            @elseif($section->component == 'landing.features' || $section->component == 'features')
                <!-- Features Section -->
                <section id="features" class="py-5">
                    <div class="container">
                        <div class="text-center mb-5">
                            <h2 class="display-6 fw-medium mb-3">Everything You Need to Manage Your School</h2>
                            <p class="lead text-muted">Powerful features designed for modern educational institutions</p>
                        </div>
                        <div class="row g-4">
                            @if (isset($features) && $features->count() > 0)
                                @foreach ($features as $feature)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body p-4">
                                                <div class="icon-circle mb-3 d-flex align-items-center justify-content-center"
                                                    style="width: 56px; height: 56px; background: {{ $feature->icon_bg_color }};">
                                                    <i class="bi {{ $feature->icon }} fs-3"
                                                        style="color: {{ str_starts_with($feature->icon_color, 'var') ? $feature->icon_color : '' }}"
                                                        class="{{ !str_starts_with($feature->icon_color, 'var') ? $feature->icon_color : '' }}"></i>
                                                </div>
                                                <h3 class="h4 fw-bold mb-3">{{ $feature->title }}</h3>
                                                <p class="text-muted">{{ $feature->description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <!-- Fallback Features -->
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body p-4">
                                            <div class="icon-circle mb-3 d-flex align-items-center justify-content-center"
                                                style="width: 56px; height: 56px; background: rgba(79, 70, 229, 0.1);">
                                                <i class="bi bi-people-fill fs-3 text-primary"></i>
                                            </div>
                                            <h3 class="h4 fw-bold mb-3">Student Management</h3>
                                            <p class="text-muted">Comprehensive student profiles, enrollment tracking, and
                                                academic records management.</p>
                                        </div>
                                    </div>
                                </div>
                                <!-- Add other fallback features here if needed -->
                            @endif
                        </div>
                    </div>
                </section>
            @elseif($section->component == 'landing.pricing' || $section->component == 'pricing')
                <!-- Pricing Section -->
                <section id="pricing" class="py-5 bg-light">
                    <div class="container">
                        <div class="text-center mb-5">
                            <h2 class="display-6 fw-medium mb-3">Choose Your Plan</h2>
                            <p class="lead text-muted">Flexible pricing for schools of all sizes</p>
                        </div>
                        <div class="row g-4">
                            @foreach ($plans as $plan)
                                <div class="col-lg-3">
                                    <div class="card h-100 border-0 shadow"
                                        @if ($plan->is_highlighted) style="transform: scale(1.05); border: 3px solid var(--primary-color) !important; z-index: 10;" @endif>
                                        <div class="card-body p-4">
                                            @if ($plan->is_highlighted)
                                                <div class="badge bg-primary mb-3">MOST POPULAR</div>
                                            @endif
                                            <h3 class="h4 fw-bold mb-3">{{ $plan->name }}</h3>
                                            <p class="text-muted mb-4">{{ $plan->tagline }}</p>
                                            <div class="mb-4">
                                                <span class="h2 fw-bold">{{ $plan->display_price }}</span>
                                                @if ($plan->billing_period_label)
                                                    <span class="text-muted">{{ $plan->billing_period_label }}</span>
                                                @endif
                                            </div>
                                            <ul class="list-unstyled mb-4">
                                                @foreach ($plan->features_list as $feature)
                                                    <li class="mb-2"><i
                                                            class="bi bi-check-circle-fill text-success me-2"></i>{{ $feature }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                            @php
                                                $ctaUrl = $plan->price_amount === null ? '#contact' : route('register');
                                            @endphp
                                            <a href="{{ $ctaUrl }}"
                                                class="btn {{ $plan->is_highlighted ? 'btn-primary' : 'btn-outline-primary' }} w-100">{{ $plan->cta_label }}</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @elseif($section->component == 'landing.testimonials' || $section->component == 'testimonials')
                <!-- Testimonials Section -->
                <section id="testimonials" class="py-5">
                    <div class="container">
                        <div class="text-center mb-5">
                            <h2 class="display-6 fw-medium mb-3">What Schools Are Saying</h2>
                            <p class="lead text-muted">Trusted by educational institutions worldwide</p>
                        </div>
                        <div class="row g-4">
                            @if (isset($testimonials) && $testimonials->count() > 0)
                                @foreach ($testimonials as $testimonial)
                                    <div class="col-md-4">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body p-4">
                                                <div class="mb-3">
                                                    @for ($i = 0; $i < $testimonial->rating; $i++)
                                                        <i class="bi bi-star-fill text-warning"></i>
                                                    @endfor
                                                </div>
                                                <p class="mb-4">"{{ $testimonial->content }}"</p>
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                                        style="width: 48px; height: 48px; overflow: hidden;">
                                                        @if ($testimonial->image_path)
                                                            <img src="{{ asset('storage/' . $testimonial->image_path) }}"
                                                                alt="{{ $testimonial->author_name }}"
                                                                class="w-100 h-100 object-fit-cover">
                                                        @else
                                                            <span
                                                                class="fw-bold">{{ substr($testimonial->author_name, 0, 2) }}</span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <strong>{{ $testimonial->author_name }}</strong>
                                                        <p class="text-muted mb-0 small">{{ $testimonial->author_role }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <!-- Fallback Testimonials -->
                                <div class="col-md-4">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body p-4">
                                            <div class="mb-3">
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                                <i class="bi bi-star-fill text-warning"></i>
                                            </div>
                                            <p class="mb-4">"SMATCAMPUS has revolutionized how we manage our school. The
                                                interface is intuitive and the support team is exceptional."</p>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                                    style="width: 48px; height: 48px;">
                                                    <span class="fw-bold">JD</span>
                                                </div>
                                                <div>
                                                    <strong>Jane Doe</strong>
                                                    <p class="text-muted mb-0 small">Principal, Greenwood High</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            @elseif($section->component == 'landing.faq' || $section->component == 'faq')
                <!-- FAQ Section -->
                <section id="faq" class="py-5 bg-light">
                    <div class="container">
                        <div class="text-center mb-5">
                            <h2 class="display-6 fw-medium mb-3">Frequently Asked Questions</h2>
                            <p class="lead text-muted">Got questions? We've got answers</p>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="accordion" id="faqAccordion">
                                    @if (isset($faqs) && $faqs->count() > 0)
                                        @foreach ($faqs as $index => $faq)
                                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                                <h3 class="accordion-header">
                                                    <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}"
                                                        type="button" data-bs-toggle="collapse"
                                                        data-bs-target="#faq{{ $faq->id }}">
                                                        {{ $faq->question }}
                                                    </button>
                                                </h3>
                                                <div id="faq{{ $faq->id }}"
                                                    class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}"
                                                    data-bs-parent="#faqAccordion">
                                                    <div class="accordion-body">
                                                        {{ $faq->answer }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <!-- Fallback FAQs -->
                                        <div class="accordion-item border-0 mb-3 shadow-sm">
                                            <h3 class="accordion-header">
                                                <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                    data-bs-target="#faq1">
                                                    How quickly can we get started?
                                                </button>
                                            </h3>
                                            <div id="faq1" class="accordion-collapse collapse show"
                                                data-bs-parent="#faqAccordion">
                                                <div class="accordion-body">
                                                    You can start using SMATCAMPUS immediately after signup. Our team will
                                                    help you import your existing data and train your staff within days.
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            @elseif($section->component == 'landing.cta' || $section->component == 'cta')
                <!-- CTA Section -->
                <section class="py-5"
                    style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
                    <div class="container text-center text-white py-5">
                        <h2 class="display-6 fw-medium mb-3">Ready to Transform Your School?</h2>
                        <p class="lead mb-4">Join hundreds of schools already using SMATCAMPUS</p>
                        <div class="d-flex gap-3 justify-content-center flex-wrap">
                            <a href="#contact" class="btn btn-light btn-lg">Contact Sales</a>
                            <a href="#contact" class="btn btn-outline-light btn-lg">Schedule Demo</a>
                        </div>
                    </div>
                </section>
            @endif
        @endforeach
    @else
        <!-- Fallback if no sections defined (Original Layout) -->
        <!-- Hero Section -->
        <section class="py-5 hero-section"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-left: 0; margin-right: 0; width: 100%; min-height: 80vh;">
            <div class="container-fluid px-5">
                <div class="row align-items-center h-100">
                    <div class="col-lg-6 mb-4 mb-lg-0 px-4 px-lg-5">
                        <h1 class="display-5 fw-semibold mb-4">Transform Your School Management</h1>
                        <p class="lead mb-4">Streamline operations, enhance learning, and empower your institution with our
                            comprehensive school management system.</p>
                        <div class="d-flex gap-3">
                            <a href="#contact" class="btn btn-primary btn-lg">Contact Sales</a>
                            <a href="#features" class="btn btn-outline-light btn-lg">Learn More</a>
                        </div>
                    </div>
                    <div class="col-lg-6 px-4 px-lg-5">
                        <div class="dashboard-mockup bg-white rounded shadow-lg p-4">
                            <!-- Dashboard Header -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="text-dark mb-0 fw-bold">School Dashboard</h6>
                                <span class="badge bg-success">Live</span>
                            </div>

                            <!-- Stats Cards -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="card border-0"
                                        style="background: linear-gradient(45deg, #4f46e5, #06b6d4);">
                                        <div class="card-body p-3 text-white">
                                            <small>Students</small>
                                            <h5 class="mb-0">1,247</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="card border-0"
                                        style="background: linear-gradient(45deg, #059669, #10b981);">
                                        <div class="card-body p-3 text-white">
                                            <small>Teachers</small>
                                            <h5 class="mb-0">89</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Chart Placeholder -->
                            <div class="bg-light p-3 mb-3" style="border-radius: 6px;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="text-muted">Attendance Rate</small>
                                    <small class="text-success fw-bold">96.8%</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-gradient"
                                        style="width: 96.8%; background: linear-gradient(90deg, #4f46e5, #06b6d4);"></div>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <div>
                                <small class="text-muted">Recent Activity</small>
                                <div class="mt-2">
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="bg-primary rounded-circle me-2" style="width: 8px; height: 8px;">
                                        </div>
                                        <small class="text-dark">New student registration</small>
                                    </div>
                                    <div class="d-flex align-items-center mb-1">
                                        <div class="bg-success rounded-circle me-2" style="width: 8px; height: 8px;">
                                        </div>
                                        <small class="text-dark">Grade reports submitted</small>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-warning rounded-circle me-2" style="width: 8px; height: 8px;">
                                        </div>
                                        <small class="text-dark">Parent meeting scheduled</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section class="py-5 bg-light">
            <div class="container">
                <div class="row g-4">
                    <div class="col-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center stats-card">
                            <div class="card-body py-4">
                                <h2 class="display-5 fw-bold text-primary mb-2">500+</h2>
                                <p class="text-muted mb-0">Schools Trust Us</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center stats-card">
                            <div class="card-body py-4">
                                <h2 class="display-5 fw-bold text-primary mb-2">50K+</h2>
                                <p class="text-muted mb-0">Active Students</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center stats-card">
                            <div class="card-body py-4">
                                <h2 class="display-5 fw-bold text-primary mb-2">99.9%</h2>
                                <p class="text-muted mb-0">Uptime Guaranteed</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card h-100 border-0 shadow-sm text-center stats-card">
                            <div class="card-body py-4">
                                <h2 class="display-5 fw-bold text-primary mb-2">4.9/5</h2>
                                <p class="text-muted mb-0">Customer Rating</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-5">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-medium mb-3">Everything You Need to Manage Your School</h2>
                    <p class="lead text-muted">Powerful features designed for modern educational institutions</p>
                </div>
                <div class="row g-4">
                    @if (isset($features) && $features->count() > 0)
                        @foreach ($features as $feature)
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <div class="icon-circle mb-3 d-flex align-items-center justify-content-center"
                                            style="width: 56px; height: 56px; background: {{ $feature->icon_bg_color }};">
                                            <i class="bi {{ $feature->icon }} fs-3"
                                                style="color: {{ str_starts_with($feature->icon_color, 'var') ? $feature->icon_color : '' }}"
                                                class="{{ !str_starts_with($feature->icon_color, 'var') ? $feature->icon_color : '' }}"></i>
                                        </div>
                                        <h3 class="h4 fw-bold mb-3">{{ $feature->title }}</h3>
                                        <p class="text-muted">{{ $feature->description }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <!-- Fallback to hardcoded features if no dynamic features exist -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="icon-circle mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 56px; height: 56px; background: rgba(79, 70, 229, 0.1);">
                                        <i class="bi bi-people-fill fs-3 text-primary"></i>
                                    </div>
                                    <h3 class="h4 fw-bold mb-3">Student Management</h3>
                                    <p class="text-muted">Comprehensive student profiles, enrollment tracking, and academic
                                        records
                                        management.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="icon-circle mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 56px; height: 56px; background: rgba(6, 182, 212, 0.1);">
                                        <i class="bi bi-calendar-check fs-3" style="color: var(--secondary-color);"></i>
                                    </div>
                                    <h3 class="h4 fw-bold mb-3">Attendance & Timetable</h3>
                                    <p class="text-muted">Digital attendance tracking and smart timetable generation with
                                        conflict
                                        detection.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="icon-circle mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 56px; height: 56px; background: rgba(245, 158, 11, 0.15);">
                                        <i class="bi bi-journal-text fs-3" style="color: var(--accent-color);"></i>
                                    </div>
                                    <h3 class="h4 fw-bold mb-3">Academic Management</h3>
                                    <p class="text-muted">Grade books, report cards, and comprehensive academic performance
                                        analytics.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="icon-circle mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 56px; height: 56px; background: rgba(79, 70, 229, 0.1);">
                                        <i class="bi bi-cash-stack fs-3 text-primary"></i>
                                    </div>
                                    <h3 class="h4 fw-bold mb-3">Fee Management</h3>
                                    <p class="text-muted">Automated fee collection, receipts, and financial reporting made
                                        simple.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="icon-circle mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 56px; height: 56px; background: rgba(6, 182, 212, 0.1);">
                                        <i class="bi bi-chat-dots fs-3" style="color: var(--secondary-color);"></i>
                                    </div>
                                    <h3 class="h4 fw-bold mb-3">Communication Hub</h3>
                                    <p class="text-muted">SMS, email, and in-app messaging to keep everyone connected.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="card-body p-4">
                                    <div class="icon-circle mb-3 d-flex align-items-center justify-content-center"
                                        style="width: 56px; height: 56px; background: rgba(245, 158, 11, 0.15);">
                                        <i class="bi bi-graph-up fs-3" style="color: var(--accent-color);"></i>
                                    </div>
                                    <h3 class="h4 fw-bold mb-3">Analytics & Reports</h3>
                                    <p class="text-muted">Insightful dashboards and customizable reports for data-driven
                                        decisions.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-5 bg-light">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-medium mb-3">Choose Your Plan</h2>
                    <p class="lead text-muted">Flexible pricing for schools of all sizes</p>
                </div>
                <div class="row g-4">
                    @foreach ($plans as $plan)
                        <div class="col-lg-3">
                            <div class="card h-100 border-0 shadow"
                                @if ($plan->is_highlighted) style="transform: scale(1.05); border: 3px solid var(--primary-color) !important; z-index: 10;" @endif>
                                <div class="card-body p-4">
                                    @if ($plan->is_highlighted)
                                        <div class="badge bg-primary mb-3">MOST POPULAR</div>
                                    @endif
                                    <h3 class="h4 fw-bold mb-3">{{ $plan->name }}</h3>
                                    <p class="text-muted mb-4">{{ $plan->tagline }}</p>
                                    <div class="mb-4">
                                        <span class="h2 fw-bold">{{ $plan->display_price }}</span>
                                        @if ($plan->billing_period_label)
                                            <span class="text-muted">{{ $plan->billing_period_label }}</span>
                                        @endif
                                    </div>
                                    <ul class="list-unstyled mb-4">
                                        @foreach ($plan->features_list as $feature)
                                            <li class="mb-2"><i
                                                    class="bi bi-check-circle-fill text-success me-2"></i>{{ $feature }}
                                            </li>
                                        @endforeach
                                    </ul>
                                    @php
                                        $ctaUrl = $plan->price_amount === null ? '#contact' : route('register');
                                    @endphp
                                    <a href="{{ $ctaUrl }}"
                                        class="btn {{ $plan->is_highlighted ? 'btn-primary' : 'btn-outline-primary' }} w-100">{{ $plan->cta_label }}</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Testimonials Section -->
        <section id="testimonials" class="py-5">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-medium mb-3">What Schools Are Saying</h2>
                    <p class="lead text-muted">Trusted by educational institutions worldwide</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                                <p class="mb-4">"SMATCAMPUS has revolutionized how we manage our school. The interface is
                                    intuitive and the support team is exceptional."</p>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3"
                                        style="width: 48px; height: 48px;">
                                        <span class="fw-bold">JD</span>
                                    </div>
                                    <div>
                                        <strong>Jane Doe</strong>
                                        <p class="text-muted mb-0 small">Principal, Greenwood High</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                                <p class="mb-4">"The automation features saved us countless hours. Parent communication
                                    has
                                    never been easier!"</p>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3"
                                        style="width: 48px; height: 48px;">
                                        <span class="fw-bold">MS</span>
                                    </div>
                                    <div>
                                        <strong>Michael Smith</strong>
                                        <p class="text-muted mb-0 small">Administrator, Valley School</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            <div class="card-body p-4">
                                <div class="mb-3">
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                                <p class="mb-4">"Outstanding platform! The analytics help us make informed decisions
                                    about
                                    our academic programs."</p>
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3"
                                        style="width: 48px; height: 48px;">
                                        <span class="fw-bold">SJ</span>
                                    </div>
                                    <div>
                                        <strong>Sarah Johnson</strong>
                                        <p class="text-muted mb-0 small">Director, Riverside Academy</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="py-5 bg-light">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-medium mb-3">Frequently Asked Questions</h2>
                    <p class="lead text-muted">Got questions? We've got answers</p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="accordion" id="faqAccordion">
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h3 class="accordion-header">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq1">
                                        How quickly can we get started?
                                    </button>
                                </h3>
                                <div id="faq1" class="accordion-collapse collapse show"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        You can start using SMATCAMPUS immediately after signup. Our team will help you
                                        import
                                        your existing data and train your staff within days.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq2">
                                        Is my data secure?
                                    </button>
                                </h3>
                                <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Absolutely! We use bank-level encryption, regular backups, and comply with
                                        international
                                        data protection standards including GDPR.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq3">
                                        Can I customize the system for my school?
                                    </button>
                                </h3>
                                <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Yes! Our system is highly customizable. You can configure workflows, reports, and
                                        even
                                        request custom features for Enterprise plans.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq4">
                                        What kind of support do you offer?
                                    </button>
                                </h3>
                                <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        We provide email support for all plans, priority support for Professional, and 24/7
                                        dedicated support for Enterprise customers.
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h3 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#faq5">
                                        Is there a free trial?
                                    </button>
                                </h3>
                                <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        Yes! We offer a 14-day free trial with full access to all features. No credit card
                                        required to start.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-5"
            style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">
            <div class="container text-center text-white py-5">
                <h2 class="display-6 fw-medium mb-3">Ready to Transform Your School?</h2>
                <p class="lead mb-4">Join hundreds of schools already using SMATCAMPUS</p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="#contact" class="btn btn-light btn-lg">Contact Sales</a>
                    <a href="#contact" class="btn btn-outline-light btn-lg">Schedule Demo</a>
                </div>
            </div>
        </section>
    @endif
@endsection

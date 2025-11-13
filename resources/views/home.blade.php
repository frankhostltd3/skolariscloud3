@extends('layouts.app')

@section('content')
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
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Register Your School</a>
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
                                <div class="card border-0" style="background: linear-gradient(45deg, #4f46e5, #06b6d4);">
                                    <div class="card-body p-3 text-white">
                                        <small>Students</small>
                                        <h5 class="mb-0">1,247</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card border-0" style="background: linear-gradient(45deg, #059669, #10b981);">
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
                                    <div class="bg-primary rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                                    <small class="text-dark">New student registration</small>
                                </div>
                                <div class="d-flex align-items-center mb-1">
                                    <div class="bg-success rounded-circle me-2" style="width: 8px; height: 8px;"></div>
                                    <small class="text-dark">Grade reports submitted</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning rounded-circle me-2" style="width: 8px; height: 8px;"></div>
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
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="icon-circle mb-3 d-flex align-items-center justify-content-center"
                                style="width: 56px; height: 56px; background: rgba(79, 70, 229, 0.1);">
                                <i class="bi bi-people-fill fs-3 text-primary"></i>
                            </div>
                            <h3 class="h4 fw-bold mb-3">Student Management</h3>
                            <p class="text-muted">Comprehensive student profiles, enrollment tracking, and academic records
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
                            <p class="text-muted">Digital attendance tracking and smart timetable generation with conflict
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
                            <p class="text-muted">Automated fee collection, receipts, and financial reporting made simple.
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
                            <p class="text-muted">Insightful dashboards and customizable reports for data-driven decisions.
                            </p>
                        </div>
                    </div>
                </div>
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
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow">
                        <div class="card-body p-4">
                            <h3 class="h4 fw-bold mb-3">Starter</h3>
                            <p class="text-muted mb-4">Perfect for small schools</p>
                            <div class="mb-4">
                                <span class="h2 fw-bold">$99</span>
                                <span class="text-muted">/month</span>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Up to 200
                                    students</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Basic features
                                </li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Email support
                                </li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Mobile app
                                    access</li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Get Started</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow"
                        style="transform: scale(1.05); border: 3px solid var(--primary-color) !important;">
                        <div class="card-body p-4">
                            <div class="badge bg-primary mb-3">MOST POPULAR</div>
                            <h3 class="h4 fw-bold mb-3">Professional</h3>
                            <p class="text-muted mb-4">For growing institutions</p>
                            <div class="mb-4">
                                <span class="h2 fw-bold">$249</span>
                                <span class="text-muted">/month</span>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Up to 1000
                                    students</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>All features
                                </li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Priority
                                    support</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Advanced
                                    analytics</li>
                            </ul>
                            <a href="{{ route('register') }}" class="btn btn-primary w-100">Get Started</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card h-100 border-0 shadow">
                        <div class="card-body p-4">
                            <h3 class="h4 fw-bold mb-3">Enterprise</h3>
                            <p class="text-muted mb-4">For large organizations</p>
                            <div class="mb-4">
                                <span class="h2 fw-bold">Custom</span>
                            </div>
                            <ul class="list-unstyled mb-4">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Unlimited
                                    students</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Custom
                                    features</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>24/7 dedicated
                                    support</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>On-premise
                                    option</li>
                            </ul>
                            <a href="#" class="btn btn-outline-primary w-100">Contact Sales</a>
                        </div>
                    </div>
                </div>
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
                            <p class="mb-4">"The automation features saved us countless hours. Parent communication has
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
                            <p class="mb-4">"Outstanding platform! The analytics help us make informed decisions about
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
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    You can start using SMATCAMPUS immediately after signup. Our team will help you import
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
                                    Absolutely! We use bank-level encryption, regular backups, and comply with international
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
                                    Yes! Our system is highly customizable. You can configure workflows, reports, and even
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
                <a href="{{ route('register') }}" class="btn btn-light btn-lg">Register Your School</a>
                <a href="#" class="btn btn-outline-light btn-lg">Schedule Demo</a>
            </div>
        </div>
    </section>
@endsection

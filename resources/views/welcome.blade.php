@extends('layouts.app')@extends('layouts.app')



@section('content')@section('content')

<!-- Hero Section --><!-- Hero Section -->

<section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin: 0; padding-left: 0 !important; padding-right: 0 !important;"><section class="py-5" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; margin-left: 0; margin-right: 0; width: 100%;">

    <div class="container-fluid px-4 py-5">    <div class="container-fluid px-4">

        <div class="row align-items-center">        <div class="row align-items-center">

            <div class="col-lg-6 mb-4 mb-lg-0">            <div class="col-lg-6 mb-4 mb-lg-0">

                <h1 class="display-4 fw-bold mb-4">Transform Your School Management</h1>                <h1 class="display-4 fw-bold mb-4">Transform Your School Management</h1>

                <p class="lead mb-4">Streamline operations, enhance learning, and empower your institution with our comprehensive school management system.</p>                <p class="lead mb-4">Streamline operations, enhance learning, and empower your institution with our comprehensive school management system.</p>

                <div class="d-flex gap-3">                <div class="d-flex gap-3">

                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Start Free Trial</a>                    <a href="{{ route('register') }}" class="btn btn-primary btn-lg">Start Free Trial</a>

                    <a href="#features" class="btn btn-outline-light btn-lg">Learn More</a>                    <a href="#features" class="btn btn-outline-light btn-lg">Learn More</a>

                </div>                </div>

            </div>            </div>

            <div class="col-lg-6">            <div class="col-lg-6">

                <img src="https://via.placeholder.com/600x400/667eea/ffffff?text=School+Dashboard" alt="Dashboard" class="img-fluid rounded shadow-lg">                <img src="https://via.placeholder.com/600x400/667eea/ffffff?text=School+Dashboard" alt="Dashboard" class="img-fluid rounded shadow-lg">

            </div>            </div>

        </div>        </div>

    </div>    </div>

</section></section>



<!-- Stats Section --><!-- Stats Section -->

<section class="py-5 bg-light"><section class="py-5 bg-light">

    <div class="container">    <div class="container">

        <div class="row text-center">        <div class="row text-center">

            <div class="col-md-3 mb-4 mb-md-0">            <div class="col-md-3 mb-4 mb-md-0">

                <h2 class="display-4 fw-bold" style="color: var(--primary-color);">500+</h2>                <h2 class="display-4 fw-bold" style="color: var(--primary-color);">500+</h2>

                <p class="text-muted">Schools Trust Us</p>                <p class="text-muted">Schools Trust Us</p>

            </div>            </div>

            <div class="col-md-3 mb-4 mb-md-0">            <div class="col-md-3 mb-4 mb-md-0">

                <h2 class="display-4 fw-bold" style="color: var(--primary-color);">50K+</h2>                <h2 class="display-4 fw-bold" style="color: var(--primary-color);">50K+</h2>

                <p class="text-muted">Active Students</p>                <p class="text-muted">Active Students</p>

            </div>            </div>

            <div class="col-md-3 mb-4 mb-md-0">            <div class="col-md-3 mb-4 mb-md-0">

                <h2 class="display-4 fw-bold" style="color: var(--primary-color);">99.9%</h2>                <h2 class="display-4 fw-bold" style="color: var(--primary-color);">99.9%</h2>

                <p class="text-muted">Uptime Guaranteed</p>                <p class="text-muted">Uptime Guaranteed</p>

            </div>            </div>

            <div class="col-md-3">            <div class="col-md-3">

                <h2 class="display-4 fw-bold" style="color: var(--primary-color);">4.9/5</h2>                <h2 class="display-4 fw-bold" style="color: var(--primary-color);">4.9/5</h2>

                <p class="text-muted">Customer Rating</p>                <p class="text-muted">Customer Rating</p>

            </div>            </div>

        </div>        </div>

    </div>    </div>

</section></section>



<!-- Features Section --><!-- Features Section -->

<section id="features" class="py-5"><section id="features" class="py-5">

    <div class="container">    <div class="container">

        <div class="text-center mb-5">        <div class="text-center mb-5">

            <h2 class="display-5 fw-bold mb-3">Everything You Need to Manage Your School</h2>            <h2 class="display-5 fw-bold mb-3">Everything You Need to Manage Your School</h2>

            <p class="lead text-muted">Powerful features designed for modern educational institutions</p>            <p class="lead text-muted">Powerful features designed for modern educational institutions</p>

        </div>        </div>

        <div class="row g-4">        <div class="row g-4">

            <div class="col-md-6 col-lg-4">            <div class="col-md-6 col-lg-4">

                <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;">                <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;">

                    <div class="card-body p-4">                    <div class="card-body p-4">

                        <div class="mb-3">                        <div class="mb-3">

                            <i class="bi bi-people-fill fs-1" style="color: var(--primary-color);"></i>                            <i class="bi bi-people-fill fs-1" style="color: var(--primary-color);"></i>

                        </div>                        </div>

                        <h3 class="h4 fw-bold mb-3">Student Management</h3>                        <h3 class="h4 fw-bold mb-3">Student Management</h3>

                        <p class="text-muted">Comprehensive student profiles, enrollment tracking, and academic records management.</p>                        <p class="text-muted">Comprehensive student profiles, enrollment tracking, and academic records management.</p>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

            <div class="col-md-6 col-lg-4">            <div class="col-md-6 col-lg-4">

                <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;">                <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;">

                    <div class="card-body p-4">                    <div class="card-body p-4">

                        <div class="mb-3">                        <div class="mb-3">

                            <i class="bi bi-calendar-check fs-1" style="color: var(--secondary-color);"></i>                            <i class="bi bi-calendar-check fs-1" style="color: var(--secondary-color);"></i>

                        </div>                        </div>

                        <h3 class="h4 fw-bold mb-3">Attendance & Timetable</h3>                        <h3 class="h4 fw-bold mb-3">Attendance & Timetable</h3>

                        <p class="text-muted">Digital attendance tracking and smart timetable generation with conflict detection.</p>                        <p class="text-muted">Digital attendance tracking and smart timetable generation with conflict detection.</p>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

            <div class="col-md-6 col-lg-4">            <div class="col-md-6 col-lg-4">

                <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;">                <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;">

                    <div class="card-body p-4">                    <div class="card-body p-4">

                        <div class="mb-3">                        <div class="mb-3">

                            <i class="bi bi-journal-text fs-1" style="color: var(--accent-color);"></i>                            <i class="bi bi-journal-text fs-1" style="color: var(--accent-color);"></i>

                        </div>                        </div>

                        <h3 class="h4 fw-bold mb-3">Academic Management</h3>                        <h3 class="h4 fw-bold mb-3">Academic Management</h3>

                        <p class="text-muted">Grade books, report cards, and comprehensive academic performance analytics.</p>                        <p class="text-muted">Grade books, report cards, and comprehensive academic performance analytics.</p>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

            <div class="col-md-6 col-lg-4">            <div class="col-md-6 col-lg-4">

                <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;">                <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;">

                    <div class="card-body p-4">                    <div class="card-body p-4">

                        <div class="mb-3">                        <div class="mb-3">

                            <i class="bi bi-cash-stack fs-1" style="color: var(--primary-color);"></i>                            <i class="bi bi-cash-stack fs-1" style="color: var(--primary-color);"></i>

                        </div>                        </div>

                        <h3 class="h4 fw-bold mb-3">Fee Management</h3>                        <h3 class="h4 fw-bold mb-3">Fee Management</h3>

                        <p class="text-muted">Automated fee collection, receipts, and financial reporting made simple.</p>                        <p class="text-muted">Automated fee collection, receipts, and financial reporting made simple.</p>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

            <div class="col-md-6 col-lg-4">            <div class="col-md-6 col-lg-4">

                <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;">                <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;">

                    <div class="card-body p-4">                    <div class="card-body p-4">

                        <div class="mb-3">                        <div class="mb-3">

                            <i class="bi bi-chat-dots fs-1" style="color: var(--secondary-color);"></i>                            <i class="bi bi-chat-dots fs-1" style="color: var(--secondary-color);"></i>

                        </div>                        </div>

                        <h3 class="h4 fw-bold mb-3">Communication Hub</h3>                        <h3 class="h4 fw-bold mb-3">Communication Hub</h3>

                        <p class="text-muted">SMS, email, and in-app messaging to keep everyone connected.</p>                        <p class="text-muted">SMS, email, and in-app messaging to keep everyone connected.</p>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

            <div class="col-md-6 col-lg-4">            <div class="col-md-6 col-lg-4">

                <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;">                <div class="card h-100 border-0 shadow-sm" style="transition: transform 0.3s;">

                    <div class="card-body p-4">                    <div class="card-body p-4">

                        <div class="mb-3">                        <div class="mb-3">

                            <i class="bi bi-graph-up fs-1" style="color: var(--accent-color);"></i>                            <i class="bi bi-graph-up fs-1" style="color: var(--accent-color);"></i>

                        </div>                        </div>

                        <h3 class="h4 fw-bold mb-3">Analytics & Reports</h3>                        <h3 class="h4 fw-bold mb-3">Analytics & Reports</h3>

                        <p class="text-muted">Insightful dashboards and customizable reports for data-driven decisions.</p>                        <p class="text-muted">Insightful dashboards and customizable reports for data-driven decisions.</p>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

        </div>        </div>

    </div>    </div>

</section></section>



<!-- Pricing Section --><!-- Pricing Section -->

<section id="pricing" class="py-5 bg-light"><section id="pricing" class="py-5 bg-light">

    <div class="container">    <div class="container">

        <div class="text-center mb-5">        <div class="text-center mb-5">

            <h2 class="display-5 fw-bold mb-3">Choose Your Plan</h2>            <h2 class="display-5 fw-bold mb-3">Choose Your Plan</h2>

            <p class="lead text-muted">Flexible pricing for schools of all sizes</p>            <p class="lead text-muted">Flexible pricing for schools of all sizes</p>

        </div>        </div>

        <div class="row g-4">        <div class="row g-4">

            <div class="col-lg-4">            <div class="col-lg-4">

                <div class="card h-100 border-0 shadow">                <div class="card h-100 border-0 shadow">

                    <div class="card-body p-4">                    <div class="card-body p-4">

                        <h3 class="h4 fw-bold mb-3">Starter</h3>                        <h3 class="h4 fw-bold mb-3">Starter</h3>

                        <p class="text-muted mb-4">Perfect for small schools</p>                        <p class="text-muted mb-4">Perfect for small schools</p>

                        <div class="mb-4">                        <div class="mb-4">

                            <span class="h2 fw-bold">$99</span>                            <span class="h2 fw-bold">$99</span>

                            <span class="text-muted">/month</span>                            <span class="text-muted">/month</span>

                        </div>                        </div>

                        <ul class="list-unstyled mb-4">                        <ul class="list-unstyled mb-4">

                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Up to 200 students</li>                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Up to 200 students</li>

                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Basic features</li>                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Basic features</li>

                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Email support</li>                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Email support</li>

                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Mobile app access</li>                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Mobile app access</li>

                        </ul>                        </ul>

                        <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Get Started</a>                        <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">Get Started</a>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

            <div class="col-lg-4">            <div class="col-lg-4">

                <div class="card h-100 border-0 shadow" style="transform: scale(1.05); border: 3px solid var(--primary-color) !important;">                <div class="card h-100 border-0 shadow" style="transform: scale(1.05); border: 3px solid var(--primary-color) !important;">

                    <div class="card-body p-4">                    <div class="card-body p-4">

                        <div class="badge bg-primary mb-3">MOST POPULAR</div>                        <div class="badge bg-primary mb-3">MOST POPULAR</div>

                        <h3 class="h4 fw-bold mb-3">Professional</h3>                        <h3 class="h4 fw-bold mb-3">Professional</h3>

                        <p class="text-muted mb-4">For growing institutions</p>                        <p class="text-muted mb-4">For growing institutions</p>

                        <div class="mb-4">                        <div class="mb-4">

                            <span class="h2 fw-bold">$249</span>                            <span class="h2 fw-bold">$249</span>

                            <span class="text-muted">/month</span>                            <span class="text-muted">/month</span>

                        </div>                        </div>

                        <ul class="list-unstyled mb-4">                        <ul class="list-unstyled mb-4">

                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Up to 1000 students</li>                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Up to 1000 students</li>

                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>All features</li>                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>All features</li>

                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Priority support</li>                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Priority support</li>

                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Advanced analytics</li>                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Advanced analytics</li>

                        </ul>                        </ul>

                        <a href="{{ route('register') }}" class="btn btn-primary w-100">Get Started</a>                        <a href="{{ route('register') }}" class="btn btn-primary w-100">Get Started</a>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

            <div class="col-lg-4">            <div class="col-lg-4">

                <div class="card h-100 border-0 shadow">                <div class="card h-100 border-0 shadow">

                    <div class="card-body p-4">                    <div class="card-body p-4">

                        <h3 class="h4 fw-bold mb-3">Enterprise</h3>                        <h3 class="h4 fw-bold mb-3">Enterprise</h3>

                        <p class="text-muted mb-4">For large organizations</p>                        <p class="text-muted mb-4">For large organizations</p>

                        <div class="mb-4">                        <div class="mb-4">

                            <span class="h2 fw-bold">Custom</span>                            <span class="h2 fw-bold">Custom</span>

                        </div>                        </div>

                        <ul class="list-unstyled mb-4">                        <ul class="list-unstyled mb-4">

                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Unlimited students</li>                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Unlimited students</li>

                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Custom features</li>                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Custom features</li>

                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>24/7 dedicated support</li>                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>24/7 dedicated support</li>

                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>On-premise option</li>                            <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>On-premise option</li>

                        </ul>                        </ul>

                        <a href="#" class="btn btn-outline-primary w-100">Contact Sales</a>                        <a href="#" class="btn btn-outline-primary w-100">Contact Sales</a>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

        </div>        </div>

    </div>    </div>

</section></section>



<!-- Testimonials Section --><!-- Testimonials Section -->

<section id="testimonials" class="py-5"><section id="testimonials" class="py-5">

    <div class="container">    <div class="container">

        <div class="text-center mb-5">        <div class="text-center mb-5">

            <h2 class="display-5 fw-bold mb-3">What Schools Are Saying</h2>            <h2 class="display-5 fw-bold mb-3">What Schools Are Saying</h2>

            <p class="lead text-muted">Trusted by educational institutions worldwide</p>            <p class="lead text-muted">Trusted by educational institutions worldwide</p>

        </div>        </div>

        <div class="row g-4">        <div class="row g-4">

            <div class="col-md-4">            <div class="col-md-4">

                <div class="card h-100 border-0 shadow-sm">                <div class="card h-100 border-0 shadow-sm">

                    <div class="card-body p-4">                    <div class="card-body p-4">

                        <div class="mb-3">                        <div class="mb-3">

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                        </div>                        </div>

                        <p class="mb-4">"SMATCAMPUS has revolutionized how we manage our school. The interface is intuitive and the support team is exceptional."</p>                        <p class="mb-4">"SMATCAMPUS has revolutionized how we manage our school. The interface is intuitive and the support team is exceptional."</p>

                        <div class="d-flex align-items-center">                        <div class="d-flex align-items-center">

                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">

                                <span class="fw-bold">JD</span>                                <span class="fw-bold">JD</span>

                            </div>                            </div>

                            <div>                            <div>

                                <strong>Jane Doe</strong>                                <strong>Jane Doe</strong>

                                <p class="text-muted mb-0 small">Principal, Greenwood High</p>                                <p class="text-muted mb-0 small">Principal, Greenwood High</p>

                            </div>                            </div>

                        </div>                        </div>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

            <div class="col-md-4">            <div class="col-md-4">

                <div class="card h-100 border-0 shadow-sm">                <div class="card h-100 border-0 shadow-sm">

                    <div class="card-body p-4">                    <div class="card-body p-4">

                        <div class="mb-3">                        <div class="mb-3">

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                        </div>                        </div>

                        <p class="mb-4">"The automation features saved us countless hours. Parent communication has never been easier!"</p>                        <p class="mb-4">"The automation features saved us countless hours. Parent communication has never been easier!"</p>

                        <div class="d-flex align-items-center">                        <div class="d-flex align-items-center">

                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">

                                <span class="fw-bold">MS</span>                                <span class="fw-bold">MS</span>

                            </div>                            </div>

                            <div>                            <div>

                                <strong>Michael Smith</strong>                                <strong>Michael Smith</strong>

                                <p class="text-muted mb-0 small">Administrator, Valley School</p>                                <p class="text-muted mb-0 small">Administrator, Valley School</p>

                            </div>                            </div>

                        </div>                        </div>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

            <div class="col-md-4">            <div class="col-md-4">

                <div class="card h-100 border-0 shadow-sm">                <div class="card h-100 border-0 shadow-sm">

                    <div class="card-body p-4">                    <div class="card-body p-4">

                        <div class="mb-3">                        <div class="mb-3">

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                            <i class="bi bi-star-fill text-warning"></i>                            <i class="bi bi-star-fill text-warning"></i>

                        </div>                        </div>

                        <p class="mb-4">"Outstanding platform! The analytics help us make informed decisions about our academic programs."</p>                        <p class="mb-4">"Outstanding platform! The analytics help us make informed decisions about our academic programs."</p>

                        <div class="d-flex align-items-center">                        <div class="d-flex align-items-center">

                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">

                                <span class="fw-bold">SJ</span>                                <span class="fw-bold">SJ</span>

                            </div>                            </div>

                            <div>                            <div>

                                <strong>Sarah Johnson</strong>                                <strong>Sarah Johnson</strong>

                                <p class="text-muted mb-0 small">Director, Riverside Academy</p>                                <p class="text-muted mb-0 small">Director, Riverside Academy</p>

                            </div>                            </div>

                        </div>                        </div>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

        </div>        </div>

    </div>    </div>

</section></section>



<!-- FAQ Section --><!-- FAQ Section -->

<section id="faq" class="py-5 bg-light"><section id="faq" class="py-5 bg-light">

    <div class="container">    <div class="container">

        <div class="text-center mb-5">        <div class="text-center mb-5">

            <h2 class="display-5 fw-bold mb-3">Frequently Asked Questions</h2>            <h2 class="display-5 fw-bold mb-3">Frequently Asked Questions</h2>

            <p class="lead text-muted">Got questions? We've got answers</p>            <p class="lead text-muted">Got questions? We've got answers</p>

        </div>        </div>

        <div class="row justify-content-center">        <div class="row justify-content-center">

            <div class="col-lg-8">            <div class="col-lg-8">

                <div class="accordion" id="faqAccordion">                <div class="accordion" id="faqAccordion">

                    <div class="accordion-item border-0 mb-3 shadow-sm">                    <div class="accordion-item border-0 mb-3 shadow-sm">

                        <h3 class="accordion-header">                        <h3 class="accordion-header">

                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">

                                How quickly can we get started?                                How quickly can we get started?

                            </button>                            </button>

                        </h3>                        </h3>

                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">

                            <div class="accordion-body">                            <div class="accordion-body">

                                You can start using SMATCAMPUS immediately after signup. Our team will help you import your existing data and train your staff within days.                                You can start using SMATCAMPUS immediately after signup. Our team will help you import your existing data and train your staff within days.

                            </div>                            </div>

                        </div>                        </div>

                    </div>                    </div>

                    <div class="accordion-item border-0 mb-3 shadow-sm">                    <div class="accordion-item border-0 mb-3 shadow-sm">

                        <h3 class="accordion-header">                        <h3 class="accordion-header">

                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">

                                Is my data secure?                                Is my data secure?

                            </button>                            </button>

                        </h3>                        </h3>

                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">

                            <div class="accordion-body">                            <div class="accordion-body">

                                Absolutely! We use bank-level encryption, regular backups, and comply with international data protection standards including GDPR.                                Absolutely! We use bank-level encryption, regular backups, and comply with international data protection standards including GDPR.

                            </div>                            </div>

                        </div>                        </div>

                    </div>                    </div>

                    <div class="accordion-item border-0 mb-3 shadow-sm">                    <div class="accordion-item border-0 mb-3 shadow-sm">

                        <h3 class="accordion-header">                        <h3 class="accordion-header">

                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">

                                Can I customize the system for my school?                                Can I customize the system for my school?

                            </button>                            </button>

                        </h3>                        </h3>

                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">

                            <div class="accordion-body">                            <div class="accordion-body">

                                Yes! Our system is highly customizable. You can configure workflows, reports, and even request custom features for Enterprise plans.                                Yes! Our system is highly customizable. You can configure workflows, reports, and even request custom features for Enterprise plans.

                            </div>                            </div>

                        </div>                        </div>

                    </div>                    </div>

                    <div class="accordion-item border-0 mb-3 shadow-sm">                    <div class="accordion-item border-0 mb-3 shadow-sm">

                        <h3 class="accordion-header">                        <h3 class="accordion-header">

                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">

                                What kind of support do you offer?                                What kind of support do you offer?

                            </button>                            </button>

                        </h3>                        </h3>

                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">

                            <div class="accordion-body">                            <div class="accordion-body">

                                We provide email support for all plans, priority support for Professional, and 24/7 dedicated support for Enterprise customers.                                We provide email support for all plans, priority support for Professional, and 24/7 dedicated support for Enterprise customers.

                            </div>                            </div>

                        </div>                        </div>

                    </div>                    </div>

                    <div class="accordion-item border-0 mb-3 shadow-sm">                    <div class="accordion-item border-0 mb-3 shadow-sm">

                        <h3 class="accordion-header">                        <h3 class="accordion-header">

                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">

                                Is there a free trial?                                Is there a free trial?

                            </button>                            </button>

                        </h3>                        </h3>

                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">

                            <div class="accordion-body">                            <div class="accordion-body">

                                Yes! We offer a 14-day free trial with full access to all features. No credit card required to start.                                Yes! We offer a 14-day free trial with full access to all features. No credit card required to start.

                            </div>                            </div>

                        </div>                        </div>

                    </div>                    </div>

                </div>                </div>

            </div>            </div>

        </div>        </div>

    </div>    </div>

</section></section>



<!-- CTA Section --><!-- CTA Section -->

<section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%); margin: 0; padding-left: 0 !important; padding-right: 0 !important;"><section class="py-5" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);">

    <div class="container-fluid px-4 py-5 text-center text-white">    <div class="container text-center text-white">

        <h2 class="display-5 fw-bold mb-3">Ready to Transform Your School?</h2>        <h2 class="display-5 fw-bold mb-3">Ready to Transform Your School?</h2>

        <p class="lead mb-4">Join hundreds of schools already using SMATCAMPUS</p>        <p class="lead mb-4">Join hundreds of schools already using SMATCAMPUS</p>

        <div class="d-flex gap-3 justify-content-center">        <div class="d-flex gap-3 justify-content-center">

            <a href="{{ route('register') }}" class="btn btn-light btn-lg">Start Free Trial</a>            <a href="{{ route('register') }}" class="btn btn-light btn-lg">Start Free Trial</a>

            <a href="#" class="btn btn-outline-light btn-lg">Schedule Demo</a>            <a href="#" class="btn btn-outline-light btn-lg">Schedule Demo</a>

        </div>        </div>

    </div>    </div>

</section></section>



<style><style>

.card:hover {.card:hover {

    transform: translateY(-10px);    transform: translateY(-10px);

}}

</style></style>



@endsection@endsection


      

        @if (Route::has('login'))
            <div class="h-14.5 hidden lg:block"></div>
        @endif
    </body>
</html>

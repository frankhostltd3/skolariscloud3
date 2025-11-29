@extends('layouts.tenant.student')

@section('title', 'My Reports')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 text-gray-800">My Reports</h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                <button class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6 col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary me-3">
                                <i class="bi bi-journal-text fs-3"></i>
                            </div>
                            <h5 class="card-title mb-0">Academic Report Card</h5>
                        </div>
                        <p class="card-text text-muted small">
                            View your academic performance, grades, and teacher comments for the current term.
                        </p>
                        <hr>
                        <div class="d-grid gap-2">
                            <a href="{{ route('tenant.student.reports.generate') }}" target="_blank"
                                class="btn btn-outline-primary">
                                <i class="bi bi-eye me-2"></i>View Report
                            </a>
                            <a href="{{ route('tenant.student.reports.download') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-download me-2"></i>Download PDF
                            </a>
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-success dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-share me-2"></i>Share
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <form action="{{ route('tenant.student.reports.share.email') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-envelope me-2"></i>Via Email
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('tenant.student.reports.share.whatsapp') }}" method="POST"
                                            target="_blank">
                                            @csrf
                                            <button type="submit" class="dropdown-item">
                                                <i class="bi bi-whatsapp me-2"></i>Via WhatsApp
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

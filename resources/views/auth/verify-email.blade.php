@extends('tenant.layouts.app')

@section('title', 'Verify Your Email Address')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="bi bi-envelope-check me-2"></i>
                            Verify Your Email Address
                        </h4>
                    </div>

                    <div class="card-body">
                        @if (session('success'))
                            <div class="alert alert-success" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        <div class="text-center mb-4">
                            <i class="bi bi-envelope-exclamation" style="font-size: 4rem; color: #6c757d;"></i>
                        </div>

                        <p class="lead text-center mb-4">
                            Before proceeding, please check your email for a verification link.
                        </p>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>What's next?</strong>
                            <ul class="mb-0 mt-2">
                                <li>Check your email inbox ({{ auth()->user()->email }})</li>
                                <li>Click the verification link in the email</li>
                                <li>If you don't see the email, check your spam folder</li>
                            </ul>
                        </div>

                        <div class="text-center mt-4">
                            <p class="text-muted mb-3">
                                Didn't receive the email?
                            </p>
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-envelope-arrow-up me-2"></i>
                                    Resend Verification Email
                                </button>
                            </form>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>
                                Go to Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-question-circle me-2"></i>
                            Why do I need to verify my email?
                        </h6>
                        <p class="card-text text-muted mb-0">
                            Email verification helps us ensure the security of your account and provides a way to recover
                            access if needed.
                            This school requires all accounts to be verified before accessing certain features.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

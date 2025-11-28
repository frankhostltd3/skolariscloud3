@extends('tenant.layouts.guest')

@section('title', __('Account Pending Approval'))

@section('content-container')
@section('content-container')
    <div class="col-md-10 col-lg-8 col-xl-7 py-5">
        <div class="card shadow-sm border-0">
            <div class="card-body p-5 text-center">
                <span class="display-4 text-warning mb-4 d-block">
                    <i class="bi bi-hourglass-split"></i>
                </span>
                <h1 class="h3 fw-bold mb-3">{{ __('Your Account Is Pending Approval') }}</h1>
                <p class="text-muted mb-4">
                    {{ __('Thank you for registering. Your account is currently awaiting approval from the school administration. You will receive an email notification once your account has been reviewed.') }}
                </p>

                <div class="mb-4">
                    <p class="mb-1">
                        <strong>{{ __('What happens next?') }}</strong>
                    </p>
                    <ul class="list-unstyled text-start text-muted small mb-0">
                        <li class="d-flex align-items-start mb-2">
                            <span class="bi bi-check-circle text-success me-2"></span>
                            <span>{{ __('Administrators have been notified of your registration request.') }}</span>
                        </li>
                        <li class="d-flex align-items-start mb-2">
                            <span class="bi bi-envelope-fill text-primary me-2"></span>
                            <span>{{ __('You will receive an email when your account is approved or requires additional information.') }}</span>
                        </li>
                        <li class="d-flex align-items-start">
                            <span class="bi bi-chat-dots text-info me-2"></span>
                            <span>{{ __('If you need immediate access, please contact your school administration.') }}</span>
                        </li>
                    </ul>
                </div>

                <a href="{{ url('/') }}" class="btn btn-primary w-100">
                    <i class="bi bi-house me-2"></i>{{ __('Return to Homepage') }}
                </a>
            </div>
        </div>
    </div>
@endsection

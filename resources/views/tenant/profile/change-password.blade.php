@extends('tenant.layouts.app')

@section('title', __('Change Password'))

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Page Header -->
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h4 fw-semibold mb-1">{{ __('Change Password') }}</h1>
                    <p class="text-muted mb-0">{{ __('Update your account security credentials') }}</p>
                </div>
                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>{{ __('Back') }}
                </a>
            </div>

            <!-- Security Tips -->
            <div class="alert alert-info border-0 shadow-sm mb-4">
                <h5 class="alert-heading">
                    <i class="bi bi-shield-check me-2"></i>{{ __('Password Security Tips') }}
                </h5>
                <ul class="mb-0 ps-3">
                    <li>{{ __('Use a unique password that you don\'t use anywhere else') }}</li>
                    <li>{{ __('Include a mix of uppercase and lowercase letters, numbers, and symbols') }}</li>
                    <li>{{ __('Avoid using personal information like birthdays or names') }}</li>
                    <li>{{ __('Consider using a password manager to generate and store strong passwords') }}</li>
                </ul>
            </div>

            <!-- Change Password Form -->
            @include('tenant.profile.partials._change_password', [
                'changePasswordRoute' => route('tenant.profile.password.update')
            ])

            <!-- Recent Password Changes -->
            @if(auth()->user()->password_changed_at)
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="bi bi-clock-history text-muted" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h6 class="mb-1">{{ __('Last Password Change') }}</h6>
                            <p class="text-muted mb-0">
                                {{ auth()->user()->password_changed_at->diffForHumans() }}
                                <small class="d-block">
                                    {{ auth()->user()->password_changed_at->format('F j, Y \a\t g:i A') }}
                                </small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

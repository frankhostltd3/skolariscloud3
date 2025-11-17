@extends('tenant.layouts.app')

@section('title', __('Settings'))

@section('sidebar')
@include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ __('System Settings') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- General Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center">
                                    <i class="bi bi-gear-fill text-primary" style="font-size: 3rem;"></i>
                                    <h5 class="card-title mt-3">{{ __('General') }}</h5>
                                    <p class="card-text text-muted">{{ __('School information, branding, and basic settings') }}</p>
                                    <a href="{{ route('tenant.settings.admin.general') }}" class="btn btn-primary">
                                        <i class="bi bi-pencil-square"></i> {{ __('Configure') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="bi bi-book-fill text-success" style="font-size: 3rem;"></i>
                                    <h5 class="card-title mt-3">{{ __('Academic') }}</h5>
                                    <p class="card-text text-muted">{{ __('Academic year, grading, attendance, and exam settings') }}</p>
                                    <a href="{{ route('tenant.settings.admin.academic') }}" class="btn btn-success">
                                        <i class="bi bi-pencil-square"></i> {{ __('Configure') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- System Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <i class="bi bi-shield-check-fill text-warning" style="font-size: 3rem;"></i>
                                    <h5 class="card-title mt-3">{{ __('System') }}</h5>
                                    <p class="card-text text-muted">{{ __('Security, maintenance, backup, and API settings') }}</p>
                                    <a href="{{ route('tenant.settings.admin.system') }}" class="btn btn-warning">
                                        <i class="bi bi-pencil-square"></i> {{ __('Configure') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Finance Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="bi bi-cash-stack text-info" style="font-size: 3rem;"></i>
                                    <h5 class="card-title mt-3">{{ __('Finance') }}</h5>
                                    <p class="card-text text-muted">{{ __('Currency, fees, payments, and tax settings') }}</p>
                                    <a href="{{ route('tenant.settings.admin.finance') }}" class="btn btn-info">
                                        <i class="bi bi-pencil-square"></i> {{ __('Configure') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-danger">
                                <div class="card-body text-center">
                                    <i class="bi bi-lock-fill text-danger" style="font-size: 3rem;"></i>
                                    <h5 class="card-title mt-3">{{ __('Permissions') }}</h5>
                                    <p class="card-text text-muted">{{ __('Role-based access control and user permissions') }}</p>
                                    <a href="{{ route('tenant.settings.admin.permissions') }}" class="btn btn-danger">
                                        <i class="bi bi-pencil-square"></i> {{ __('Configure') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Email Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-secondary">
                                <div class="card-body text-center">
                                    <i class="bi bi-envelope-fill text-secondary" style="font-size: 3rem;"></i>
                                    <h5 class="card-title mt-3">{{ __('Email') }}</h5>
                                    <p class="card-text text-muted">{{ __('Email configuration and testing') }}</p>
                                    <a href="{{ route('tenant.settings.admin.email') }}" class="btn btn-secondary">
                                        <i class="bi bi-pencil-square"></i> {{ __('Configure') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Notifications Settings -->
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="bi bi-bell-fill text-info" style="font-size: 3rem;"></i>
                                    <h5 class="card-title mt-3">{{ __('Notifications') }}</h5>
                                    <p class="card-text text-muted">{{ __('SMS, WhatsApp, and notification providers') }}</p>
                                    <a href="{{ route('tenant.settings.admin.notifications') }}" class="btn btn-info">
                                        <i class="bi bi-pencil-square"></i> {{ __('Configure') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>{{ __('Quick Actions') }}</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('tenant.settings.admin.test-email') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-envelope"></i> {{ __('Test Email') }}
                                </a>
                                <a href="{{ route('tenant.settings.admin.backup') }}" class="btn btn-outline-success">
                                    <i class="bi bi-download"></i> {{ __('Create Backup') }}
                                </a>
                                <a href="{{ route('tenant.settings.admin.currency-rates') }}" class="btn btn-outline-info">
                                    <i class="bi bi-currency-exchange"></i> {{ __('Update Currency Rates') }}
                                </a>
                                <a href="{{ route('tenant.settings.admin.clear-cache') }}" class="btn btn-outline-warning">
                                    <i class="bi bi-trash"></i> {{ __('Clear Cache') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- System Information -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>{{ __('System Information') }}</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <td><strong>{{ __('Application Version') }}</strong></td>
                                            <td>{{ config('app.version', '1.0.0') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Laravel Version') }}</strong></td>
                                            <td>{{ app()->version() }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('PHP Version') }}</strong></td>
                                            <td>{{ PHP_VERSION }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Database') }}</strong></td>
                                            <td>{{ config('database.default') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Timezone') }}</strong></td>
                                            <td>{{ config('app.timezone') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Environment') }}</strong></td>
                                            <td>{{ config('app.env') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>{{ __('Available Currencies') }}</strong></td>
                                            <td>{{ __('Configured per tenant') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
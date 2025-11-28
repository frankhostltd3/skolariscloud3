@extends('layouts.app')

@section('title', __('Register your school'))

@section('content')
    <div class="container py-5 py-lg-6">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-lg-5">
                        <h1 class="h3 fw-semibold mb-2">{{ __('Create your school workspace') }}</h1>
                        <p class="text-secondary mb-4">{{ __('Start your free trial in minutes. No payment required.') }}</p>

                        @if ($errors->has('general'))
                            <div class="alert alert-danger">{{ $errors->first('general') }}</div>
                        @endif

                        @if ($success = session('registration_success'))
                            <div class="alert alert-success">
                                <h4 class="alert-heading fw-semibold mb-2">{{ __('Your school is ready!') }}</h4>
                                <p class="mb-2">
                                    {{ __(':school has been created successfully.', ['school' => $success['school'] ?? __('Your school')]) }}
                                </p>
                                @if (!empty($success['domain']))
                                    <p class="mb-2">
                                        {{ __('School address:') }}
                                        <a href="{{ $success['domain'] }}" target="_blank" rel="noopener"
                                            class="fw-semibold">{{ $success['domain'] }}</a>
                                    </p>
                                @endif
                                <ul class="mb-3 ms-3">
                                    @if (!empty($success['login_url']))
                                        <li>
                                            {{ __('Sign in using your admin email (:email) and password at', ['email' => $success['admin_email'] ?? __('your admin email')]) }}
                                            <a href="{{ $success['login_url'] }}" target="_blank"
                                                rel="noopener">{{ $success['login_url'] }}</a>.
                                        </li>
                                    @endif
                                    <li>{{ __('Share the new school address with your staff and families so they can log in.') }}
                                    </li>
                                    <li>{{ __('Check your inbox for a welcome email that includes onboarding resources.') }}
                                    </li>
                                </ul>
                                @if (!empty($success['login_url']))
                                    <a href="{{ $success['login_url'] }}" class="btn btn-success btn-sm" target="_blank"
                                        rel="noopener">
                                        {{ __('Go to login page') }}
                                    </a>
                                @endif
                            </div>
                        @endif

                        @if ($errorMessage = session('registration_error'))
                            <div class="alert alert-danger">
                                <strong>{{ __('Registration notice:') }}</strong>
                                <span class="d-block">{{ $errorMessage }}</span>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register.store') }}" novalidate>
                            @csrf
                            <div class="row g-4">
                                <div class="col-12">
                                    <label for="school_name" class="form-label fw-semibold">{{ __('School name') }}</label>
                                    <input type="text" class="form-control @error('school_name') is-invalid @enderror"
                                        id="school_name" name="school_name" value="{{ old('school_name') }}" required
                                        autofocus>
                                    @error('school_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="subdomain"
                                        class="form-label fw-semibold">{{ __('Desired school address') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('subdomain') is-invalid @enderror"
                                            id="subdomain" name="subdomain" value="{{ old('subdomain') }}"
                                            placeholder="starlight" aria-describedby="domainHelp" required>
                                        @if ($baseDomain)
                                            <span class="input-group-text">.{{ $baseDomain }}</span>
                                        @endif
                                    </div>
                                    @error('subdomain')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <div id="domainHelp" class="form-text">
                                        {{ __('We will create :url for your staff and families.', ['url' => __(':subdomain.:domain', ['subdomain' => __('your-school'), 'domain' => $baseDomain ?? __('example.com')])]) }}
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="admin_name" class="form-label fw-semibold">{{ __('Your name') }}</label>
                                    <input type="text" class="form-control @error('admin_name') is-invalid @enderror"
                                        id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required>
                                    @error('admin_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12">
                                    <label for="admin_email" class="form-label fw-semibold">{{ __('Work email') }}</label>
                                    <input type="email" class="form-control @error('admin_email') is-invalid @enderror"
                                        id="admin_email" name="admin_email" value="{{ old('admin_email') }}" required>
                                    @error('admin_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="password" class="form-label fw-semibold">{{ __('Password') }}</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-12 col-md-6">
                                    <label for="password_confirmation"
                                        class="form-label fw-semibold">{{ __('Confirm password') }}</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" value="1" id="terms" name="terms"
                                    @checked(old('terms')) required>
                                <label class="form-check-label" for="terms">
                                    {!! __('I agree to the <a href=":url" target="_blank">terms of service</a>.', ['url' => '#']) !!}
                                </label>
                                @error('terms')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-3 align-items-start align-items-sm-center mt-4">
                                <button type="submit" class="btn btn-primary px-4">{{ __('Create my school') }}</button>
                                <p class="text-secondary small mb-0">
                                    {{ __('We will set up your workspace instantly and email you setup tips.') }}</p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

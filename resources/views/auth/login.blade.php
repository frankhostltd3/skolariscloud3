@extends('layouts.app')

@section('content')
    <section class="py-5" style="background-color: #f9fafb; min-height: 70vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4 p-lg-5">
                            <h1 class="h3 fw-semibold mb-2 text-center">
                                @if ($school)
                                    Sign in to {{ $school->name }}
                                @else
                                    Sign in to your workspace
                                @endif
                            </h1>
                            @php($centralDomain = config('tenancy.central_domain'))

                            <p class="text-muted text-center mb-4">
                                @if ($school)
                                    Use your {{ $school->name }} credentials to continue.
                                @else
                                    Visit your school's unique address to sign in. Newly created schools receive a custom
                                    subdomain (e.g. your-school.{{ $centralDomain ?? 'example.com' }}).
                                @endif
                            </p>
                            <form method="POST" action="{{ route('login.store') }}" novalidate>
                                @csrf

                                @if (!$school)
                                    <div class="alert alert-warning" role="alert">
                                        Sign-ins happen on your school's workspace URL. Use the link we emailed during setup
                                        (for example, <span
                                            class="fw-semibold">https://your-school.{{ $centralDomain ?? 'example.com' }}</span>).
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" required
                                        autocomplete="email" autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required autocomplete="current-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="remember"
                                            name="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                    <a href="{{ route('password.request') }}" class="text-decoration-none small">Forgot
                                        password?</a>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg"
                                        @if (!$school) disabled @endif>Sign In</button>
                                </div>
                            </form>

                            <hr class="my-4">
                            <p class="text-center mb-0">Don't have an account?
                                <a href="{{ route('register') }}" class="text-decoration-none">Register your school</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

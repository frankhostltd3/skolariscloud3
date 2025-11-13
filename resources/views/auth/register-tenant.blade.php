@extends('layouts.app')

@section('content')
    <section class="py-5" style="background-color: #f9fafb; min-height: 70vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-6 col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4 p-lg-5">
                            @php($supportEmail = data_get($school->meta, 'support_email'))

                            <h1 class="h3 fw-semibold mb-2 text-center">Join {{ $school->name }}</h1>
                            <p class="text-muted text-center mb-4">Complete your account using the work email your school
                                invited. We'll match your invitation automatically.</p>

                            <form method="POST" action="{{ route('register.store') }}" novalidate>
                                @csrf

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="name" class="form-label">Full name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name') }}" required autofocus>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="email" class="form-label">Work email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                            id="email" name="email" value="{{ old('email') }}" required
                                            autocomplete="email">
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Must match the invitation sent by {{ $school->name }}.</div>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror"
                                            id="password" name="password" required autocomplete="new-password">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label">Confirm password</label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" required autocomplete="new-password">
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-4">
                                    <button type="submit" class="btn btn-primary btn-lg">Create my account</button>
                                </div>
                            </form>

                            <hr class="my-4">
                            <p class="text-center mb-0">Need a new invitation?
                                <a href="mailto:{{ $supportEmail ?? 'hello@' . ($school->domain ?? ($school->subdomain ? $school->subdomain . '.' . (config('tenancy.central_domain') ?? 'example.com') : config('tenancy.central_domain') ?? 'example.com')) }}"
                                    class="text-decoration-none">Contact your administrator</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

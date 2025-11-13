@extends('layouts.app')

@section('content')
    <section class="py-5" style="background-color: #f9fafb; min-height: 70vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4 p-lg-5">
                            <h1 class="h3 fw-semibold mb-2 text-center">Forgot your password?</h1>
                            <p class="text-muted text-center mb-4">
                                @if ($school)
                                    Enter the email address linked to your {{ $school->name }} account and we will send a
                                    reset link.
                                @else
                                    Password resets are handled on your school workspace. Visit your school's unique address
                                    to request a new link.
                                @endif
                            </p>

                            <form method="POST" action="{{ route('password.email') }}" novalidate>
                                @csrf

                                @if (!$school)
                                    <div class="alert alert-warning" role="alert">
                                        Open your school workspace URL (for example,
                                        https://your-school.{{ config('tenancy.central_domain') ?? 'example.com' }}) to
                                        reset your password.
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="email" class="form-label">Work email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email') }}" required
                                        autocomplete="email" @if (!$school) disabled @endif>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg"
                                        @if (!$school) disabled @endif>
                                        Email password reset link
                                    </button>
                                </div>
                            </form>

                            <hr class="my-4">
                            <p class="text-center mb-0">
                                <a href="{{ route('login') }}" class="text-decoration-none">Back to sign in</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

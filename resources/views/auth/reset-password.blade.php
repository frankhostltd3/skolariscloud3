@extends('layouts.app')

@section('content')
    <section class="py-5" style="background-color: #f9fafb; min-height: 70vh;">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4 p-lg-5">
                            <h1 class="h3 fw-semibold mb-2 text-center">Reset your password</h1>
                            <p class="text-muted text-center mb-4">
                                @if ($school)
                                    Choose a new password for your {{ $school->name }} account.
                                @else
                                    Password resets must be completed from your school workspace. Use the link sent in your
                                    email from your school's address.
                                @endif
                            </p>

                            <form method="POST" action="{{ route('password.store') }}" novalidate>
                                @csrf

                                <input type="hidden" name="token" value="{{ $token }}">

                                <div class="mb-3">
                                    <label for="email" class="form-label">Work email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" value="{{ old('email', $email) }}" required
                                        autocomplete="email" @if (!$school) disabled @endif>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">New password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        id="password" name="password" required autocomplete="new-password"
                                        @if (!$school) disabled @endif>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm password</label>
                                    <input type="password" class="form-control" id="password_confirmation"
                                        name="password_confirmation" required autocomplete="new-password"
                                        @if (!$school) disabled @endif>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg"
                                        @if (!$school) disabled @endif>
                                        Update password
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

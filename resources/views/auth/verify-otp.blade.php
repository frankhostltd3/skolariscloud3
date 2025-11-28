@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Verify Your Account') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <p>{{ __('Please enter the 6-digit code sent to your email address.') }}</p>

                        <form method="POST" action="{{ route('verification.otp.verify') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="otp"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Verification Code') }}</label>

                                <div class="col-md-6">
                                    <input id="otp" type="text"
                                        class="form-control @error('otp') is-invalid @enderror" name="otp" required
                                        autocomplete="off" autofocus>

                                    @error('otp')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Verify') }}
                                    </button>

                                    <form class="d-inline" method="POST" action="{{ route('verification.otp.resend') }}">
                                        @csrf
                                        <button type="submit"
                                            class="btn btn-link p-0 m-0 align-baseline">{{ __('Resend Code') }}</button>
                                    </form>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

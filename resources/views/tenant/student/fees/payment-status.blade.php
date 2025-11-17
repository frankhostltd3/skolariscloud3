@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'Payment Status')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">
                <i class="bi bi-credit-card me-2"></i>{{ __('Payment Status') }}
            </h4>
            <p class="text-muted mb-0">{{ __('Transaction Details') }}</p>
        </div>
        <a href="{{ route('tenant.student.fees.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Fees') }}
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-5">
                    @if($payment->status === 'confirmed')
                        <!-- Success State -->
                        <div class="mb-4">
                            <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-success mb-3">{{ __('Payment Successful!') }}</h3>
                        <p class="text-muted mb-4">
                            {{ __('Your payment has been processed successfully.') }}
                        </p>
                    @elseif($payment->status === 'pending')
                        <!-- Pending State -->
                        <div class="mb-4">
                            <i class="bi bi-clock text-warning" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-warning mb-3">{{ __('Payment Processing') }}</h3>
                        <p class="text-muted mb-4">
                            {{ __('Your payment is being processed. Please wait for confirmation.') }}
                        </p>
                    @elseif($payment->status === 'failed')
                        <!-- Failed State -->
                        <div class="mb-4">
                            <i class="bi bi-x-circle text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-danger mb-3">{{ __('Payment Failed') }}</h3>
                        <p class="text-muted mb-4">
                            {{ __('Your payment could not be processed. Please try again or contact support.') }}
                        </p>
                    @else
                        <!-- Unknown State -->
                        <div class="mb-4">
                            <i class="bi bi-question-circle text-info" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="text-info mb-3">{{ __('Payment Status Unknown') }}</h3>
                        <p class="text-muted mb-4">
                            {{ __('We are unable to determine the status of your payment at this time.') }}
                        </p>
                    @endif

                    <!-- Payment Details -->
                    <div class="card border mb-4">
                        <div class="card-body">
                            <h5 class="card-title">{{ __('Payment Details') }}</h5>
                            <div class="row text-start">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>{{ __('Fee') }}:</strong><br>
                                        {{ $payment->fee->name }}
                                    </p>
                                    <p class="mb-2">
                                        <strong>{{ __('Amount') }}:</strong><br>
                                        {{ format_money($payment->amount) }}
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>{{ __('Reference') }}:</strong><br>
                                        <code>{{ $payment->reference }}</code>
                                    </p>
                                    <p class="mb-2">
                                        <strong>{{ __('Date') }}:</strong><br>
                                        {{ $payment->paid_at->format('F d, Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center gap-2">
                        <a href="{{ route('tenant.student.fees.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left me-2"></i>{{ __('Back to Fees') }}
                        </a>
                        <a href="{{ route('tenant.student.fees.show', optional($payment->invoice)->id ? optional($payment->invoice)->id : 0) }}" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-2"></i>{{ __('View Fee Details') }}
                        </a>
                        @if($payment->status === 'failed')
                            <form method="POST" action="{{ route('tenant.student.fees.pay', $payment->fee) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-arrow-repeat me-2"></i>{{ __('Try Again') }}
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="alert alert-info mt-4">
                <h6 class="alert-heading">
                    <i class="bi bi-info-circle me-2"></i>{{ __('Need Help?') }}
                </h6>
                <p class="mb-0">
                    {{ __('If you have any questions about your payment or need assistance, please contact the school administration or visit the school office.') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

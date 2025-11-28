@extends('tenant.layouts.app')

@section('content')
    @php
        $photoUrl = $student->profile_photo
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($student->profile_photo)
            : 'https://ui-avatars.com/api/?name=' . urlencode($student->name) . '&background=0D8ABC&color=fff';
    @endphp

    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('tenant.dashboard') }}">{{ __('Dashboard') }}</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('tenant.modules.students.index') }}">{{ __('Students') }}</a></li>
                        <li class="breadcrumb-item active">{{ $student->name }}</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold mb-0">{{ __('Student Profile') }}</h1>
            </div>
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="{{ route('tenant.modules.students.edit', $student) }}">
                    <i class="bi bi-pencil-square me-1"></i>{{ __('Edit') }}
                </a>
                <a class="btn btn-light" href="{{ route('tenant.modules.students.index') }}">
                    <i class="bi bi-list me-1"></i>{{ __('Back to list') }}
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-xl-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="row align-items-center g-4">
                            <div class="col-md-3 text-center">
                                <img src="{{ $photoUrl }}" alt="{{ $student->name }}" class="rounded-circle shadow-sm"
                                    width="140" height="140">
                                <div class="mt-2">
                                    <span
                                        class="badge bg-{{ $student->status === 'active' ? 'success' : 'secondary' }} text-uppercase">{{ __($student->status) }}</span>
                                </div>
                            </div>
                            <div class="col-md-9">
                                <h2 class="h4 mb-1">{{ $student->name }}</h2>
                                <p class="text-muted mb-3">{{ __('Admission No.') }}: {{ $student->admission_no }}</p>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="small text-muted">{{ __('Class') }}</div>
                                        <div class="fw-semibold">{{ optional($student->class)->name ?? __('Not set') }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="small text-muted">{{ __('Stream') }}</div>
                                        <div class="fw-semibold">{{ optional($student->stream)->name ?? __('Not set') }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="small text-muted">{{ __('Gender') }}</div>
                                        <div class="fw-semibold text-capitalize">{{ $student->gender ?? __('Not set') }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="small text-muted">{{ __('Date of Birth') }}</div>
                                        <div class="fw-semibold">{{ $student->dob?->format('M d, Y') ?? __('Not set') }}
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="small text-muted">{{ __('Email') }}</div>
                                        <div class="fw-semibold">{{ $student->email ?? __('Not provided') }}</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="small text-muted">{{ __('Phone') }}</div>
                                        <div class="fw-semibold">{{ $student->phone ?? __('Not provided') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">{{ __('Contact & Guardian Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted small">{{ __('Address') }}</h6>
                                <p class="mb-2">{{ $student->address ?: __('No address on file') }}</p>
                                <p class="mb-0 text-muted">
                                    {{ collect([$student->city, $student->state, $student->postal_code])->filter()->join(', ') ?:__('City/State not provided') }}<br>
                                    {{ $student->country ?: __('Country not provided') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted small">{{ __('Emergency Contact') }}</h6>
                                <p class="mb-1 fw-semibold">{{ $student->emergency_contact_name ?: __('Not provided') }}
                                </p>
                                <p class="mb-0 text-muted">
                                    {{ $student->emergency_contact_phone ?: __('Phone not provided') }}<br>
                                    {{ $student->emergency_contact_relation ?: __('Relationship not provided') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted small">{{ __('Father / Guardian') }}</h6>
                                <p class="mb-1 fw-semibold">{{ $student->father_name ?: __('Not provided') }}</p>
                                <p class="mb-0 text-muted">{{ $student->father_phone ?: __('Phone not provided') }} |
                                    {{ $student->father_email ?: __('Email not provided') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted small">{{ __('Mother / Guardian') }}</h6>
                                <p class="mb-1 fw-semibold">{{ $student->mother_name ?: __('Not provided') }}</p>
                                <p class="mb-0 text-muted">{{ $student->mother_phone ?: __('Phone not provided') }} |
                                    {{ $student->mother_email ?: __('Email not provided') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">{{ __('Academic & Notes') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted small">{{ __('Medical') }}</h6>
                                <p class="mb-1"><strong>{{ __('Conditions') }}:</strong>
                                    {{ $student->medical_conditions ?: __('None reported') }}</p>
                                <p class="mb-1"><strong>{{ __('Allergies') }}:</strong>
                                    {{ $student->allergies ?: __('None reported') }}</p>
                                <p class="mb-0"><strong>{{ __('Medications') }}:</strong>
                                    {{ $student->medications ?: __('None reported') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-uppercase text-muted small">{{ __('Previous Schooling') }}</h6>
                                <p class="mb-1"><strong>{{ __('School') }}:</strong>
                                    {{ $student->previous_school ?: __('Not provided') }}</p>
                                <p class="mb-1"><strong>{{ __('Class') }}:</strong>
                                    {{ $student->previous_class ?: __('Not provided') }}</p>
                                <p class="mb-0"><strong>{{ __('Reason') }}:</strong>
                                    {{ $student->transfer_reason ?: __('Not provided') }}</p>
                            </div>
                            <div class="col-12">
                                <h6 class="text-uppercase text-muted small">{{ __('Additional Notes') }}</h6>
                                <p class="mb-0">{{ $student->notes ?: __('No notes recorded for this student yet.') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <form class="card shadow-sm" action="{{ route('tenant.modules.students.destroy', $student) }}"
                    method="post" onsubmit="return confirm('{{ __('Delete this student?') }}')">
                    @csrf
                    @method('DELETE')
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">{{ __('Danger Zone') }}</h5>
                            <p class="text-muted mb-0 small">{{ __('Deleting will remove this profile permanently.') }}
                            </p>
                        </div>
                        <button class="btn btn-danger">
                            <i class="bi bi-trash me-1"></i>{{ __('Delete Student') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="col-xl-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">{{ __('Account Status') }}</h5>
                        @if ($account)
                            <span
                                class="badge {{ $account->getApprovalBadgeClass() }}">{{ $account->getApprovalLabel() }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($account)
                            <dl class="row mb-0 small">
                                <dt class="col-5 text-uppercase text-muted">{{ __('Email') }}</dt>
                                <dd class="col-7">{{ $account->email }}</dd>
                                <dt class="col-5 text-uppercase text-muted">{{ __('User Type') }}</dt>
                                <dd class="col-7 text-capitalize">{{ $account->user_type?->value ?? __('Student') }}</dd>
                                <dt class="col-5 text-uppercase text-muted">{{ __('Roles') }}</dt>
                                <dd class="col-7">
                                    {{ $account->roles->pluck('name')->join(', ') ?: __('No roles assigned') }}</dd>
                                <dt class="col-5 text-uppercase text-muted">{{ __('Email Verified') }}</dt>
                                <dd class="col-7">
                                    @if ($account->email_verified_at)
                                        <span class="badge bg-success">{{ __('Verified') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ __('Pending') }}</span>
                                    @endif
                                </dd>
                                <dt class="col-5 text-uppercase text-muted">{{ __('2FA') }}</dt>
                                <dd class="col-7">
                                    @if ($account->two_factor_confirmed_at)
                                        <span class="badge bg-success">{{ __('Enabled') }}</span>
                                    @else
                                        <span class="badge bg-secondary">{{ __('Disabled') }}</span>
                                    @endif
                                </dd>
                                <dt class="col-5 text-uppercase text-muted">{{ __('Active') }}</dt>
                                <dd class="col-7">
                                    <span
                                        class="badge {{ $account->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $account->is_active ? __('Yes') : __('No') }}</span>
                                </dd>
                            </dl>
                        @else
                            <div class="alert alert-warning mb-0">
                                <i
                                    class="bi bi-exclamation-triangle me-2"></i>{{ __('No linked user account found. Student onboarding may be incomplete.') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="card-title mb-0">{{ __('Latest Enrollment') }}</h5>
                    </div>
                    <div class="card-body">
                        @if ($latestEnrollment)
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge {{ $latestEnrollment->status_badge_class }}">
                                    {{ $latestEnrollment->status_display }}
                                </span>
                                <span class="ms-2 text-muted small">
                                    {{ __('Enrolled on') }}
                                    {{ $latestEnrollment->enrollment_date?->format('M d, Y') ?? __('N/A') }}
                                </span>
                            </div>
                            <dl class="row mb-0 small">
                                <dt class="col-6 text-uppercase text-muted">{{ __('Class') }}</dt>
                                <dd class="col-6">{{ optional($latestEnrollment->class)->name ?? __('Not set') }}</dd>
                                <dt class="col-6 text-uppercase text-muted">{{ __('Stream') }}</dt>
                                <dd class="col-6">{{ optional($latestEnrollment->stream)->name ?? __('Not set') }}</dd>
                                <dt class="col-6 text-uppercase text-muted">{{ __('Academic Year') }}</dt>
                                <dd class="col-6">{{ optional($latestEnrollment->academicYear)->name ?? __('Not set') }}
                                </dd>
                                <dt class="col-6 text-uppercase text-muted">{{ __('Fees') }}</dt>
                                <dd class="col-6">
                                    {{ formatMoney($latestEnrollment->fees_paid ?? 0) }} /
                                    {{ formatMoney($latestEnrollment->fees_total ?? 0) }}
                                </dd>
                            </dl>
                        @else
                            <div class="alert alert-info mb-0">
                                <i
                                    class="bi bi-info-circle me-2"></i>{{ __('No enrollment records found for this student yet.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.admin._sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <h1 class="h4 fw-semibold mb-0">{{ __('User Registration Details') }}</h1>
    <a href="{{ route('admin.user-approvals.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> {{ __('Back to List') }}
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ __('User Information') }}</h5>
                <span class="badge {{ $user->getApprovalBadgeClass() }}">
                    {{ $user->getApprovalLabel() }}
                </span>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 30%;">{{ __('Name') }}</th>
                        <td>{{ $user->name }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Email') }}</th>
                        <td>{{ $user->email }}</td>
                    </tr>
                    <tr>
                        <th>{{ __('Role(s)') }}</th>
                        <td>
                            @foreach($user->getRoleNames() as $role)
                                <span class="badge bg-info">{{ $role }}</span>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('Registration Date') }}</th>
                        <td>
                            {{ $user->created_at->format('F d, Y \a\t g:i A') }}
                            <small class="text-muted">({{ $user->created_at->diffForHumans() }})</small>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ __('Approval Status') }}</th>
                        <td>
                            <span class="badge {{ $user->getApprovalBadgeClass() }}">
                                {{ $user->getApprovalLabel() }}
                            </span>
                        </td>
                    </tr>

                    @if($user->approved_by)
                        <tr>
                            <th>{{ __('Approved/Rejected By') }}</th>
                            <td>{{ $user->approver->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Decision Date') }}</th>
                            <td>{{ $user->approved_at?->format('F d, Y \a\t g:i A') }}</td>
                        </tr>
                    @endif

                    @if($user->rejection_reason)
                        <tr>
                            <th>{{ __('Rejection Reason') }}</th>
                            <td>
                                <div class="alert alert-danger mb-0">
                                    {{ $user->rejection_reason }}
                                </div>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>
        </div>

        @if($user->registration_data)
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Additional Registration Data') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        @if(isset($user->registration_data['student_id']))
                            <tr>
                                <th style="width: 30%;">{{ __('Student ID') }}</th>
                                <td>{{ $user->registration_data['student_id'] }}</td>
                            </tr>
                        @endif

                        @if(isset($user->registration_data['class']))
                            <tr>
                                <th>{{ __('Class') }}</th>
                                <td>{{ $user->registration_data['class'] }}</td>
                            </tr>
                        @endif

                        @if(isset($user->registration_data['employee_id']))
                            <tr>
                                <th>{{ __('Employee ID') }}</th>
                                <td>{{ $user->registration_data['employee_id'] }}</td>
                            </tr>
                        @endif

                        @if(isset($user->registration_data['department']))
                            <tr>
                                <th>{{ __('Department') }}</th>
                                <td>{{ $user->registration_data['department'] }}</td>
                            </tr>
                        @endif

                        @if(empty($user->registration_data))
                            <tr>
                                <td colspan="2" class="text-muted">{{ __('No additional data provided') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        @if($user->approval_status === 'pending')
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">{{ __('Actions') }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.user-approvals.approve', $user) }}" class="mb-3">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('{{ __('Approve this user registration?') }}')">
                            <i class="bi bi-check-circle"></i> {{ __('Approve Registration') }}
                        </button>
                    </form>

                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-circle"></i> {{ __('Reject Registration') }}
                    </button>
                </div>
            </div>
        @endif

        @if($user->approval_status === 'approved')
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('Manage Employment') }}</h5>
                </div>
                <div class="card-body">
                    <button class="btn btn-outline-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#employmentModal">
                        <i class="bi bi-briefcase"></i> {{ __('Edit Employment') }}
                    </button>
                    @if($user->is_active)
                        <form method="POST" action="{{ route('admin.user-approvals.suspend', $user) }}" class="mb-2">
                            @csrf
                            <input type="hidden" name="reason" value="{{ __('Suspended via approvals detail') }}">
                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('{{ __('Suspend this user?') }}')">
                                <i class="bi bi-pause-circle"></i> {{ __('Suspend') }}
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('admin.user-approvals.reinstate', $user) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('{{ __('Reinstate this user?') }}')">
                                <i class="bi bi-play-circle"></i> {{ __('Reinstate') }}
                            </button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.user-approvals.expel', $user) }}">
                        @csrf
                        <input type="hidden" name="reason" value="{{ __('Expelled/Terminated via approvals detail') }}">
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('{{ __('Expel/Terminate this user? This will clear teaching allocations.') }}')">
                            <i class="bi bi-slash-circle"></i> {{ __('Expel / Terminate') }}
                        </button>
                    </form>
                </div>
            </div>

            <!-- Employment Modal -->
            <div class="modal fade" id="employmentModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <form method="POST" action="{{ route('admin.user-approvals.employment', $user) }}">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title">{{ __('Edit Employment for') }} {{ $user->name }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">{{ __('Employment Role') }}</label>
                                        <select name="employment_role" class="form-select" required>
                                            @foreach(['Teacher','Bursar','Nurse','Staff','Other'] as $role)
                                                <option value="{{ $role }}" {{ $user->hasRole($role) ? 'selected' : '' }}>{{ __($role) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">{{ __('Employment Type') }}</label>
                                        <input type="text" name="employee_type" class="form-control" placeholder="{{ __('e.g., full_time, part_time, contract') }}">
                                    </div>
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            <i class="bi bi-info-circle"></i> {{ __('If role is Teacher, you can optionally assign class and subjects here.') }}
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">{{ __('Class (optional)') }}</label>
                                        <select name="class_id" class="form-select">
                                            <option value="">{{ __('— Select —') }}</option>
                                            @php($classes = $classes ?? (\App\Models\Academic\ClassRoom::orderBy('grade_level')->orderBy('name')->get(['id','name','code'])))
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }} {{ $class->code ? '(' . $class->code . ')' : '' }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">{{ __('Subjects (optional)') }}</label>
                                        @php($subjects = $subjects ?? (\App\Models\Subject::orderBy('name')->get(['id','name'])))
                                        <select name="subject_ids[]" class="form-select" multiple>
                                            @foreach($subjects as $subj)
                                                <option value="{{ $subj->id }}">{{ $subj->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">{{ __('Hold Ctrl/Command to select multiple') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">{{ __('Quick Info') }}</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-person"></i>
                        <strong>{{ __('User ID') }}:</strong> #{{ $user->id }}
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-calendar"></i>
                        <strong>{{ __('Account Age') }}:</strong> {{ $user->created_at->diffForHumans() }}
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-shield"></i>
                        <strong>{{ __('Email Verified') }}:</strong> 
                        @if($user->email_verified_at)
                            <span class="badge bg-success">{{ __('Yes') }}</span>
                        @else
                            <span class="badge bg-warning">{{ __('No') }}</span>
                        @endif
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
@if($user->approval_status === 'pending')
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.user-approvals.reject', $user) }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Reject Registration') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Please provide a reason for rejecting this registration:') }}</p>
                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label">{{ __('Rejection Reason') }}</label>
                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" required placeholder="{{ __('Enter the reason for rejection...') }}"></textarea>
                        </div>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i>
                            {{ __('The user will receive an email notification with this reason.') }}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> {{ __('Reject Registration') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

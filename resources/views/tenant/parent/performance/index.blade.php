@extends('layouts.tenant.parent')

@section('title', __('Performance'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ __('Academic Performance') }}</h4>
    </div>

    <div class="row g-4">
        @forelse ($students as $student)
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            @if ($student->profile_photo)
                                <img src="{{ $student->profile_photo }}" alt="{{ $student->name }}" class="rounded-circle me-2"
                                    width="40" height="40" style="object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center me-2"
                                    style="width: 40px; height: 40px;">
                                    <span class="fw-bold text-muted">{{ substr($student->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $student->name }}</h6>
                                <small class="text-muted">{{ $student->class->name ?? 'No Class' }}</small>
                            </div>
                        </div>
                        <a href="{{ route('tenant.parent.performance.show', $student->id) }}"
                            class="btn btn-sm btn-outline-primary">{{ __('View Details') }}</a>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted small text-uppercase fw-bold mb-3">{{ __('Recent Grades') }}</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Subject') }}</th>
                                        <th class="text-end">{{ __('Score') }}</th>
                                        <th class="text-end">{{ __('Grade') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $grades = $student->account ? $student->account->grades->take(5) : collect([]);
                                    @endphp
                                    @forelse ($grades as $grade)
                                        <tr>
                                            <td>{{ $grade->subject->name ?? 'Unknown' }}</td>
                                            <td class="text-end">{{ $grade->marks_obtained }}</td>
                                            <td class="text-end">
                                                <span
                                                    class="badge bg-{{ $grade->marks_obtained >= 50 ? 'success' : 'danger' }}">
                                                    {{ $grade->grade ?? '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted py-3">
                                                {{ __('No grades recorded yet') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>{{ __('No children linked to your account.') }}
                </div>
            </div>
        @endforelse
    </div>
@endsection

@extends('layouts.tenant.parent')

@section('title', __('Behaviour Records'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ __('Behaviour Records') }}</h4>
    </div>

    <div class="row g-4">
        @forelse ($students as $student)
            <div class="col-12">
                <div class="card border-0 shadow-sm">
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
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">{{ __('Date') }}</th>
                                        <th>{{ __('Type') }}</th>
                                        <th>{{ __('Description') }}</th>
                                        <th>{{ __('Points') }}</th>
                                        <th class="text-end pe-4">{{ __('Recorded By') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($student->behaviours as $record)
                                        <tr>
                                            <td class="ps-4">
                                                {{ $record->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $record->type === 'positive' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($record->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $record->title }}</td>
                                            <td>{{ $record->points }}</td>
                                            <td class="text-end pe-4">{{ $record->recorder->name ?? 'Unknown' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                {{ __('No behaviour records found') }}</td>
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

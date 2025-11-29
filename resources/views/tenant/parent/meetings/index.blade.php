@extends('layouts.tenant.parent')

@section('title', __('Parent-Teacher Meetings'))

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0">{{ __('Parent-Teacher Meetings') }}</h4>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#requestMeetingModal">
            <i class="bi bi-plus-lg me-2"></i>{{ __('Request Meeting') }}
        </button>
    </div>

    <div class="row g-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">{{ __('Date & Time') }}</th>
                                    <th>{{ __('Teacher') }}</th>
                                    <th>{{ __('Student') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th class="text-end pe-4">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($meetings as $meeting)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold">
                                                {{ \Carbon\Carbon::parse($meeting->scheduled_at)->format('M d, Y') }}</div>
                                            <small
                                                class="text-muted">{{ \Carbon\Carbon::parse($meeting->scheduled_at)->format('h:i A') }}</small>
                                        </td>
                                        <td>{{ $meeting->teacher->name ?? 'Unknown' }}</td>
                                        <td>{{ $meeting->student->name ?? 'Unknown' }}</td>
                                        <td>
                                            @if ($meeting->status == 'approved')
                                                <span class="badge bg-success">{{ __('Confirmed') }}</span>
                                            @elseif($meeting->status == 'pending')
                                                <span class="badge bg-warning">{{ __('Pending') }}</span>
                                            @elseif($meeting->status == 'rejected')
                                                <span class="badge bg-danger">{{ __('Declined') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($meeting->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            @if ($meeting->status == 'pending')
                                                <button class="btn btn-sm btn-outline-danger">{{ __('Cancel') }}</button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>
                                            {{ __('No meetings scheduled.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($meetings->hasPages())
                    <div class="card-footer bg-white py-3">
                        {{ $meetings->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Request Meeting Modal -->
    <div class="modal fade" id="requestMeetingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ __('Request Meeting') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">{{ __('Select Student') }}</label>
                            <select class="form-select" name="student_id" required>
                                <option value="">{{ __('Choose...') }}</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Preferred Date') }}</label>
                            <input type="date" class="form-control" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Reason') }}</label>
                            <textarea class="form-control" name="reason" rows="3" required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">{{ __('Submit Request') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

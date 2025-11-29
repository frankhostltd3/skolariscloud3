@extends('layouts.tenant.parent')

@section('title', $student->name . ' - ' . __('Performance'))

@section('content')
    <div class="mb-4">
        <a href="{{ route('tenant.parent.performance.index') }}" class="text-decoration-none text-muted small">
            <i class="bi bi-arrow-left me-1"></i>{{ __('Back to Performance') }}
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div class="d-flex align-items-center">
            @if (optional($student)->profile_photo)
                <img src="{{ $student->profile_photo }}" alt="{{ $student->name }}" class="rounded-circle me-3"
                    width="60" height="60" style="object-fit: cover;">
            @else
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center me-3"
                    style="width: 60px; height: 60px;">
                    <span class="fs-4 fw-bold text-muted">{{ substr($student->name ?? 'S', 0, 1) }}</span>
                </div>
            @endif
            <div>
                <h4 class="fw-bold mb-0">{{ $student->name ?? 'Student' }}</h4>
                <p class="text-muted mb-0">{{ optional($student->class)->name ?? 'No Class' }} @if (optional($student)->stream)
                        - {{ $student->stream->name }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex flex-wrap gap-2">
            <!-- Download PDF -->
            <a href="{{ route('tenant.parent.performance.download', $student->id) }}" class="btn btn-primary">
                <i class="bi bi-download me-1"></i>{{ __('Download PDF') }}
            </a>

            <!-- Print -->
            <button type="button" class="btn btn-outline-secondary" onclick="printReport()">
                <i class="bi bi-printer me-1"></i>{{ __('Print') }}
            </button>

            <!-- Email -->
            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#emailModal">
                <i class="bi bi-envelope me-1"></i>{{ __('Email') }}
            </button>

            <!-- WhatsApp Share -->
            <a href="https://wa.me/?text={{ urlencode(__('Check out :name\'s report card from :school', ['name' => $student->name, 'school' => $school->name ?? 'School']) . ' - ' . route('tenant.parent.performance.show', $student->id)) }}"
                target="_blank" class="btn btn-success">
                <i class="bi bi-whatsapp me-1"></i>{{ __('WhatsApp') }}
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    @php
        $grades = optional(optional($student)->account)->grades ?? collect([]);
        $totalMarks = $grades->sum('marks_obtained');
        $totalPossible = $grades->sum('total_marks') ?: $grades->count() * 100;
        $averagePercentage = $grades->count() > 0 ? round(($totalMarks / $totalPossible) * 100, 1) : 0;
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-primary mb-2"><i class="bi bi-book fs-2"></i></div>
                    <h3 class="fw-bold mb-0">{{ $grades->count() }}</h3>
                    <small class="text-muted">{{ __('Subjects') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-success mb-2"><i class="bi bi-check-circle fs-2"></i></div>
                    <h3 class="fw-bold mb-0">{{ $totalMarks }}</h3>
                    <small class="text-muted">{{ __('Total Marks') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="text-info mb-2"><i class="bi bi-percent fs-2"></i></div>
                    <h3 class="fw-bold mb-0">{{ $averagePercentage }}%</h3>
                    <small class="text-muted">{{ __('Average') }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <div class="mb-2">
                        @if ($averagePercentage >= 80)
                            <span class="badge bg-success fs-4 px-3 py-2">A</span>
                        @elseif($averagePercentage >= 60)
                            <span class="badge bg-primary fs-4 px-3 py-2">B</span>
                        @elseif($averagePercentage >= 40)
                            <span class="badge bg-warning fs-4 px-3 py-2">C</span>
                        @else
                            <span class="badge bg-danger fs-4 px-3 py-2">D</span>
                        @endif
                    </div>
                    <small class="text-muted">{{ __('Overall Grade') }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4" id="reportCard">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">{{ __('All Grades') }}</h5>
            <span class="badge bg-secondary">{{ __('Academic Year') }} {{ date('Y') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4" style="width: 5%;">#</th>
                            <th style="width: 30%;">{{ __('Subject') }}</th>
                            <th style="width: 20%;">{{ __('Assessment') }}</th>
                            <th style="width: 15%;">{{ __('Date') }}</th>
                            <th class="text-end" style="width: 15%;">{{ __('Score') }}</th>
                            <th class="text-end pe-4" style="width: 15%;">{{ __('Grade') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($grades as $index => $grade)
                            @php
                                $percentage =
                                    ($grade->total_marks ?? 100) > 0
                                        ? round(($grade->marks_obtained / ($grade->total_marks ?? 100)) * 100)
                                        : $grade->marks_obtained;
                            @endphp
                            <tr>
                                <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ optional($grade->subject)->name ?? 'Unknown' }}</td>
                                <td>
                                    <span
                                        class="badge bg-light text-dark">{{ $grade->assessment_type ?? 'General' }}</span>
                                </td>
                                <td>{{ $grade->assessment_date ? \Carbon\Carbon::parse($grade->assessment_date)->format('M d, Y') : '-' }}
                                </td>
                                <td class="text-end">
                                    <span class="fw-semibold">{{ $grade->marks_obtained }}</span>
                                    <span class="text-muted">/{{ $grade->total_marks ?? 100 }}</span>
                                </td>
                                <td class="text-end pe-4">
                                    @if ($percentage >= 80)
                                        <span class="badge bg-success">{{ $grade->grade_letter ?? 'A' }}</span>
                                    @elseif($percentage >= 60)
                                        <span class="badge bg-primary">{{ $grade->grade_letter ?? 'B' }}</span>
                                    @elseif($percentage >= 40)
                                        <span class="badge bg-warning text-dark">{{ $grade->grade_letter ?? 'C' }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ $grade->grade_letter ?? 'D' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                                    {{ __('No grades recorded yet') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if ($grades->count() > 0)
                        <tfoot class="bg-light">
                            <tr class="fw-bold">
                                <td class="ps-4" colspan="4">{{ __('Total') }}</td>
                                <td class="text-end">{{ $totalMarks }}/{{ $totalPossible }}</td>
                                <td class="text-end pe-4">{{ $averagePercentage }}%</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>

    <!-- Share Options Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-share me-2"></i>{{ __('Share Report') }}</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <a href="{{ route('tenant.parent.performance.download', $student->id) }}"
                        class="btn btn-outline-primary w-100 py-3">
                        <i class="bi bi-file-pdf fs-4 d-block mb-1"></i>
                        {{ __('Download PDF') }}
                    </a>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-secondary w-100 py-3" onclick="printReport()">
                        <i class="bi bi-printer fs-4 d-block mb-1"></i>
                        {{ __('Print Report') }}
                    </button>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-outline-info w-100 py-3" data-bs-toggle="modal"
                        data-bs-target="#emailModal">
                        <i class="bi bi-envelope fs-4 d-block mb-1"></i>
                        {{ __('Email Report') }}
                    </button>
                </div>
                <div class="col-md-3">
                    <a href="https://wa.me/?text={{ urlencode(__('Check out :name\'s report card from :school', ['name' => $student->name, 'school' => $school->name ?? 'School']) . ' - ' . route('tenant.parent.performance.show', $student->id)) }}"
                        target="_blank" class="btn btn-outline-success w-100 py-3">
                        <i class="bi bi-whatsapp fs-4 d-block mb-1"></i>
                        {{ __('Share on WhatsApp') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Modal -->
    <div class="modal fade" id="emailModal" tabindex="-1" aria-labelledby="emailModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('tenant.parent.performance.email', $student->id) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="emailModalLabel">
                            <i class="bi bi-envelope me-2"></i>{{ __('Email Report Card') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="{{ auth()->user()->email }}" required>
                            <small
                                class="text-muted">{{ __('The report card PDF will be sent to this email address.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>{{ __('Send Email') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function printReport() {
            // Open the PDF download URL in a new window for printing
            const printWindow = window.open('{{ route('tenant.parent.performance.download', $student->id) }}', '_blank');
        }
    </script>
@endpush

@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'Academic Progress & Reports')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h1 class="h3 fw-semibold mb-0">
                    <i class="bi bi-graph-up me-2"></i>{{ __('Academic Progress & Reports') }}
                </h1>
                @if($hasFullPayment && $currentTerm)
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#shareReportModal">
                            <i class="bi bi-share me-2"></i>{{ __('Share Report') }}
                        </button>
                        <a href="{{ route('tenant.student.academic.report.download', ['term_id' => $currentTerm->id]) }}" class="btn btn-success">
                            <i class="bi bi-download me-2"></i>{{ __('Download Report') }}
                        </a>
                    </div>
                @endif
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(!$student)
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ __('Student record not found. Please contact your administrator.') }}
                </div>
            @endif

            @if($student && !$hasFullPayment)
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>{{ __('Fee Payment Required') }}</strong><br>
                    {{ __('You must clear all outstanding fees to download or share your academic report. Please visit the Fees section to make a payment.') }}
                </div>
            @endif
        </div>
    </div>

    @if($student)
        <!-- Statistics Cards -->
        @if($statistics && $currentTerm)
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-primary bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-primary text-white rounded-circle p-3">
                                        <i class="bi bi-book fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">{{ __('Total Subjects') }}</h6>
                                    <h3 class="mb-0 fw-bold">{{ $statistics['current_term']['total_subjects'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-success bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-success text-white rounded-circle p-3">
                                        <i class="bi bi-percent fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">{{ __('Average Score') }}</h6>
                                    <h3 class="mb-0 fw-bold">{{ number_format($statistics['current_term']['average_percentage'], 1) }}%</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-info bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-info text-white rounded-circle p-3">
                                        <i class="bi bi-clipboard-check fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">{{ __('Assessments') }}</h6>
                                    <h3 class="mb-0 fw-bold">{{ $statistics['current_term']['total_assessments'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 shadow-sm bg-warning bg-opacity-10">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-warning text-white rounded-circle p-3">
                                        <i class="bi bi-trophy fs-4"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="text-muted mb-1">{{ __('Highest Score') }}</h6>
                                    <h3 class="mb-0 fw-bold">{{ number_format($statistics['current_term']['highest_score'], 0) }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Performance by Subject -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">{{ __('Performance by Subject') }}</h5>
                            @if($currentTerm)
                                <span class="badge bg-primary">{{ $currentTerm->name }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($grades->count() > 0)
                            @php
                                $gradesBySubject = $grades->groupBy('subject_id');
                            @endphp
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ __('Subject') }}</th>
                                            <th class="text-center">{{ __('Assessments') }}</th>
                                            <th class="text-center">{{ __('Average Score') }}</th>
                                            <th class="text-center">{{ __('Average %') }}</th>
                                            <th class="text-center">{{ __('Grade') }}</th>
                                            <th class="text-end">{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($gradesBySubject as $subjectId => $subjectGrades)
                                            @php
                                                $subject = $subjects->firstWhere('id', $subjectId);
                                                $avgScore = $subjectGrades->avg('marks_obtained');
                                                $avgTotal = $subjectGrades->avg('total_marks');
                                                $avgPercentage = $avgTotal > 0 ? ($avgScore / $avgTotal) * 100 : 0;
                                                $avgGradeLetter = $subjectGrades->first()->grade_letter ?? 'N/A';
                                            @endphp
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-book text-primary me-2"></i>
                                                        <div>
                                                            <strong>{{ $subject->name ?? 'Unknown' }}</strong>
                                                            @if($subject && $subject->code)
                                                                <br><small class="text-muted">{{ $subject->code }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">{{ $subjectGrades->count() }}</td>
                                                <td class="text-center">
                                                    <strong>{{ number_format($avgScore, 1) }}</strong>/{{ number_format($avgTotal, 0) }}
                                                </td>
                                                <td class="text-center">
                                                    <div class="progress" style="height: 25px;">
                                                        <div class="progress-bar bg-{{ $avgPercentage >= 70 ? 'success' : ($avgPercentage >= 50 ? 'warning' : 'danger') }}" 
                                                             role="progressbar" 
                                                             style="width: {{ $avgPercentage }}%">
                                                            <strong>{{ number_format($avgPercentage, 1) }}%</strong>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-{{ $avgPercentage >= 70 ? 'success' : ($avgPercentage >= 50 ? 'warning' : 'danger') }} fs-6">
                                                        {{ $avgGradeLetter }}
                                                    </span>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            data-bs-toggle="collapse" 
                                                            data-bs-target="#subject-{{ $subjectId }}">
                                                        <i class="bi bi-eye"></i> {{ __('View Details') }}
                                                    </button>
                                                </td>
                                            </tr>
                                            <tr class="collapse" id="subject-{{ $subjectId }}">
                                                <td colspan="6" class="bg-light">
                                                    <div class="p-3">
                                                        <h6 class="mb-3">{{ __('Assessment Details') }}</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered">
                                                                <thead>
                                                                    <tr>
                                                                        <th>{{ __('Assessment') }}</th>
                                                                        <th>{{ __('Date') }}</th>
                                                                        <th class="text-center">{{ __('Score') }}</th>
                                                                        <th class="text-center">{{ __('Grade') }}</th>
                                                                        <th>{{ __('Teacher') }}</th>
                                                                        <th>{{ __('Remarks') }}</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($subjectGrades as $grade)
                                                                        <tr>
                                                                            <td>
                                                                                <strong>{{ $grade->assessment_name }}</strong>
                                                                                <br><small class="text-muted">{{ $grade->assessment_type }}</small>
                                                                            </td>
                                                                            <td>{{ $grade->assessment_date ? $grade->assessment_date->format('M d, Y') : 'N/A' }}</td>
                                                                            <td class="text-center">
                                                                                <strong>{{ number_format($grade->marks_obtained, 1) }}</strong>/{{ number_format($grade->total_marks, 0) }}
                                                                            </td>
                                                                            <td class="text-center">
                                                                                <span class="badge bg-secondary">{{ $grade->grade_letter ?? 'N/A' }}</span>
                                                                            </td>
                                                                            <td>{{ $grade->teacher->name ?? 'N/A' }}</td>
                                                                            <td>{{ $grade->remarks ?? '-' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-clipboard-data text-muted" style="font-size: 4rem;"></i>
                                <p class="text-muted mt-3 mb-0">{{ __('No grades available yet.') }}</p>
                                <small class="text-muted">{{ __('Your grades will appear here once they are published by your teachers.') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Term Reports Section -->
        @if($terms->count() > 0)
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">{{ __('Download Previous Reports') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($terms as $term)
                                    @php
                                        $termGrades = $grades->where('semester_id', $term->id);
                                        $hasGrades = $termGrades->count() > 0;
                                    @endphp
                                    <div class="col-md-4">
                                        <div class="card h-100 border {{ $term->is_active ? 'border-primary' : '' }}">
                                            <div class="card-body">
                                                <div class="d-flex align-items-start justify-content-between mb-3">
                                                    <div>
                                                        <h6 class="fw-bold mb-1">{{ $term->name }}</h6>
                                                        <small class="text-muted">
                                                            {{ $term->start_date ? $term->start_date->format('M d, Y') : '' }} - 
                                                            {{ $term->end_date ? $term->end_date->format('M d, Y') : '' }}
                                                        </small>
                                                    </div>
                                                    @if($term->is_active)
                                                        <span class="badge bg-success">{{ __('Current') }}</span>
                                                    @endif
                                                </div>

                                                @if($hasGrades)
                                                    <div class="mb-3">
                                                        <div class="d-flex justify-content-between small mb-1">
                                                            <span>{{ __('Assessments:') }}</span>
                                                            <strong>{{ $termGrades->count() }}</strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between small">
                                                            <span>{{ __('Average:') }}</span>
                                                            <strong>{{ number_format($termGrades->avg('marks_obtained'), 1) }}</strong>
                                                        </div>
                                                    </div>

                                                    @if($hasFullPayment)
                                                        <a href="{{ route('tenant.student.academic.report.download', ['term_id' => $term->id]) }}" 
                                                           class="btn btn-primary btn-sm w-100">
                                                            <i class="bi bi-download me-2"></i>{{ __('Download Report') }}
                                                        </a>
                                                    @else
                                                        <button class="btn btn-secondary btn-sm w-100" disabled>
                                                            <i class="bi bi-lock me-2"></i>{{ __('Payment Required') }}
                                                        </button>
                                                    @endif
                                                @else
                                                    <p class="text-muted small mb-0">{{ __('No grades available for this term.') }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Overall Performance Summary -->
        @if($statistics)
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm border-info">
                        <div class="card-header bg-info bg-opacity-10">
                            <h6 class="mb-0">
                                <i class="bi bi-info-circle text-info me-2"></i>
                                {{ __('Overall Performance Summary') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Total Subjects Studied:') }}</span>
                                    <strong>{{ $statistics['overall']['total_subjects'] }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Total Assessments Taken:') }}</span>
                                    <strong>{{ $statistics['overall']['total_assessments'] }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Overall Average Score:') }}</span>
                                    <strong>{{ number_format($statistics['overall']['average_score'], 1) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>{{ __('Overall Average Percentage:') }}</span>
                                    <strong class="text-{{ $statistics['overall']['average_percentage'] >= 70 ? 'success' : ($statistics['overall']['average_percentage'] >= 50 ? 'warning' : 'danger') }}">
                                        {{ number_format($statistics['overall']['average_percentage'], 1) }}%
                                    </strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm border-success">
                        <div class="card-header bg-success bg-opacity-10">
                            <h6 class="mb-0">
                                <i class="bi bi-lightbulb text-success me-2"></i>
                                {{ __('Tips for Academic Success') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="small mb-0">
                                <li class="mb-2">{{ __('Review your grades regularly and identify areas for improvement') }}</li>
                                <li class="mb-2">{{ __('Download your reports to track your progress over time') }}</li>
                                <li class="mb-2">{{ __('Share your reports with parents or guardians') }}</li>
                                <li class="mb-2">{{ __('Consult with teachers about subjects where you need help') }}</li>
                                <li>{{ __('Keep all your fees paid to access report downloads anytime') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif
</div>

<!-- Share Report Modal -->
@if($hasFullPayment && $currentTerm)
<div class="modal fade" id="shareReportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Share Academic Report') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="shareReportForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('Select Term') }}</label>
                        <select name="term_id" class="form-select" required>
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}" {{ $term->is_active ? 'selected' : '' }}>
                                    {{ $term->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Recipient Email') }}</label>
                        <input type="email" name="email" class="form-control" placeholder="parent@example.com" required>
                        <small class="text-muted">{{ __('Enter the email address where you want to send the report') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-2"></i>{{ __('Share Report') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
document.getElementById('shareReportForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
    
    try {
        const response = await fetch('{{ route("tenant.student.academic.report.share") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': formData.get('_token'),
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                term_id: formData.get('term_id'),
                email: formData.get('email')
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            bootstrap.Modal.getInstance(document.getElementById('shareReportModal')).hide();
            this.reset();
        } else {
            alert(data.error || 'An error occurred');
        }
    } catch (error) {
        alert('Failed to share report. Please try again.');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});
</script>
@endpush
@endsection

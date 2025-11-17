@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'Online Classes')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h1 class="h3 fw-semibold mb-0">
                    <i class="bi bi-camera-video me-2"></i>{{ __('My Online Classes') }}
                </h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('tenant.student.online-classes.recordings') }}" class="btn btn-outline-primary">
                        <i class="bi bi-play-circle me-2"></i>{{ __('View Recordings') }}
                    </a>
                </div>
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

            @if(!$student || !$student->class_id)
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ __('You have not been assigned to a class yet. Please contact your administrator.') }}
                </div>
            @endif
        </div>
    </div>

    @if($student && $student->class_id)
        <!-- Filters -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Subject') }}</label>
                                <select name="subject_id" class="form-select">
                                    <option value="">{{ __('All Subjects') }}</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ ($filters['subject_id'] ?? '') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Platform') }}</label>
                                <select name="platform" class="form-select">
                                    <option value="">{{ __('All Platforms') }}</option>
                                    <option value="zoom" {{ ($filters['platform'] ?? '') == 'zoom' ? 'selected' : '' }}>Zoom</option>
                                    <option value="google_meet" {{ ($filters['platform'] ?? '') == 'google_meet' ? 'selected' : '' }}>Google Meet</option>
                                    <option value="microsoft_teams" {{ ($filters['platform'] ?? '') == 'microsoft_teams' ? 'selected' : '' }}>Microsoft Teams</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Date') }}</label>
                                <input type="date" name="date" class="form-control" value="{{ $filters['date'] ?? '' }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-funnel me-2"></i>{{ __('Filter') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Online Classes List -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('Upcoming Classes') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($classes->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($classes as $class)
                                    <div class="list-group-item">
                                        <div class="row align-items-center">
                                            <div class="col-md-7">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0 me-3">
                                                        @if($class->platform === 'zoom')
                                                            <div class="bg-primary bg-opacity-10 text-primary p-3 rounded">
                                                                <i class="bi bi-camera-video fs-4"></i>
                                                            </div>
                                                        @elseif($class->platform === 'google_meet')
                                                            <div class="bg-success bg-opacity-10 text-success p-3 rounded">
                                                                <i class="bi bi-google fs-4"></i>
                                                            </div>
                                                        @elseif($class->platform === 'microsoft_teams')
                                                            <div class="bg-info bg-opacity-10 text-info p-3 rounded">
                                                                <i class="bi bi-microsoft-teams fs-4"></i>
                                                            </div>
                                                        @else
                                                            <div class="bg-secondary bg-opacity-10 text-secondary p-3 rounded">
                                                                <i class="bi bi-link-45deg fs-4"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 fw-bold">{{ $class->title }}</h6>
                                                        <div class="small text-muted mb-2">
                                                            @if($class->subject)
                                                                <span class="me-3">
                                                                    <i class="bi bi-book me-1"></i>{{ $class->subject->name }}
                                                                </span>
                                                            @endif
                                                            @if($class->teacher)
                                                                <span class="me-3">
                                                                    <i class="bi bi-person me-1"></i>{{ $class->teacher->name }}
                                                                </span>
                                                            @endif
                                                            <span class="badge bg-{{ $class->status === 'live' ? 'danger' : 'primary' }}">
                                                                @if($class->status === 'live')
                                                                    <i class="bi bi-record-circle me-1"></i>{{ __('LIVE NOW') }}
                                                                @else
                                                                    {{ ucfirst($class->status) }}
                                                                @endif
                                                            </span>
                                                        </div>
                                                        @if($class->description)
                                                            <p class="mb-0 small">{{ Str::limit($class->description, 100) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3 text-md-center">
                                                <div class="mb-1">
                                                    <i class="bi bi-calendar3 me-1"></i>
                                                    <strong>{{ $class->scheduled_at->format('M d, Y') }}</strong>
                                                </div>
                                                <div class="text-primary">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $class->scheduled_at->format('g:i A') }}
                                                </div>
                                                <div class="small text-muted">
                                                    ({{ $class->duration_formatted }})
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-md-end mt-3 mt-md-0">
                                                @if($class->can_join)
                                                    <a href="{{ route('tenant.student.online-classes.join', $class->id) }}" class="btn btn-success btn-sm w-100">
                                                        <i class="bi bi-box-arrow-up-right me-1"></i>{{ __('Join Now') }}
                                                    </a>
                                                @elseif($class->is_live)
                                                    <span class="badge bg-danger">{{ __('Live Now') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Not Yet') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $classes->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-camera-video-off text-muted" style="font-size: 4rem;"></i>
                                <p class="text-muted mt-3 mb-0">{{ __('No upcoming online classes at the moment.') }}</p>
                                <small class="text-muted">{{ __('Check back later or contact your teacher.') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Cards -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-primary">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            {{ __('How to Join') }}
                        </h6>
                        <ul class="small mb-0">
                            <li>{{ __('Classes can be joined 15 minutes before the scheduled time') }}</li>
                            <li>{{ __('Click "Join Now" when the button becomes active') }}</li>
                            <li>{{ __('You will be redirected to the meeting platform') }}</li>
                            <li>{{ __('Make sure you have a stable internet connection') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-lightbulb text-success me-2"></i>
                            {{ __('Tips for Online Learning') }}
                        </h6>
                        <ul class="small mb-0">
                            <li>{{ __('Find a quiet place with good lighting') }}</li>
                            <li>{{ __('Test your camera and microphone before joining') }}</li>
                            <li>{{ __('Keep your video on during class') }}</li>
                            <li>{{ __('Use the chat feature to ask questions') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
// Auto-refresh page every 2 minutes to update class status
setTimeout(function() {
    location.reload();
}, 120000);
</script>
@endpush
@endsection


@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.student._sidebar')
@endsection

@section('title', 'Class Recordings')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h1 class="h3 fw-semibold mb-0">
                    <i class="bi bi-play-circle me-2"></i>{{ __('Class Recordings') }}
                </h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('tenant.student.online-classes.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-camera-video me-2"></i>{{ __('Live Classes') }}
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
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
                            <div class="col-md-5">
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
                            <div class="col-md-2">
                                <label class="form-label">{{ __('Month') }}</label>
                                <input type="month" name="month" class="form-control" value="{{ $filters['month'] ?? '' }}">
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

        <!-- Recordings List -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">{{ __('Recorded Classes') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($classes->count() > 0)
                            <div class="row g-4">
                                @foreach($classes as $class)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body">
                                                <!-- Platform Badge -->
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    @if($class->platform === 'zoom')
                                                        <span class="badge bg-primary">
                                                            <i class="bi bi-camera-video me-1"></i>Zoom
                                                        </span>
                                                    @elseif($class->platform === 'google_meet')
                                                        <span class="badge bg-success">
                                                            <i class="bi bi-google me-1"></i>Google Meet
                                                        </span>
                                                    @elseif($class->platform === 'microsoft_teams')
                                                        <span class="badge bg-info">
                                                            <i class="bi bi-microsoft-teams me-1"></i>Teams
                                                        </span>
                                                    @else
                                                        <span class="badge bg-secondary">
                                                            <i class="bi bi-link-45deg me-1"></i>{{ ucfirst(str_replace('_', ' ', $class->platform)) }}
                                                        </span>
                                                    @endif
                                                    
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>{{ __('Completed') }}
                                                    </span>
                                                </div>

                                                <!-- Class Info -->
                                                <h6 class="card-title fw-bold mb-2">{{ $class->title }}</h6>
                                                
                                                @if($class->subject)
                                                    <p class="text-muted small mb-2">
                                                        <i class="bi bi-book me-1"></i>{{ $class->subject->name }}
                                                    </p>
                                                @endif

                                                @if($class->teacher)
                                                    <p class="text-muted small mb-2">
                                                        <i class="bi bi-person me-1"></i>{{ $class->teacher->name }}
                                                    </p>
                                                @endif

                                                @if($class->description)
                                                    <p class="small text-muted mb-3">{{ Str::limit($class->description, 80) }}</p>
                                                @endif

                                                <!-- Date & Duration -->
                                                <div class="d-flex justify-content-between text-muted small mb-3">
                                                    <span>
                                                        <i class="bi bi-calendar3 me-1"></i>
                                                        {{ $class->scheduled_at->format('M d, Y') }}
                                                    </span>
                                                    <span>
                                                        <i class="bi bi-clock me-1"></i>
                                                        {{ $class->duration_formatted }}
                                                    </span>
                                                </div>

                                                <!-- Watch Button -->
                                                <a href="{{ route('tenant.student.online-classes.recording', $class->id) }}" 
                                                   class="btn btn-primary w-100" 
                                                   target="_blank">
                                                    <i class="bi bi-play-fill me-2"></i>{{ __('Watch Recording') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $classes->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-film text-muted" style="font-size: 4rem;"></i>
                                <p class="text-muted mt-3 mb-0">{{ __('No recorded classes available yet.') }}</p>
                                <small class="text-muted">{{ __('Recordings will appear here after classes are completed.') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Info Card -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card border-info">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-info-circle text-info me-2"></i>
                            {{ __('About Recordings') }}
                        </h6>
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="small mb-0">
                                    <li>{{ __('Recordings are available after the class has ended') }}</li>
                                    <li>{{ __('You can watch recordings multiple times') }}</li>
                                    <li>{{ __('Some recordings may expire after a certain period') }}</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="small mb-0">
                                    <li>{{ __('Use recordings to review missed classes') }}</li>
                                    <li>{{ __('Take notes while watching for better retention') }}</li>
                                    <li>{{ __('Contact your teacher if a recording is unavailable') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection


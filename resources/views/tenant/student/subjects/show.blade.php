@extends('tenant.student.layouts.app')

@section('title', $subject->name . ' - Subject Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">{{ $subject->name }}</h1>
                    <p class="text-muted mb-0">{{ $subject->description ?? 'Subject description not available' }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('tenant.student.subjects.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Subjects
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <div class="progress-circle mb-2" data-progress="{{ $stats['progress'] }}">
                                    <div class="progress-circle-inner">
                                        <span class="progress-text">{{ $stats['progress'] }}%</span>
                                    </div>
                                </div>
                                <h6 class="text-muted">Overall Progress</h6>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row g-3">
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <div class="stat-icon">
                                            <i class="bi bi-file-earmark-text text-info"></i>
                                        </div>
                                        <div class="stat-number">{{ $stats['materials_viewed'] }}/{{ $stats['total_materials'] }}</div>
                                        <div class="stat-label">Materials</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <div class="stat-icon">
                                            <i class="bi bi-clipboard-check text-warning"></i>
                                        </div>
                                        <div class="stat-number">{{ $stats['completed_assignments'] }}/{{ $stats['total_assignments'] }}</div>
                                        <div class="stat-label">Assignments</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <div class="stat-icon">
                                            <i class="bi bi-question-circle text-success"></i>
                                        </div>
                                        <div class="stat-number">{{ $stats['completed_quizzes'] }}/{{ $stats['total_quizzes'] }}</div>
                                        <div class="stat-label">Quizzes</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <div class="stat-icon">
                                            <i class="bi bi-exclamation-triangle text-danger"></i>
                                        </div>
                                        <div class="stat-number">{{ $stats['overdue_assignments'] }}</div>
                                        <div class="stat-label">Overdue</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <div class="stat-icon">
                                            <i class="bi bi-clock text-primary"></i>
                                        </div>
                                        <div class="stat-number">{{ $stats['pending_assignments'] }}</div>
                                        <div class="stat-label">Pending</div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="text-center">
                                        <div class="stat-icon">
                                            <i class="bi bi-sticky text-secondary"></i>
                                        </div>
                                        <div class="stat-number">{{ $stats['notes_count'] }}</div>
                                        <div class="stat-label">Notes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Learning Materials -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-file-earmark-text text-info me-2"></i>Learning Materials</h5>
                    <span class="badge bg-info">{{ $materials->count() }} items</span>
                </div>
                <div class="card-body">
                    @if($materials->count() > 0)
                        <div class="row g-3">
                            @foreach($materials as $material)
                                <div class="col-md-6">
                                    <div class="material-card">
                                        <div class="d-flex align-items-start">
                                            <div class="material-icon me-3">
                                                @if($material->type === 'video')
                                                    <i class="bi bi-play-circle text-danger"></i>
                                                @elseif($material->type === 'document')
                                                    <i class="bi bi-file-earmark-text text-primary"></i>
                                                @elseif($material->type === 'presentation')
                                                    <i class="bi bi-easel text-warning"></i>
                                                @else
                                                    <i class="bi bi-file-earmark text-secondary"></i>
                                                @endif
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $material->title }}</h6>
                                                <p class="text-muted small mb-2">{{ Str::limit($material->description, 100) }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="bi bi-person"></i> {{ $material->teacher->name ?? 'Teacher' }}
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar"></i> {{ $material->created_at->format('M d') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <a href="#" class="btn btn-sm btn-outline-primary w-100">
                                                <i class="bi bi-eye"></i> View Material
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-file-earmark-text display-4 text-muted mb-3"></i>
                            <h6 class="text-muted">No learning materials available</h6>
                            <p class="text-muted small">Materials will be added by your teacher soon.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assignments -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check text-warning me-2"></i>Assignments</h5>
                    <span class="badge bg-warning">{{ $assignments->count() }} items</span>
                </div>
                <div class="card-body">
                    @if($assignments->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($assignments as $assignment)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $assignment->title }}</h6>
                                            <p class="text-muted small mb-2">{{ Str::limit($assignment->description, 120) }}</p>
                                            <div class="d-flex gap-3 small text-muted">
                                                <span><i class="bi bi-person"></i> {{ $assignment->teacher->name ?? 'Teacher' }}</span>
                                                <span><i class="bi bi-calendar"></i> Due: {{ $assignment->due_date->format('M d, Y') }}</span>
                                                <span><i class="bi bi-trophy"></i> {{ $assignment->points ?? 0 }} points</span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            @if($assignment->submissions->isNotEmpty())
                                                @if($assignment->submissions->first()->status === 'graded')
                                                    <span class="badge bg-success">Graded</span>
                                                    <div class="small text-muted mt-1">{{ $assignment->submissions->first()->grade }}/{{ $assignment->points ?? 0 }}</div>
                                                @else
                                                    <span class="badge bg-info">Submitted</span>
                                                @endif
                                            @else
                                                @if($assignment->due_date < now())
                                                    <span class="badge bg-danger">Overdue</span>
                                                @else
                                                    <span class="badge bg-secondary">Not Submitted</span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="#" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-clipboard-check display-4 text-muted mb-3"></i>
                            <h6 class="text-muted">No assignments available</h6>
                            <p class="text-muted small">Assignments will be posted by your teacher.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quizzes -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-question-circle text-success me-2"></i>Quizzes</h5>
                    <span class="badge bg-success">{{ $quizzes->count() }} items</span>
                </div>
                <div class="card-body">
                    @if($quizzes->count() > 0)
                        <div class="row g-3">
                            @foreach($quizzes as $quiz)
                                <div class="col-md-6">
                                    <div class="quiz-card">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-1">{{ $quiz->title }}</h6>
                                            @if($quiz->attempts->isNotEmpty())
                                                <span class="badge bg-success">Completed</span>
                                            @else
                                                <span class="badge bg-secondary">Not Attempted</span>
                                            @endif
                                        </div>
                                        <p class="text-muted small mb-3">{{ Str::limit($quiz->description, 100) }}</p>
                                        <div class="d-flex justify-content-between align-items-center small text-muted mb-3">
                                            <span><i class="bi bi-person"></i> {{ $quiz->teacher->name ?? 'Teacher' }}</span>
                                            <span><i class="bi bi-clock"></i> {{ $quiz->duration ?? 30 }} min</span>
                                        </div>
                                        @if($quiz->attempts->isNotEmpty())
                                            <div class="quiz-result mb-3">
                                                <small class="text-muted">Last attempt:</small>
                                                <div class="fw-bold">{{ $quiz->attempts->first()->score }}/{{ $quiz->total_marks }} points</div>
                                            </div>
                                        @endif
                                        <a href="#" class="btn btn-sm btn-outline-success w-100">
                                            @if($quiz->attempts->isNotEmpty())
                                                <i class="bi bi-arrow-repeat"></i> Retake Quiz
                                            @else
                                                <i class="bi bi-play-circle"></i> Start Quiz
                                            @endif
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-question-circle display-4 text-muted mb-3"></i>
                            <h6 class="text-muted">No quizzes available</h6>
                            <p class="text-muted small">Quizzes will be added by your teacher.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Upcoming Deadlines -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-calendar-event text-danger me-2"></i>Upcoming Deadlines</h5>
                </div>
                <div class="card-body">
                    @if($upcomingDeadlines->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($upcomingDeadlines as $assignment)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 small">{{ Str::limit($assignment->title, 30) }}</h6>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar"></i> {{ $assignment->due_date->format('M d, Y') }}
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            @if($assignment->due_date->isToday())
                                                <span class="badge bg-danger">Today</span>
                                            @elseif($assignment->due_date->isTomorrow())
                                                <span class="badge bg-warning">Tomorrow</span>
                                            @else
                                                <span class="badge bg-info">{{ $assignment->due_date->diffInDays(now()) }} days</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-calendar-check text-muted mb-2"></i>
                            <small class="text-muted">No upcoming deadlines</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-activity text-primary me-2"></i>Recent Activity</h5>
                </div>
                <div class="card-body">
                    @if($recentActivity->count() > 0)
                        <div class="activity-timeline">
                            @foreach($recentActivity as $activity)
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="bi {{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-title small">{{ $activity['title'] }}</div>
                                        <div class="activity-date small text-muted">{{ $activity['date']->format('M d, H:i') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-graph-up text-muted mb-2"></i>
                            <small class="text-muted">No recent activity</small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-lightning text-warning me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="openStudyTimer()">
                            <i class="bi bi-stopwatch"></i> Study Timer
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="openFlashcards()">
                            <i class="bi bi-card-text"></i> Flashcards
                        </button>
                        <button class="btn btn-outline-secondary btn-sm" onclick="openNotes()">
                            <i class="bi bi-sticky"></i> My Notes
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="openQuickQuiz()">
                            <i class="bi bi-question-circle"></i> Quick Quiz
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Study Timer Modal -->
<div class="modal fade" id="studyTimerModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-stopwatch text-primary me-2"></i>Study Timer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div class="timer-display mb-4">
                    <div class="timer-circle">
                        <span id="timerDisplay">25:00</span>
                    </div>
                </div>
                <div class="timer-controls">
                    <button id="startTimer" class="btn btn-success me-2">Start</button>
                    <button id="pauseTimer" class="btn btn-warning me-2" disabled>Pause</button>
                    <button id="resetTimer" class="btn btn-secondary">Reset</button>
                </div>
                <div class="mt-3">
                    <small class="text-muted">Use the Pomodoro technique: 25 minutes study, 5 minutes break</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.progress-circle {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 0 auto;
    background: conic-gradient(
        #0d6efd var(--progress, 0%),
        #e9ecef var(--progress, 0%)
    );
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.progress-circle-inner {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 18px;
    font-weight: bold;
    color: #6c757d;
    width: 60px;
    height: 60px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-icon {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.stat-number {
    font-size: 1.25rem;
    font-weight: bold;
    color: #495057;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
}

.material-card, .quiz-card {
    border: 1px solid #e9ecef;
    border-radius: 0.5rem;
    padding: 1rem;
    transition: all 0.3s ease;
}

.material-card:hover, .quiz-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 123, 255, 0.25);
}

.material-icon {
    font-size: 1.5rem;
    color: #6c757d;
}

.activity-timeline {
    position: relative;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1rem;
    padding-left: 2rem;
    position: relative;
}

.activity-item:before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 1.5rem;
    bottom: -1rem;
    width: 2px;
    background: #e9ecef;
}

.activity-item:last-child:before {
    display: none;
}

.activity-icon {
    position: absolute;
    left: 0;
    top: 0;
    width: 1.5rem;
    height: 1.5rem;
    border-radius: 50%;
    background: white;
    border: 2px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}

.activity-content {
    flex: 1;
}

.timer-circle {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    border: 4px solid #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 2rem;
    font-weight: bold;
    color: #495057;
}
</style>

<script>
// Progress circles
document.addEventListener('DOMContentLoaded', function() {
    const progressCircles = document.querySelectorAll('.progress-circle');
    progressCircles.forEach(circle => {
        const progress = circle.dataset.progress;
        circle.style.setProperty('--progress', progress + '%');
    });
});

// Study Timer functionality
let timerInterval;
let timeLeft = 25 * 60; // 25 minutes in seconds
let isRunning = false;

function openStudyTimer() {
    const modal = new bootstrap.Modal(document.getElementById('studyTimerModal'));
    modal.show();
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    document.getElementById('timerDisplay').textContent =
        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

function startTimer() {
    if (!isRunning) {
        isRunning = true;
        document.getElementById('startTimer').disabled = true;
        document.getElementById('pauseTimer').disabled = false;

        timerInterval = setInterval(() => {
            timeLeft--;
            updateTimerDisplay();

            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                alert('Study session complete! Take a 5-minute break.');
                resetTimer();
            }
        }, 1000);
    }
}

function pauseTimer() {
    isRunning = false;
    clearInterval(timerInterval);
    document.getElementById('startTimer').disabled = false;
    document.getElementById('pauseTimer').disabled = true;
}

function resetTimer() {
    isRunning = false;
    clearInterval(timerInterval);
    timeLeft = 25 * 60;
    updateTimerDisplay();
    document.getElementById('startTimer').disabled = false;
    document.getElementById('pauseTimer').disabled = true;
}

// Event listeners
document.getElementById('startTimer').addEventListener('click', startTimer);
document.getElementById('pauseTimer').addEventListener('click', pauseTimer);
document.getElementById('resetTimer').addEventListener('click', resetTimer);

// Initialize timer display
updateTimerDisplay();

// Placeholder functions for other quick actions
function openFlashcards() {
    alert('Flashcards feature coming soon!');
}

function openNotes() {
    alert('Notes feature coming soon!');
}

function openQuickQuiz() {
    alert('Quick Quiz feature coming soon!');
}
</script>
@endsection
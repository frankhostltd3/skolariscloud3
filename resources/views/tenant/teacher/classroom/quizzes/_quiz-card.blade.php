<div class="col-md-6 col-lg-4">
    <div class="card h-100">
        <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
            @php
                $statusClass = match ($quiz->status) {
                    'published' => 'success',
                    'draft' => 'warning',
                    'archived' => 'secondary',
                    default => 'secondary',
                };
                $isExpired = $quiz->available_until && $quiz->available_until < now();
            @endphp
            <div>
                <span class="badge bg-{{ $statusClass }}">
                    {{ ucfirst($quiz->status) }}
                </span>
                @if ($quiz->status === 'published' && $isExpired)
                    <span class="badge bg-danger ms-1">Expired</span>
                @elseif ($quiz->status === 'published' && $quiz->available_from && $quiz->available_from > now())
                    <span class="badge bg-info ms-1">Scheduled</span>
                @endif
            </div>
            <div class="dropdown">
                <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('tenant.teacher.classroom.quizzes.show', $quiz->id) }}">
                            <i class="bi bi-eye me-2"></i>View Details
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('tenant.teacher.classroom.quizzes.edit', $quiz->id) }}">
                            <i class="bi bi-pencil me-2"></i>Edit Quiz
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form action="{{ route('tenant.teacher.classroom.quizzes.destroy', $quiz->id) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this quiz?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-trash me-2"></i>Delete
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <h5 class="card-title text-truncate" title="{{ $quiz->title }}">{{ $quiz->title }}</h5>
            <p class="card-text text-muted small mb-3">
                {{ Str::limit(strip_tags($quiz->description), 100) }}
            </p>
            <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
                <span><i class="bi bi-book me-1"></i> {{ $quiz->subject->name ?? 'N/A' }}</span>
                <span><i class="bi bi-people me-1"></i> {{ $quiz->class->name ?? 'N/A' }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center small text-muted mb-2">
                <span><i class="bi bi-clock me-1"></i> {{ $quiz->duration_minutes ?? 0 }} mins</span>
                <span><i class="bi bi-question-square me-1"></i> {{ $quiz->questions_count ?? 0 }} Qs</span>
            </div>
            @if ($quiz->available_from || $quiz->available_until)
                <div class="small text-muted border-top pt-2 mt-2">
                    @if ($quiz->available_from)
                        <div><i class="bi bi-calendar-event me-1"></i> From:
                            {{ $quiz->available_from->format('M d, Y H:i') }}</div>
                    @endif
                    @if ($quiz->available_until)
                        <div><i class="bi bi-calendar-x me-1"></i> Until:
                            {{ $quiz->available_until->format('M d, Y H:i') }}</div>
                    @endif
                </div>
            @endif
        </div>
        <div class="card-footer bg-transparent border-top-0">
            <a href="{{ route('tenant.teacher.classroom.quizzes.show', $quiz->id) }}"
                class="btn btn-outline-primary w-100">
                <i class="bi bi-gear me-1"></i> Manage Questions
            </a>
        </div>
    </div>
</div>

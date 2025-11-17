@php
    $studentMenuUrls = [
        'dashboard' => \Illuminate\Support\Facades\Route::has('tenant.student.dashboard') ? route('tenant.student.dashboard') : null,
        'classroom' => \Illuminate\Support\Facades\Route::has('tenant.student.classroom.index') ? route('tenant.student.classroom.index') : null,
        'assignments' => \Illuminate\Support\Facades\Route::has('tenant.student.assignments.index') ? route('tenant.student.assignments.index') : null,
        'exams' => \Illuminate\Support\Facades\Route::has('tenant.student.exams.index') ? route('tenant.student.exams.index') : null,
        'attendance' => \Illuminate\Support\Facades\Route::has('tenant.student.attendance.index') ? route('tenant.student.attendance.index') : null,
        'grades' => \Illuminate\Support\Facades\Route::has('tenant.student.grades.index') ? route('tenant.student.grades.index') : null,
        'timetable' => \Illuminate\Support\Facades\Route::has('tenant.student.timetable.index') ? route('tenant.student.timetable.index') : null,
        'library' => \Illuminate\Support\Facades\Route::has('tenant.student.library.index') ? route('tenant.student.library.index') : null,
        'fees' => \Illuminate\Support\Facades\Route::has('tenant.student.fees.index') ? route('tenant.student.fees.index') : null,
        'messages' => \Illuminate\Support\Facades\Route::has('tenant.student.messages.index') ? route('tenant.student.messages.index') : null,
        'profile' => \Illuminate\Support\Facades\Route::has('tenant.student.profile') ? route('tenant.student.profile') : null,
    ];
@endphp

<div class="card shadow-sm">
    <div class="card-header fw-semibold">{{ __('Student Menu') }}</div>
    <div class="list-group list-group-flush">
          <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.dashboard') ? 'active' : '' }} {{ $studentMenuUrls['dashboard'] ? '' : 'disabled opacity-50' }}"
              href="{{ $studentMenuUrls['dashboard'] ?? '#' }}" @if (request()->routeIs('tenant.student.dashboard')) aria-current="page" @endif @unless($studentMenuUrls['dashboard']) aria-disabled="true" tabindex="-1" @endunless>
            <span class="bi bi-speedometer2 me-2"></span>{{ __('Dashboard') }}
        </a>

          <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.classroom*') ? 'active' : '' }} {{ $studentMenuUrls['classroom'] ? '' : 'disabled opacity-50' }}"
              href="{{ $studentMenuUrls['classroom'] ?? '#' }}" @unless($studentMenuUrls['classroom']) aria-disabled="true" tabindex="-1" @endunless>
            <span class="bi bi-book me-2"></span>{{ __('My Classroom') }}
        </a>

          <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.assignments*') ? 'active' : '' }} {{ $studentMenuUrls['assignments'] ? '' : 'disabled opacity-50' }}"
              href="{{ $studentMenuUrls['assignments'] ?? '#' }}" @unless($studentMenuUrls['assignments']) aria-disabled="true" tabindex="-1" @endunless>
            <span class="bi bi-journal-check me-2"></span>{{ __('Assignments') }}
        </a>

          <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.exams*') ? 'active' : '' }} {{ $studentMenuUrls['exams'] ? '' : 'disabled opacity-50' }}"
              href="{{ $studentMenuUrls['exams'] ?? '#' }}" @unless($studentMenuUrls['exams']) aria-disabled="true" tabindex="-1" @endunless>
            <span class="bi bi-pencil-square me-2"></span>{{ __('Exams & Quizzes') }}
        </a>

          <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.attendance*') ? 'active' : '' }} {{ $studentMenuUrls['attendance'] ? '' : 'disabled opacity-50' }}"
              href="{{ $studentMenuUrls['attendance'] ?? '#' }}" @unless($studentMenuUrls['attendance']) aria-disabled="true" tabindex="-1" @endunless>
            <span class="bi bi-calendar-check me-2"></span>{{ __('My Attendance') }}
        </a>

          <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.grades*') ? 'active' : '' }} {{ $studentMenuUrls['grades'] ? '' : 'disabled opacity-50' }}"
              href="{{ $studentMenuUrls['grades'] ?? '#' }}" @unless($studentMenuUrls['grades']) aria-disabled="true" tabindex="-1" @endunless>
            <span class="bi bi-trophy me-2"></span>{{ __('My Grades') }}
        </a>

          <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.timetable*') ? 'active' : '' }} {{ $studentMenuUrls['timetable'] ? '' : 'disabled opacity-50' }}"
              href="{{ $studentMenuUrls['timetable'] ?? '#' }}" @unless($studentMenuUrls['timetable']) aria-disabled="true" tabindex="-1" @endunless>
            <span class="bi bi-calendar2-week me-2"></span>{{ __('Timetable') }}
        </a>

          <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.library*') ? 'active' : '' }} {{ $studentMenuUrls['library'] ? '' : 'disabled opacity-50' }}"
              href="{{ $studentMenuUrls['library'] ?? '#' }}" @unless($studentMenuUrls['library']) aria-disabled="true" tabindex="-1" @endunless>
            <span class="bi bi-book-half me-2"></span>{{ __('Library') }}
        </a>

          <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.fees*') ? 'active' : '' }} {{ $studentMenuUrls['fees'] ? '' : 'disabled opacity-50' }}"
              href="{{ $studentMenuUrls['fees'] ?? '#' }}" @unless($studentMenuUrls['fees']) aria-disabled="true" tabindex="-1" @endunless>
            <span class="bi bi-cash-coin me-2"></span>{{ __('Fees & Payments') }}
        </a>

          <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.messages*') ? 'active' : '' }} {{ $studentMenuUrls['messages'] ? '' : 'disabled opacity-50' }}"
              href="{{ $studentMenuUrls['messages'] ?? '#' }}" @unless($studentMenuUrls['messages']) aria-disabled="true" tabindex="-1" @endunless>
            <span class="bi bi-envelope me-2"></span>{{ __('Messages') }}
        </a>

          <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.student.profile*') ? 'active' : '' }} {{ $studentMenuUrls['profile'] ? '' : 'disabled opacity-50' }}"
              href="{{ $studentMenuUrls['profile'] ?? '#' }}" @unless($studentMenuUrls['profile']) aria-disabled="true" tabindex="-1" @endunless>
            <span class="bi bi-person me-2"></span>{{ __('My Profile') }}
        </a>
    </div>
</div>

@php
    use App\Enums\UserType;

    $userType = $user?->user_type instanceof UserType ? $user->user_type->value : $user?->user_type ?? 'default';

    $logoUrl = setting('school_logo') ? \Illuminate\Support\Facades\Storage::url(setting('school_logo')) : null;
    $schoolName = setting('school_name', $school->name ?? config('app.name'));

    $baseItems = [
        [
            'label' => 'Dashboard',
            'icon' => 'bi-speedometer2',
            'url' => route('tenant.dashboard'),
            'active' => ['tenant.dashboard', 'tenant.dashboard.alias'],
        ],
        [
            'label' => 'Bookstore',
            'icon' => 'bi-shop',
            'url' => \Illuminate\Support\Facades\Route::has('tenant.bookstore.index')
                ? route('tenant.bookstore.index')
                : '#',
            'active' => ['tenant.bookstore.*'],
        ],
        [
            'label' => 'Forum',
            'icon' => 'bi-chat-quote',
            'url' => \Illuminate\Support\Facades\Route::has('tenant.forum.index') ? route('tenant.forum.index') : '#',
            'active' => ['tenant.forum.*'],
        ],
    ];

    $menus = [
        UserType::TEACHING_STAFF->value => array_merge($baseItems, [
            [
                'label' => 'Dashboard',
                'icon' => 'bi-speedometer2',
                'url' => route('tenant.teacher.dashboard'),
                'active' => ['tenant.teacher.dashboard'],
            ],
            [
                'label' => 'My Classes',
                'icon' => 'bi-easel',
                'url' => route('tenant.teacher.classes.index'),
                'active' => ['tenant.teacher.classes.*'],
            ],
            [
                'label' => 'Virtual Classes',
                'icon' => 'bi-camera-video',
                'url' => route('tenant.teacher.classroom.virtual.index'),
                'active' => ['tenant.teacher.classroom.virtual.*'],
            ],
            [
                'label' => 'Lesson Plans',
                'icon' => 'bi-journal-text',
                'url' => route('tenant.teacher.classroom.lessons.index'),
                'active' => ['tenant.teacher.classroom.lessons.*'],
            ],
            [
                'label' => 'Learning Materials',
                'icon' => 'bi-collection',
                'url' => route('tenant.teacher.classroom.materials.index'),
                'active' => ['tenant.teacher.classroom.materials.*'],
            ],
            [
                'type' => 'divider',
            ],
            [
                'type' => 'header',
                'label' => 'ASSIGNMENT SYSTEM',
            ],
            [
                'label' => 'All Assignments',
                'icon' => 'bi-list-task',
                'url' => route('tenant.teacher.classroom.exercises.index'),
                'active' => [
                    'tenant.teacher.classroom.exercises.index',
                    'tenant.teacher.classroom.exercises.edit',
                    'tenant.teacher.classroom.exercises.show',
                    'tenant.teacher.classroom.exercises.submissions',
                ],
                'badge' => 'NEW',
                'badge_class' => 'bg-success',
            ],
            [
                'label' => 'Create Assignment',
                'icon' => 'bi-plus-circle',
                'url' => route('tenant.teacher.classroom.exercises.create'),
                'active' => ['tenant.teacher.classroom.exercises.create'],
            ],
            [
                'type' => 'divider',
            ],
            [
                'label' => 'Quizzes',
                'icon' => 'bi-patch-question',
                'url' => route('tenant.teacher.classroom.quizzes.index'),
                'active' => ['tenant.teacher.classroom.quizzes.*'],
            ],
            [
                'label' => 'Online Exams',
                'icon' => 'bi-laptop',
                'url' => route('tenant.teacher.classroom.exams.index'),
                'active' => ['tenant.teacher.classroom.exams.*'],
            ],
            [
                'label' => 'Discussions',
                'icon' => 'bi-chat-square-text',
                'url' => route('tenant.teacher.classroom.discussions.index'),
                'active' => ['tenant.teacher.classroom.discussions.*'],
            ],
            [
                'label' => 'Integrations',
                'icon' => 'bi-puzzle',
                'url' => route('tenant.teacher.classroom.integrations.index'),
                'active' => ['tenant.teacher.classroom.integrations.*'],
            ],
            [
                'label' => 'Attendance',
                'icon' => 'bi-people-check',
                'url' => route('tenant.teacher.attendance.index'),
                'active' => ['tenant.teacher.attendance.*'],
            ],
            [
                'label' => 'Reports',
                'icon' => 'bi-bar-chart',
                'url' => route('tenant.reports.index'),
                'active' => ['tenant.reports.*'],
            ],
        ]),
        UserType::GENERAL_STAFF->value => array_merge($baseItems, [
            [
                'label' => 'Tasks',
                'icon' => 'bi-list-check',
                'url' => '#',
            ],
            [
                'label' => 'Resources',
                'icon' => 'bi-archive',
                'url' => '#',
            ],
        ]),
        UserType::STUDENT->value => array_merge($baseItems, [
            [
                'label' => 'Classroom',
                'icon' => 'bi-door-open',
                'url' => \Illuminate\Support\Facades\Route::has('tenant.student.classroom.index')
                    ? route('tenant.student.classroom.index')
                    : '#',
                'active' => ['tenant.student.classroom.index'],
            ],
            [
                'label' => 'Assignments',
                'icon' => 'bi-pencil-square',
                'url' => \Illuminate\Support\Facades\Route::has('tenant.student.classroom.exercises.index')
                    ? route('tenant.student.classroom.exercises.index')
                    : '#',
                'active' => ['tenant.student.classroom.exercises.*'],
            ],
            [
                'label' => 'My Grades',
                'icon' => 'bi-award',
                'url' => \Illuminate\Support\Facades\Route::has('tenant.student.classroom.exercises.grades')
                    ? route('tenant.student.classroom.exercises.grades')
                    : '#',
                'active' => ['tenant.student.classroom.exercises.grades'],
            ],
            [
                'label' => 'Virtual Classes',
                'icon' => 'bi-camera-video',
                'url' => \Illuminate\Support\Facades\Route::has('tenant.student.classroom.virtual.index')
                    ? route('tenant.student.classroom.virtual.index')
                    : '#',
                'active' => ['tenant.student.classroom.virtual.*'],
            ],
            [
                'label' => 'Materials',
                'icon' => 'bi-folder',
                'url' => \Illuminate\Support\Facades\Route::has('tenant.student.classroom.materials.index')
                    ? route('tenant.student.classroom.materials.index')
                    : '#',
                'active' => ['tenant.student.classroom.materials.*'],
            ],
            [
                'label' => 'Timetable',
                'icon' => 'bi-calendar-week',
                'url' => '#',
            ],
            [
                'label' => 'Attendance',
                'icon' => 'bi-calendar-check',
                'url' => \Illuminate\Support\Facades\Route::has('tenant.student.attendance.index')
                    ? route('tenant.student.attendance.index')
                    : '#',
                'active' => ['tenant.student.attendance.*'],
            ],
            [
                'label' => 'Research Assistant',
                'icon' => 'bi-robot',
                'type' => 'submenu',
                'id' => 'researchMenu',
                'active' => ['tenant.student.research.*'],
                'children' => [
                    [
                        'label' => 'Google',
                        'icon' => 'bi-google',
                        'url' => '#',
                    ],
                    [
                        'label' => 'Wikipedia',
                        'icon' => 'bi-globe',
                        'url' => '#',
                    ],
                    [
                        'label' => 'AI Research',
                        'icon' => 'bi-cpu',
                        'url' => route('tenant.student.notes.personal.create'),
                    ],
                ],
            ],
            [
                'label' => 'Pay Fees',
                'icon' => 'bi-credit-card',
                'url' => route('tenant.finance.payments.pay'),
                'active' => ['tenant.finance.payments.pay'],
            ],
        ]),
        UserType::PARENT->value => array_merge($baseItems, [
            [
                'label' => 'Ward Progress',
                'icon' => 'bi-emoji-smile',
                'url' => '#',
            ],
            [
                'label' => 'Invoices',
                'icon' => 'bi-receipt',
                'url' => '#',
            ],
            [
                'label' => 'Pay Fees',
                'icon' => 'bi-credit-card',
                'url' => route('tenant.finance.payments.pay'),
                'active' => ['tenant.finance.payments.pay'],
            ],
            [
                'label' => 'Messages',
                'icon' => 'bi-chat-dots',
                'url' => '#',
            ],
        ]),
    ];

    $menuItems = $menus[$userType] ?? $baseItems;
    $isAdmin = $userType === UserType::ADMIN->value;
@endphp

<div class="bg-white border-end h-100 d-flex flex-column">
    <div class="d-flex align-items-center gap-3 px-3 py-4 border-bottom flex-shrink-0">
        @if ($logoUrl)
            <img src="{{ $logoUrl }}" alt="{{ $schoolName }} Logo"
                style="width: 44px; height: 44px; object-fit: contain;"
                onerror="this.src=''; this.onerror=null; this.nextElementSibling.style.display='inline-flex';">
            <span class="bg-primary rounded-circle text-white d-none align-items-center justify-content-center"
                style="width: 44px; height: 44px;">
                <i class="bi bi-mortarboard-fill"></i>
            </span>
        @else
            <span class="bg-primary rounded-circle text-white d-inline-flex align-items-center justify-content-center"
                style="width: 44px; height: 44px;">
                <i class="bi bi-mortarboard-fill"></i>
            </span>
        @endif
        <div>
            <div class="fw-semibold">{{ $schoolName }}</div>
            <small class="text-muted">{{ $user?->user_type?->label() ?? 'Workspace' }}</small>
        </div>
    </div>

    @if ($isAdmin)
        {{-- Admin Menu --}}
        <nav class="flex-grow-1 overflow-auto">
            @include('tenant.layouts.partials.admin-menu')
        </nav>
    @else
        {{-- Other User Types Menu --}}
        <nav class="py-4 flex-grow-1 overflow-auto">
            <ul class="list-unstyled mb-0">
                @foreach ($menuItems as $item)
                    @if (isset($item['type']))
                        @if ($item['type'] === 'divider')
                            <li class="px-3 my-2">
                                <hr class="m-0 opacity-25">
                            </li>
                        @elseif($item['type'] === 'header')
                            <li class="px-3 mt-3 mb-2">
                                <small class="text-muted fw-bold text-uppercase"
                                    style="font-size: 0.75rem; letter-spacing: 0.5px;">{{ $item['label'] }}</small>
                            </li>
                        @elseif($item['type'] === 'submenu')
                            @php
                                $isActive = false;
                                foreach ((array) ($item['active'] ?? []) as $pattern) {
                                    if (request()->routeIs($pattern)) {
                                        $isActive = true;
                                        break;
                                    }
                                }
                            @endphp
                            <li class="px-3">
                                <a class="d-flex align-items-center gap-3 px-3 py-2 rounded mb-1 text-decoration-none position-relative {{ $isActive ? 'bg-primary text-white' : 'text-body' }}"
                                    data-bs-toggle="collapse" href="#{{ $item['id'] }}" role="button"
                                    aria-expanded="{{ $isActive ? 'true' : 'false' }}"
                                    aria-controls="{{ $item['id'] }}">
                                    <i class="{{ $item['icon'] ?? 'bi-circle' }}"></i>
                                    <span class="fw-medium flex-grow-1">{{ $item['label'] }}</span>
                                    <span class="bi bi-chevron-down small"></span>
                                </a>
                                <div class="collapse {{ $isActive ? 'show' : '' }}" id="{{ $item['id'] }}">
                                    <ul class="list-unstyled ms-3">
                                        @foreach ($item['children'] as $child)
                                            <li>
                                                <a href="{{ $child['url'] }}"
                                                    class="d-flex align-items-center gap-3 px-3 py-2 rounded mb-1 text-decoration-none text-body">
                                                    <i class="{{ $child['icon'] ?? 'bi-circle' }}"></i>
                                                    <span class="fw-medium">{{ $child['label'] }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </li>
                        @endif
                    @else
                        @php
                            $isActive = false;
                            foreach ((array) ($item['active'] ?? []) as $pattern) {
                                if (request()->routeIs($pattern)) {
                                    $isActive = true;
                                    break;
                                }
                            }
                        @endphp
                        <li class="px-3">
                            <a href="{{ $item['url'] ?? '#' }}"
                                class="d-flex align-items-center gap-3 px-3 py-2 rounded mb-1 text-decoration-none position-relative {{ $isActive ? 'bg-primary text-white' : 'text-body' }}">
                                <i class="{{ $item['icon'] ?? 'bi-circle' }}"></i>
                                <span class="fw-medium flex-grow-1">{{ $item['label'] }}</span>
                                @if (isset($item['badge']))
                                    <span
                                        class="badge {{ $item['badge_class'] ?? 'bg-primary' }} badge-sm">{{ $item['badge'] }}</span>
                                @endif
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </nav>
    @endif
</div>

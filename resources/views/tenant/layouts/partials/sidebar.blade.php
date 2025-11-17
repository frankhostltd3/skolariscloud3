@php
    use App\Enums\UserType;

    $userType = $user?->user_type instanceof UserType ? $user->user_type->value : $user?->user_type ?? 'default';

    $logoUrl = setting('school_logo') ? \Illuminate\Support\Facades\Storage::url(setting('school_logo')) : null;
    $schoolName = setting('school_name', $school->name ?? config('app.name'));

    $baseItems = [
        [
            'label' => 'Dashboard',
            'icon' => 'bi-speedometer2',
            'url' => url('/dashboard'),
            'active' => ['dashboard'],
        ],
    ];

    $menus = [
        UserType::TEACHING_STAFF->value => array_merge($baseItems, [
            [
                'label' => 'My Classes',
                'icon' => 'bi-easel',
                'url' => '#',
            ],
            [
                'label' => 'Assignments',
                'icon' => 'bi-clipboard-check',
                'url' => '#',
            ],
            [
                'label' => 'Attendance',
                'icon' => 'bi-people-check',
                'url' => '#',
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
                'label' => 'Timetable',
                'icon' => 'bi-calendar-week',
                'url' => '#',
            ],
            [
                'label' => 'Grades',
                'icon' => 'bi-mortarboard',
                'url' => '#',
            ],
            [
                'label' => 'Assignments',
                'icon' => 'bi-ui-checks-grid',
                'url' => '#',
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
                            class="d-flex align-items-center gap-3 px-3 py-2 rounded mb-1 text-decoration-none {{ $isActive ? 'bg-primary text-white' : 'text-body' }}">
                            <i class="{{ $item['icon'] ?? 'bi-circle' }}"></i>
                            <span class="fw-medium">{{ $item['label'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    @endif
</div>

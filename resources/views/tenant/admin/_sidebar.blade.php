<div class="card shadow-sm">
    <div class="card-header fw-semibold">{{ __('Admin menu') }}</div>
    <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.admin') ? 'active' : '' }}"
            href="{{ route('tenant.admin') }}" @if (request()->routeIs('tenant.admin')) aria-current="page" @endif>
            <span class="bi bi-speedometer2 me-2"></span>{{ __('Overview') }}
        </a>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.reports.index') ? 'active' : '' }}"
            href="{{ route('tenant.reports.index') }}">
            <span class="bi bi-file-earmark-text me-2"></span>{{ __('Academic Reports') }}
        </a>

        @php($examOversightActive = request()->routeIs('admin.exams.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $examOversightActive ? 'active' : '' }}"
            href="{{ route('admin.exams.index') }}" @if ($examOversightActive) aria-current="page" @endif>
            <span><span class="bi bi-clipboard2-check me-2"></span>{{ __('Exam Oversight') }}</span>
            @php($pendingExams = \App\Models\OnlineExam::where('approval_status', 'pending_review')->count())
            @if ($pendingExams > 0)
                <span class="badge bg-danger-subtle text-danger">{{ $pendingExams }}</span>
            @endif
        </a>

        {{-- User Approvals --}}
        <a class="list-group-item list-group-item-action {{ request()->routeIs('admin.user-approvals*') ? 'active' : '' }}"
            href="{{ route('admin.user-approvals.index') }}"
            @if (request()->routeIs('admin.user-approvals*')) aria-current="page" @endif>
            <span class="bi bi-person-check me-2"></span>{{ __('User Approvals') }}
            @php($pendingApprovals = \App\Models\User::where('approval_status', 'pending')->count())
            @if ($pendingApprovals > 0)
                <span class="badge bg-warning text-dark ms-2">{{ $pendingApprovals }}</span>
            @endif
        </a>

        @php($settingsActive = request()->routeIs('tenant.settings.admin.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $settingsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#settingsMenu" role="button"
            aria-expanded="{{ $settingsActive ? 'true' : 'false' }}" aria-controls="settingsMenu">
            <span><span class="bi bi-gear me-2"></span>{{ __('Settings') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $settingsActive ? 'show' : '' }}" id="settingsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.settings.admin.general') ? 'active' : '' }}"
                    href="{{ route('tenant.settings.admin.general') }}">
                    <span class="bi bi-house-gear me-2"></span>{{ __('General') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.settings.admin.academic') ? 'active' : '' }}"
                    href="{{ route('tenant.settings.admin.academic') }}">
                    <span class="bi bi-mortarboard me-2"></span>{{ __('Academic') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.settings.admin.system') ? 'active' : '' }}"
                    href="{{ route('tenant.settings.admin.system') }}">
                    <span class="bi bi-server me-2"></span>{{ __('System') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.settings.admin.finance') ? 'active' : '' }}"
                    href="{{ route('tenant.settings.admin.finance') }}">
                    <span class="bi bi-cash me-2"></span>{{ __('Finance') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.settings.admin.permissions') ? 'active' : '' }}"
                    href="{{ route('tenant.settings.admin.permissions') }}">
                    <span class="bi bi-shield me-2"></span>{{ __('Permissions') }}
                </a>
            </div>
        </div>

        @php($reportsActive = request()->routeIs('tenant.reports.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $reportsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#reportsMenu" role="button"
            aria-expanded="{{ $reportsActive ? 'true' : 'false' }}" aria-controls="reportsMenu">
            <span><span class="bi bi-bar-chart-line me-2"></span>{{ __('Reports') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $reportsActive ? 'show' : '' }}" id="reportsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.reports.index') ? 'active' : '' }}"
                    href="{{ route('tenant.reports.index') }}">
                    <span class="bi bi-house-door me-2"></span>{{ __('Overview') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.reports.academic') ? 'active' : '' }}"
                    href="{{ route('tenant.reports.academic') }}">
                    <span class="bi bi-mortarboard me-2"></span>{{ __('Academic') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.reports.attendance') ? 'active' : '' }}"
                    href="{{ route('tenant.reports.attendance') }}">
                    <span class="bi bi-people-check me-2"></span>{{ __('Attendance') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.reports.financial') ? 'active' : '' }}"
                    href="{{ route('tenant.reports.financial') }}">
                    <span class="bi bi-cash me-2"></span>{{ __('Financial') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.reports.enrollment') ? 'active' : '' }}"
                    href="{{ route('tenant.reports.enrollment') }}">
                    <span class="bi bi-person-plus me-2"></span>{{ __('Enrollment') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.reports.late-submissions') ? 'active' : '' }}"
                    href="{{ route('tenant.reports.late-submissions') }}">
                    <span class="bi bi-clock-history me-2"></span>{{ __('Late Submissions') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.reports.report-cards.*') ? 'active' : '' }}"
                    href="{{ route('tenant.reports.report-cards.index') }}">
                    <span class="bi bi-file-earmark-text me-2"></span>{{ __('Report Cards') }}
                </a>
            </div>
        </div>

        <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.profile.admin.*') ? 'active' : '' }}"
            href="{{ route('tenant.profile.admin.index') }}"
            @if (request()->routeIs('tenant.profile.admin.*')) aria-current="page" @endif>
            <span class="bi bi-person me-2"></span>{{ __('My Profile') }}
        </a>

        @php($academicsActive = request()->routeIs('tenant.academics.*') || request()->routeIs('admin.exams.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $academicsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#academicsMenu" role="button"
            aria-expanded="{{ $academicsActive ? 'true' : 'false' }}" aria-controls="academicsMenu">
            <span><span class="bi bi-mortarboard me-2"></span>{{ __('Academics') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $academicsActive ? 'show' : '' }}" id="academicsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.classes.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.classes.index') }}">
                    <span class="bi bi-house-door me-2"></span>{{ __('Classes') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.streams.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.streams.index', ['class' => 1]) }}">
                    <span class="bi bi-diagram-2 me-2"></span>{{ __('Class streams') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.subjects.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.subjects.index') }}">
                    <span class="bi bi-book me-2"></span>{{ __('Subjects') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.admin.lesson-plans.*') ? 'active' : '' }}"
                    href="{{ route('tenant.admin.lesson-plans.index') }}">
                    <span class="bi bi-journal-check me-2"></span>{{ __('Lesson Plan Reviews') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.terms.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.terms.index') }}">
                    <span class="bi bi-calendar-event me-2"></span>{{ __('Terms') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.grading_schemes.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.grading_schemes.index') }}">
                    <span class="bi bi-journal-check me-2"></span>{{ __('Grading Systems') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.exams.*') ? 'active' : '' }}"
                    href="{{ route('admin.exams.index') }}">
                    <span class="bi bi-clipboard-check me-2"></span>{{ __('Exams') }}
                </a>
            </div>
        </div>

        {{-- ASSIGNMENT SYSTEM - World-Class Features --}}
        @php($assignmentActive = request()->routeIs('tenant.teacher.classroom.exercises.*'))
        <div class="list-group-item bg-light border-0 py-1">
            <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                {{ __('ASSIGNMENT SYSTEM') }}
            </small>
        </div>
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $assignmentActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#assignmentMenu" role="button"
            aria-expanded="{{ $assignmentActive ? 'true' : 'false' }}" aria-controls="assignmentMenu">
            <span><span class="bi bi-list-task me-2"></span>{{ __('Assignments') }}</span>
            <span class="badge bg-success badge-sm">NEW</span>
            <span class="bi bi-chevron-down small ms-auto"></span>
        </a>
        <div class="collapse {{ $assignmentActive ? 'show' : '' }}" id="assignmentMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.teacher.classroom.exercises.index') ? 'active' : '' }}"
                    href="{{ route('tenant.teacher.classroom.exercises.index') }}">
                    <span class="bi bi-grid-3x3-gap me-2"></span>{{ __('All Assignments') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.teacher.classroom.exercises.create') ? 'active' : '' }}"
                    href="{{ route('tenant.teacher.classroom.exercises.create') }}">
                    <span class="bi bi-plus-circle me-2"></span>{{ __('Create Assignment') }}
                </a>
            </div>
        </div>

        @php($financeActive = request()->routeIs('tenant.finance.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $financeActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#financeMenu" role="button"
            aria-expanded="{{ $financeActive ? 'true' : 'false' }}" aria-controls="financeMenu">
            <span><span class="bi bi-cash-stack me-2"></span>{{ __('Finance') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $financeActive ? 'show' : '' }}" id="financeMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.finance.expense-categories.*') ? 'active' : '' }}"
                    href="{{ route('tenant.finance.expense-categories.index') }}">
                    <span class="bi bi-tags me-2"></span>{{ __('Expense Categories') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.finance.expenses.*') ? 'active' : '' }}"
                    href="{{ route('tenant.finance.expenses.index') }}">
                    <span class="bi bi-receipt me-2"></span>{{ __('Expenses') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.finance.fee-structures.*') ? 'active' : '' }}"
                    href="{{ route('tenant.finance.fee-structures.index') }}">
                    <span class="bi bi-file-earmark-text me-2"></span>{{ __('Fee Structures') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.finance.invoices.*') ? 'active' : '' }}"
                    href="{{ route('tenant.finance.invoices.index') }}">
                    <span class="bi bi-file-earmark-ruled me-2"></span>{{ __('Invoices') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.finance.payments.*') ? 'active' : '' }}"
                    href="{{ route('tenant.finance.payments.index') }}">
                    <span class="bi bi-credit-card me-2"></span>{{ __('Payments') }}
                </a>
            </div>
        </div>

        @php($hrActive = request()->routeIs('tenant.modules.human-resource.*'))
        @php($libraryActive = request()->routeIs('tenant.modules.library.*'))
        @php($bookstoreActive = request()->routeIs('tenant.bookstore.*') || request()->routeIs('tenant.modules.bookstore.*'))
        @php($timetableActive = request()->routeIs('tenant.academics.timetable.*'))

        @hasanyrole('Admin|Staff|admin')
            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $hrActive || $libraryActive || $bookstoreActive || $timetableActive ? 'active' : '' }}"
                data-bs-toggle="collapse" href="#modulesMenu" role="button"
                aria-expanded="{{ $hrActive || $libraryActive || $bookstoreActive || $timetableActive ? 'true' : 'false' }}"
                aria-controls="modulesMenu">
                <span><span class="bi bi-boxes me-2"></span>{{ __('Modules') }}</span>
                <span class="bi bi-chevron-down small"></span>
            </a>
            <div class="collapse {{ $hrActive || $libraryActive || $bookstoreActive || $timetableActive ? 'show' : '' }}"
                id="modulesMenu">
                <div class="list-group list-group-flush ms-3">

                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $hrActive ? 'active' : '' }}"
                        data-bs-toggle="collapse" href="#humanResourceMenu" role="button"
                        aria-expanded="{{ $hrActive ? 'true' : 'false' }}" aria-controls="humanResourceMenu">
                        <span><span class="bi bi-briefcase-fill me-2"></span>{{ __('Human resource') }}</span>
                        <span class="bi bi-chevron-down small"></span>
                    </a>
                    <div class="collapse {{ $hrActive ? 'show' : '' }}" id="humanResourceMenu">
                        <div class="list-group list-group-flush ms-3">
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.departments.index') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.human-resource.departments.index') }}">
                                <span class="bi bi-diagram-3 me-2"></span>{{ __('Departments') }}
                            </a>
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.positions.index') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.human-resource.positions.index') }}">
                                <span class="bi bi-person-badge me-2"></span>{{ __('Positions') }}
                            </a>
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.employees.index') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.human-resource.employees.index') }}">
                                <span class="bi bi-people-fill me-2"></span>{{ __('Employees') }}
                            </a>
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.leave_types.index') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.human-resource.leave_types.index') }}">
                                <span class="bi bi-calendar-x me-2"></span>{{ __('Leave types') }}
                            </a>
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.leave_requests.index') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.human-resource.leave_requests.index') }}">
                                <span class="bi bi-calendar-check me-2"></span>{{ __('Leave management') }}
                            </a>
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.payroll-settings.index') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.human-resource.payroll-settings.index') }}">
                                <span class="bi bi-cash-coin me-2"></span>{{ __('Payroll settings') }}
                            </a>
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.employee-ids.index') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.human-resource.employee-ids.index') }}">
                                <span class="bi bi-upc me-2"></span>{{ __('Employee IDs') }}
                            </a>
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.salary_scales.index') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.human-resource.salary_scales.index') }}">
                                <span class="bi bi-graph-up me-2"></span>{{ __('Salary scales') }}
                            </a>
                        </div>
                    </div>

                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $libraryActive ? 'active' : '' }}"
                        data-bs-toggle="collapse" href="#libraryMenu" role="button"
                        aria-expanded="{{ $libraryActive ? 'true' : 'false' }}" aria-controls="libraryMenu">
                        <span><span class="bi bi-journal-bookmark me-2"></span>{{ __('Library') }}</span>
                        <span class="bi bi-chevron-down small"></span>
                    </a>
                    <div class="collapse {{ $libraryActive ? 'show' : '' }}" id="libraryMenu">
                        <div class="list-group list-group-flush ms-3">
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.library.index') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.library.index') }}">
                                {{ __('Dashboard') }}
                            </a>
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.library.books.index') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.library.books.index') }}">
                                {{ __('Books') }}
                            </a>
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.library.transactions.index') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.library.transactions.index') }}">
                                {{ __('Transactions') }}
                            </a>
                        </div>
                    </div>

                    <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $bookstoreActive ? 'active' : '' }}"
                        data-bs-toggle="collapse" href="#bookstoreMenu" role="button"
                        aria-expanded="{{ $bookstoreActive ? 'true' : 'false' }}" aria-controls="bookstoreMenu">
                        <span><span class="bi bi-shop me-2"></span>{{ __('Bookstore') }}</span>
                        <span class="bi bi-chevron-down small"></span>
                    </a>
                    <div class="collapse {{ $bookstoreActive ? 'show' : '' }}" id="bookstoreMenu">
                        <div class="list-group list-group-flush ms-3">
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.bookstore.index') ? 'active' : '' }}"
                                href="{{ route('tenant.bookstore.index') }}">
                                {{ __('Dashboard') }}
                            </a>
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.bookstore.books.*') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.bookstore.books.index') }}">
                                {{ __('Inventory') }}
                            </a>
                            <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.bookstore.orders.*') ? 'active' : '' }}"
                                href="{{ route('tenant.modules.bookstore.orders.index') }}">
                                {{ __('Orders') }}
                            </a>
                        </div>
                    </div>

                    @canany(['manage timetable', 'view timetable'])
                        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $timetableActive ? 'active' : '' }}"
                            data-bs-toggle="collapse" href="#timetableMenu" role="button"
                            aria-expanded="{{ $timetableActive ? 'true' : 'false' }}" aria-controls="timetableMenu">
                            <span><span class="bi bi-calendar-week me-2"></span>{{ __('Timetable') }}</span>
                            <span class="bi bi-chevron-down small"></span>
                        </a>
                        <div class="collapse {{ $timetableActive ? 'show' : '' }}" id="timetableMenu">
                            <div class="list-group list-group-flush ms-3">
                                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.timetable.index') ? 'active' : '' }}"
                                    href="{{ route('tenant.academics.timetable.index') }}">
                                    {{ __('View Timetable') }}
                                </a>
                                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.timetable.generate') ? 'active' : '' }}"
                                    href="{{ route('tenant.academics.timetable.generate') }}">
                                    {{ __('Generate Timetable') }}
                                </a>
                            </div>
                        </div>
                    @endcanany

                </div>
            </div>
        @endhasanyrole

        @canany(['manage attendance', 'view attendance'])
            <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.modules.attendance.*') ? 'active' : '' }}"
                href="{{ route('tenant.modules.attendance.index') }}"
                @if (request()->routeIs('tenant.modules.attendance.*')) aria-current="page" @endif>
                <span class="bi bi-calendar-check-fill me-2"></span>{{ __('Attendance') }}
            </a>
        @endcanany

        @php($userMgmtActive = request()->routeIs('tenant.users.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $userMgmtActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#userMgmtMenu" role="button"
            aria-expanded="{{ $userMgmtActive ? 'true' : 'false' }}" aria-controls="userMgmtMenu">
            <span><span class="bi bi-people me-2"></span>{{ __('User management') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $userMgmtActive ? 'show' : '' }}" id="userMgmtMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.users.admins*') ? 'active' : '' }}"
                    href="{{ route('tenant.users.admins') }}">
                    <span class="bi bi-person-gear me-2"></span>{{ __('Administrators') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.employees*') ? 'active' : '' }}"
                    href="{{ route('tenant.modules.human-resource.employees.index') }}">
                    <span class="bi bi-person-badge me-2"></span>{{ __('Employees') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.students*') ? 'active' : '' }}"
                    href="{{ route('tenant.modules.students.index') }}">
                    <span class="bi bi-mortarboard-fill me-2"></span>{{ __('Students') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.enrollments*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.enrollments.index') }}">
                    <span class="bi bi-person-plus me-2"></span>{{ __('Enrollments') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.users.parents*') ? 'active' : '' }}"
                    href="{{ route('tenant.users.parents') }}">
                    <span class="bi bi-people me-2"></span>{{ __('Parents') }}
                </a>
                <div class="border-top my-2"></div>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.users.password*') ? 'active' : '' }}"
                    href="{{ route('admin.users.password.index') }}">
                    <span class="bi bi-shield-lock me-2"></span>{{ __('Password Management') }}
                    <span class="badge bg-warning text-dark ms-2">Admin</span>
                </a>
            </div>
        </div>

        @php($portalsActive = request()->routeIs('tenant.student.*') || request()->routeIs('tenant.teacher.*') || request()->routeIs('tenant.staff') || request()->routeIs('tenant.student'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $portalsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#portalsMenu" role="button"
            aria-expanded="{{ $portalsActive ? 'true' : 'false' }}" aria-controls="portalsMenu">
            <span><span class="bi bi-door-open me-2"></span>{{ __('User Portals') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $portalsActive ? 'show' : '' }}" id="portalsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.student') ? 'active' : '' }}"
                    href="{{ route('tenant.student') }}" target="_blank">
                    <span class="bi bi-mortarboard me-2"></span>{{ __('Student Portal') }}
                    <small class="text-muted ms-1">(Basic)</small>
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.student.dashboard') ? 'active' : '' }}"
                    href="{{ route('tenant.student.dashboard') }}" target="_blank">
                    <span class="bi bi-mortarboard-fill me-2"></span>{{ __('Student Portal') }}
                    <small class="text-muted ms-1">(Full)</small>
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.staff') ? 'active' : '' }}"
                    href="{{ route('tenant.staff') }}" target="_blank">
                    <span class="bi bi-person-badge me-2"></span>{{ __('Staff Portal') }}
                    <small class="text-muted ms-1">(Basic)</small>
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.teacher.dashboard') ? 'active' : '' }}"
                    href="{{ route('tenant.teacher.dashboard') }}" target="_blank">
                    <span class="bi bi-person-badge-fill me-2"></span>{{ __('Teacher Portal') }}
                    <small class="text-muted ms-1">(Full)</small>
                </a>
            </div>
        </div>

        @php($notificationsActive = request()->routeIs('admin.notifications.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $notificationsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#notificationsMenu" role="button"
            aria-expanded="{{ $notificationsActive ? 'true' : 'false' }}" aria-controls="notificationsMenu">
            <span><span class="bi bi-bell me-2"></span>{{ __('Notifications') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $notificationsActive ? 'show' : '' }}" id="notificationsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.notifications.index') ? 'active' : '' }}"
                    href="{{ route('admin.notifications.index') }}">
                    <span class="bi bi-list-ul me-2"></span>{{ __('All Notifications') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.notifications.create') ? 'active' : '' }}"
                    href="{{ route('admin.notifications.create') }}">
                    <span class="bi bi-plus-circle me-2"></span>{{ __('Create Notification') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.messages.index') ? 'active' : '' }}"
                    href="{{ route('admin.messages.index') }}">
                    <span class="bi bi-chat-dots me-2"></span>{{ __('Messages') }}
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Handle chevron rotation for collapsible menus
        document.addEventListener('DOMContentLoaded', function() {
            const collapsibleTriggers = document.querySelectorAll('[data-bs-toggle="collapse"]');

            collapsibleTriggers.forEach(trigger => {
                const chevron = trigger.querySelector('.bi-chevron-down');
                if (chevron) {
                    const collapseElement = document.querySelector(trigger.getAttribute('href'));

                    // Set initial state
                    if (collapseElement && collapseElement.classList.contains('show')) {
                        chevron.style.transform = 'rotate(180deg)';
                    }

                    // Listen for collapse events
                    collapseElement.addEventListener('show.bs.collapse', function() {
                        chevron.style.transform = 'rotate(180deg)';
                        chevron.style.transition = 'transform 0.3s ease';
                    });

                    collapseElement.addEventListener('hide.bs.collapse', function() {
                        chevron.style.transform = 'rotate(0deg)';
                        chevron.style.transition = 'transform 0.3s ease';
                    });
                }
            });
        });
    </script>
@endpush

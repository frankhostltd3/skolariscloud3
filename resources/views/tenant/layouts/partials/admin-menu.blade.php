<div class="card shadow-sm mx-3 my-3">
    <div class="card-header fw-semibold">{{ __('Admin menu') }}</div>
    <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action {{ request()->routeIs('dashboard') ? 'active' : '' }}"
            href="{{ route('dashboard') }}" @if (request()->routeIs('dashboard')) aria-current="page" @endif>
            <span class="bi bi-speedometer2 me-2"></span>{{ __('Overview') }}
        </a>

        {{-- User Approvals --}}
        <a class="list-group-item list-group-item-action {{ request()->routeIs('admin.user-approvals*') ? 'active' : '' }}"
            href="{{ route('admin.user-approvals.index') }}"
            @if (request()->routeIs('admin.user-approvals*')) aria-current="page" @endif>
            <span class="bi bi-person-check me-2"></span>{{ __('User Approvals') }}
        </a>

        @php($settingsActive = request()->routeIs('settings.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $settingsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#settingsMenu" role="button"
            aria-expanded="{{ $settingsActive ? 'true' : 'false' }}" aria-controls="settingsMenu">
            <span><span class="bi bi-gear me-2"></span>{{ __('Settings') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $settingsActive ? 'show' : '' }}" id="settingsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('settings.index') ? 'active' : '' }}"
                    href="{{ route('settings.index') }}">
                    <span class="bi bi-house-gear me-2"></span>{{ __('Overview') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('settings.general.*') ? 'active' : '' }}"
                    href="{{ route('settings.general.edit') }}">
                    <span class="bi bi-gear me-2"></span>{{ __('General Settings') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('settings.mail.*') ? 'active' : '' }}"
                    href="{{ route('settings.mail.edit') }}">
                    <span class="bi bi-envelope me-2"></span>{{ __('Mail Delivery') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('settings.payments.*') ? 'active' : '' }}"
                    href="{{ route('settings.payments.edit') }}">
                    <span class="bi bi-credit-card me-2"></span>{{ __('Payment Settings') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('settings.currencies.*') ? 'active' : '' }}"
                    href="{{ route('settings.currencies.index') }}">
                    <span class="bi bi-currency-exchange me-2"></span>{{ __('Currencies') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('settings.messaging.*') ? 'active' : '' }}"
                    href="{{ route('settings.messaging.edit') }}">
                    <span class="bi bi-chat-dots me-2"></span>{{ __('Messaging') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('settings.academic.*') ? 'active' : '' }}"
                    href="{{ route('settings.academic.edit') }}">
                    <span class="bi bi-mortarboard me-2"></span>{{ __('Academic Settings') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('settings.system.*') ? 'active' : '' }}"
                    href="{{ route('settings.system.edit') }}">
                    <span class="bi bi-server me-2"></span>{{ __('System') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.settings.admin.permissions') ? 'active' : '' }}"
                    href="{{ route('tenant.settings.admin.permissions') }}">
                    <span class="bi bi-shield-lock me-2"></span>{{ __('Permissions') }}
                </a>
            </div>
        </div>

        @php($reportsActive = request()->routeIs('admin.reports*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $reportsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#reportsMenu" role="button"
            aria-expanded="{{ $reportsActive ? 'true' : 'false' }}" aria-controls="reportsMenu">
            <span><span class="bi bi-bar-chart-line me-2"></span>{{ __('Reports') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $reportsActive ? 'show' : '' }}" id="reportsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.reports.index') ? 'active' : '' }}"
                    href="{{ route('admin.reports.index') }}">
                    <span class="bi bi-house-door me-2"></span>{{ __('Overview') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.reports.academic') ? 'active' : '' }}"
                    href="{{ route('admin.reports.academic') }}">
                    <span class="bi bi-mortarboard me-2"></span>{{ __('Academic') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.reports.attendance') ? 'active' : '' }}"
                    href="{{ route('admin.reports.attendance') }}">
                    <span class="bi bi-calendar-check me-2"></span>{{ __('Attendance') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.reports.financial') ? 'active' : '' }}"
                    href="{{ route('admin.reports.financial') }}">
                    <span class="bi bi-cash me-2"></span>{{ __('Financial') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.reports.enrollment') ? 'active' : '' }}"
                    href="{{ route('admin.reports.enrollment') }}">
                    <span class="bi bi-person-plus me-2"></span>{{ __('Enrollment') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.reports.late-submissions*') ? 'active' : '' }}"
                    href="{{ route('admin.reports.late-submissions') }}">
                    <span class="bi bi-clock-history me-2"></span>{{ __('Late Submissions') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.reports.report-cards*') ? 'active' : '' }}"
                    href="{{ route('admin.reports.report-cards') }}">
                    <span class="bi bi-card-text me-2"></span>{{ __('Report Cards') }}
                </a>
            </div>
        </div>



        @php($academicsActive = request()->routeIs('tenant.academics.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $academicsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#academicsMenu" role="button"
            aria-expanded="{{ $academicsActive ? 'true' : 'false' }}" aria-controls="academicsMenu">
            <span><span class="bi bi-mortarboard me-2"></span>{{ __('Academics') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $academicsActive ? 'show' : '' }}" id="academicsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.education-levels.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.education-levels.index') }}">
                    <span class="bi bi-mortarboard-fill me-2"></span>{{ __('Education Levels') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.examination-bodies.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.examination-bodies.index') }}">
                    <span class="bi bi-award me-2"></span>{{ __('Examination Bodies') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.countries.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.countries.index') }}">
                    <span class="bi bi-globe me-2"></span>{{ __('Countries') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.grading_schemes.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.grading_schemes.index') }}">
                    <span class="bi bi-award-fill me-2"></span>{{ __('Grading Systems') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.classes.*') && !request()->routeIs('tenant.academics.streams.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.classes.index') }}">
                    <span class="bi bi-building me-2"></span>{{ __('Classes') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.streams.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.classes.index') }}"
                    title="{{ __('Select a class to manage its streams') }}">
                    <span class="bi bi-diagram-3 me-2"></span>{{ __('Class streams') }}
                    <small class="text-muted ms-1">({{ __('via Classes') }})</small>
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.subjects.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.subjects.index') }}">
                    <span class="bi bi-book me-2"></span>{{ __('Subjects') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.teacher-allocations.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.teacher-allocations.index') }}">
                    <span class="bi bi-person-badge me-2"></span>{{ __('Teacher Allocation') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.terms.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.terms.index') }}">
                    <span class="bi bi-calendar-event me-2"></span>{{ __('Terms') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.timetable.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.timetable.index') }}">
                    <span class="bi bi-calendar3 me-2"></span>{{ __('Timetable') }}
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
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $hrActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#hrMenu" role="button"
            aria-expanded="{{ $hrActive ? 'true' : 'false' }}" aria-controls="hrMenu">
            <span><span class="bi bi-person-workspace me-2"></span>{{ __('Human Resources') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $hrActive ? 'show' : '' }}" id="hrMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.index') ? 'active' : '' }}"
                    href="{{ route('tenant.modules.human-resource.index') }}">
                    <span class="bi bi-house-door me-2"></span>{{ __('HR Overview') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.employees.*') ? 'active' : '' }}"
                    href="{{ route('tenant.modules.human-resource.employees.index') }}">
                    <span class="bi bi-people me-2"></span>{{ __('Employees') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.departments.*') ? 'active' : '' }}"
                    href="{{ route('tenant.modules.human-resource.departments.index') }}">
                    <span class="bi bi-diagram-3 me-2"></span>{{ __('Departments') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.positions.*') ? 'active' : '' }}"
                    href="{{ route('tenant.modules.human-resource.positions.index') }}">
                    <span class="bi bi-briefcase me-2"></span>{{ __('Positions') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.salary-scales.*') ? 'active' : '' }}"
                    href="{{ route('tenant.modules.human-resource.salary-scales.index') }}">
                    <span class="bi bi-cash-coin me-2"></span>{{ __('Salary Scales') }}
                </a>
                <div class="border-top my-2"></div>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.leave-types.*') ? 'active' : '' }}"
                    href="{{ route('tenant.modules.human-resource.leave-types.index') }}">
                    <span class="bi bi-calendar-x me-2"></span>{{ __('Leave Types') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.leave-requests.*') ? 'active' : '' }}"
                    href="{{ route('tenant.modules.human-resource.leave-requests.index') }}">
                    <span class="bi bi-calendar-event me-2"></span>{{ __('Leave Requests') }}
                </a>
                <div class="border-top my-2"></div>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.payroll-settings.*') ? 'active' : '' }}"
                    href="{{ route('tenant.modules.human-resource.payroll-settings.index') }}">
                    <span class="bi bi-gear me-2"></span>{{ __('Payroll Settings') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.modules.human-resource.payroll-payslip.*') ? 'active' : '' }}"
                    href="{{ route('tenant.modules.human-resource.payroll-payslip.index') }}">
                    <span class="bi bi-receipt-cutoff me-2"></span>{{ __('Payslips') }}
                </a>
            </div>
        </div>

        @php($attendanceActive = request()->routeIs('admin.attendance.*') || request()->routeIs('admin.staff-attendance.*') || request()->routeIs('admin.exam-attendance.*') || request()->routeIs('admin.qr-scanner.*') || request()->routeIs('admin.biometric.*') || request()->routeIs('admin.omr.*') || request()->routeIs('admin.attendance-analytics.*') || request()->routeIs('admin.device-monitoring.*') || request()->routeIs('tenant.attendance.settings.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $attendanceActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#attendanceMenu" role="button"
            aria-expanded="{{ $attendanceActive ? 'true' : 'false' }}" aria-controls="attendanceMenu">
            <span><span class="bi bi-calendar-check-fill me-2"></span>{{ __('Attendance') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $attendanceActive ? 'show' : '' }}" id="attendanceMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.attendance.*') && !request()->routeIs('admin.attendance-analytics.*') ? 'active' : '' }}"
                    href="{{ route('admin.attendance.index') }}">
                    <span class="bi bi-people me-2"></span>{{ __('Student Attendance') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.staff-attendance.*') ? 'active' : '' }}"
                    href="{{ route('admin.staff-attendance.index') }}">
                    <span class="bi bi-person-badge me-2"></span>{{ __('Staff Attendance') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.exam-attendance.*') ? 'active' : '' }}"
                    href="{{ route('admin.exam-attendance.index') }}">
                    <span class="bi bi-journal-check me-2"></span>{{ __('Exam Attendance') }}
                </a>
                <div class="border-top my-2"></div>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.qr-scanner.*') ? 'active' : '' }}"
                    href="{{ route('admin.qr-scanner.index') }}">
                    <span class="bi bi-qr-code-scan me-2"></span>{{ __('QR Scanner') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.biometric.*') ? 'active' : '' }}"
                    href="{{ route('admin.biometric.index') }}">
                    <span class="bi bi-fingerprint me-2"></span>{{ __('Biometric Enrollment') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.omr.*') ? 'active' : '' }}"
                    href="{{ route('admin.omr.index') }}">
                    <span class="bi bi-file-earmark-medical me-2"></span>{{ __('OMR Templates') }}
                </a>
                <div class="border-top my-2"></div>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.attendance-analytics.*') ? 'active' : '' }}"
                    href="{{ route('admin.attendance-analytics.index') }}">
                    <span class="bi bi-graph-up me-2"></span>{{ __('Analytics Dashboard') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.device-monitoring.*') ? 'active' : '' }}"
                    href="{{ route('admin.device-monitoring.index') }}">
                    <span class="bi bi-hdd-network me-2"></span>{{ __('Device Monitoring') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.attendance.settings.*') ? 'active' : '' }}"
                    href="{{ route('tenant.attendance.settings.index') }}">
                    <span class="bi bi-gear me-2"></span>{{ __('Attendance Settings') }}
                </a>
            </div>
        </div>

        @php($userMgmtActive = false)
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $userMgmtActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#userMgmtMenu" role="button"
            aria-expanded="{{ $userMgmtActive ? 'true' : 'false' }}" aria-controls="userMgmtMenu">
            <span><span class="bi bi-people me-2"></span>{{ __('User management') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $userMgmtActive ? 'show' : '' }}" id="userMgmtMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-person-gear me-2"></span>{{ __('Administrators') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-person-badge me-2"></span>{{ __('Employees') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-mortarboard-fill me-2"></span>{{ __('Students') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-people me-2"></span>{{ __('Parents') }}
                </a>
                <div class="border-top my-2"></div>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-shield-lock me-2"></span>{{ __('Password Management') }}
                    <span class="badge bg-warning text-dark ms-2">Admin</span>
                </a>
            </div>
        </div>

        @php($portalsActive = false)
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $portalsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#portalsMenu" role="button"
            aria-expanded="{{ $portalsActive ? 'true' : 'false' }}" aria-controls="portalsMenu">
            <span><span class="bi bi-door-open me-2"></span>{{ __('User Portals') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $portalsActive ? 'show' : '' }}" id="portalsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none" href="#"
                    target="_blank">
                    <span class="bi bi-mortarboard me-2"></span>{{ __('Student Portal') }}
                    <small class="text-muted ms-1">(Basic)</small>
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#"
                    target="_blank">
                    <span class="bi bi-mortarboard-fill me-2"></span>{{ __('Student Portal') }}
                    <small class="text-muted ms-1">(Full)</small>
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#"
                    target="_blank">
                    <span class="bi bi-person-badge me-2"></span>{{ __('Staff Portal') }}
                    <small class="text-muted ms-1">(Basic)</small>
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#"
                    target="_blank">
                    <span class="bi bi-person-badge-fill me-2"></span>{{ __('Teacher Portal') }}
                    <small class="text-muted ms-1">(Full)</small>
                </a>
            </div>
        </div>

        @php($notificationsActive = false)
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $notificationsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#notificationsMenu" role="button"
            aria-expanded="{{ $notificationsActive ? 'true' : 'false' }}" aria-controls="notificationsMenu">
            <span><span class="bi bi-bell me-2"></span>{{ __('Notifications') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $notificationsActive ? 'show' : '' }}" id="notificationsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-list-ul me-2"></span>{{ __('All Notifications') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-plus-circle me-2"></span>{{ __('Create Notification') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-chat-dots me-2"></span>{{ __('Messages') }}
                </a>
            </div>
        </div>

        {{-- Logout at the bottom --}}
        <div class="border-top mt-2">
            <form method="POST" action="{{ route('tenant.logout') }}" class="m-0">
                @csrf
                <button type="submit"
                    class="list-group-item list-group-item-action text-danger border-0 w-100 text-start">
                    <span class="bi bi-box-arrow-right me-2"></span>{{ __('Logout') }}
                </button>
            </form>
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

                    if (collapseElement) {
                        // Set initial state
                        if (collapseElement.classList.contains('show')) {
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
                }
            });
        });
    </script>
@endpush

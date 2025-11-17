<div class="card shadow-sm mx-3 my-3">
    <div class="card-header fw-semibold">{{ __('Admin menu') }}</div>
    <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action {{ request()->is('dashboard*') ? 'active' : '' }}"
            href="{{ url('/dashboard') }}" @if (request()->is('dashboard*')) aria-current="page" @endif>
            <span class="bi bi-speedometer2 me-2"></span>{{ __('Overview') }}
        </a>

        {{-- User Approvals --}}
        <a class="list-group-item list-group-item-action {{ request()->is('admin/user-approvals*') ? 'active' : '' }}"
            href="{{ url('/admin/user-approvals') }}" @if (request()->is('admin/user-approvals*')) aria-current="page" @endif>
            <span class="bi bi-person-check me-2"></span>{{ __('User Approvals') }}
        </a>

        @php
            $portalsActive = request()->routeIs('tenant.student.*')
                || request()->routeIs('tenant.staff')
                || request()->routeIs('tenant.teacher.*');
            $portalLinks = [
                'student_basic' => \Illuminate\Support\Facades\Route::has('tenant.student')
                    ? route('tenant.student')
                    : null,
                'student_full' => \Illuminate\Support\Facades\Route::has('tenant.student.dashboard')
                    ? route('tenant.student.dashboard')
                    : null,
                'staff_basic' => \Illuminate\Support\Facades\Route::has('tenant.staff')
                    ? route('tenant.staff')
                    : null,
                'teacher_full' => \Illuminate\Support\Facades\Route::has('tenant.teacher.dashboard')
                    ? route('tenant.teacher.dashboard')
                    : null,
            ];
        @endphp

        @php($settingsActive = request()->routeIs('settings.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $settingsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#settingsMenu" role="button"
            aria-expanded="{{ $settingsActive ? 'true' : 'false' }}" aria-controls="settingsMenu">
            <span><span class="bi bi-gear me-2"></span>{{ __('Settings') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $settingsActive ? 'show' : '' }}" id="settingsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('settings') ? 'active' : '' }}"
                    href="{{ url('/settings') }}">
                    <span class="bi bi-house-gear me-2"></span>{{ __('Overview') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('settings/general*') ? 'active' : '' }}"
                    href="{{ url('/settings/general') }}">
                    <span class="bi bi-gear me-2"></span>{{ __('General Settings') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('settings/mail*') ? 'active' : '' }}"
                    href="{{ url('/settings/mail') }}">
                    <span class="bi bi-envelope me-2"></span>{{ __('Mail Delivery') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('settings/payments*') ? 'active' : '' }}"
                    href="{{ url('/settings/payments') }}">
                    <span class="bi bi-credit-card me-2"></span>{{ __('Payment Settings') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('settings/currencies*') ? 'active' : '' }}"
                    href="{{ url('/settings/currencies') }}">
                    <span class="bi bi-currency-exchange me-2"></span>{{ __('Currencies') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('settings/messaging*') ? 'active' : '' }}"
                    href="{{ url('/settings/messaging') }}">
                    <span class="bi bi-chat-dots me-2"></span>{{ __('Messaging') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('settings/academic*') ? 'active' : '' }}"
                    href="{{ url('/settings/academic') }}">
                    <span class="bi bi-mortarboard me-2"></span>{{ __('Academic Settings') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('settings/system*') ? 'active' : '' }}"
                    href="{{ url('/settings/system') }}">
                    <span class="bi bi-server me-2"></span>{{ __('System') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('settings/admin/permissions*') ? 'active' : '' }}"
                    href="{{ url('/settings/admin/permissions') }}">
                    <span class="bi bi-shield-lock me-2"></span>{{ __('Permissions') }}
                </a>
            </div>
        </div>

        @php($reportsActive = request()->is('admin/reports*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $reportsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#reportsMenu" role="button"
            aria-expanded="{{ $reportsActive ? 'true' : 'false' }}" aria-controls="reportsMenu">
            <span><span class="bi bi-bar-chart-line me-2"></span>{{ __('Reports') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $reportsActive ? 'show' : '' }}" id="reportsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('admin/reports/attendance*') ? 'active' : '' }}"
                    href="{{ url('/admin/reports/attendance') }}">
                    <span class="bi bi-calendar-check me-2"></span>{{ __('Attendance') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('admin/reports/financial*') ? 'active' : '' }}"
                    href="{{ url('/admin/reports/financial') }}">
                    <span class="bi bi-cash me-2"></span>{{ __('Financial') }}
                </a>
                {{-- Other report routes not yet implemented --}}
            </div>
        </div>



        @php($academicsActive = request()->is('tenant/academics*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $academicsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#academicsMenu" role="button"
            aria-expanded="{{ $academicsActive ? 'true' : 'false' }}" aria-controls="academicsMenu">
            <span><span class="bi bi-mortarboard me-2"></span>{{ __('Academics') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $academicsActive ? 'show' : '' }}" id="academicsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/academics/education-levels.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/academics/education-levels') }}">
                    <span class="bi bi-mortarboard-fill me-2"></span>{{ __('Education Levels') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/academics/examination-bodies.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/academics/examination-bodies') }}">
                    <span class="bi bi-award me-2"></span>{{ __('Examination Bodies') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/academics/countries.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/academics/countries') }}">
                    <span class="bi bi-globe me-2"></span>{{ __('Countries') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/academics/grading_schemes.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/academics/grading_schemes') }}">
                    <span class="bi bi-award-fill me-2"></span>{{ __('Grading Systems') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/academics/classes.*') && !request()->is('tenant/academics/streams.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/academics/classes') }}">
                    <span class="bi bi-building me-2"></span>{{ __('Classes') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/academics/streams.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/academics/classes') }}"
                    title="{{ __('Select a class to manage its streams') }}">
                    <span class="bi bi-diagram-3 me-2"></span>{{ __('Class streams') }}
                    <small class="text-muted ms-1">({{ __('via Classes') }})</small>
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/academics/subjects.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/academics/subjects') }}">
                    <span class="bi bi-book me-2"></span>{{ __('Subjects') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/academics/teacher-allocations.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/academics/teacher-allocations') }}">
                    <span class="bi bi-person-badge me-2"></span>{{ __('Teacher Allocation') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/academics/terms.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/academics/terms') }}">
                    <span class="bi bi-calendar-event me-2"></span>{{ __('Terms') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/academics/timetable.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/academics/timetable') }}">
                    <span class="bi bi-calendar3 me-2"></span>{{ __('Timetable') }}
                </a>
            </div>
        </div>

        @php($financeActive = request()->is('tenant/finance.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $financeActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#financeMenu" role="button"
            aria-expanded="{{ $financeActive ? 'true' : 'false' }}" aria-controls="financeMenu">
            <span><span class="bi bi-cash-stack me-2"></span>{{ __('Finance') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $financeActive ? 'show' : '' }}" id="financeMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/finance.expense-categories.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/finance/expense-categories') }}">
                    <span class="bi bi-tags me-2"></span>{{ __('Expense Categories') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/finance.expenses.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/finance/expenses') }}">
                    <span class="bi bi-receipt me-2"></span>{{ __('Expenses') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/finance.fee-structures.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/finance/fee-structures') }}">
                    <span class="bi bi-file-earmark-text me-2"></span>{{ __('Fee Structures') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/finance.invoices.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/finance/invoices') }}">
                    <span class="bi bi-file-earmark-ruled me-2"></span>{{ __('Invoices') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/finance.payments.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/finance/payments') }}">
                    <span class="bi bi-credit-card me-2"></span>{{ __('Payments') }}
                </a>
            </div>
        </div>

        @php($hrActive = request()->is('tenant/modules/human-resource.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $hrActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#hrMenu" role="button"
            aria-expanded="{{ $hrActive ? 'true' : 'false' }}" aria-controls="hrMenu">
            <span><span class="bi bi-person-workspace me-2"></span>{{ __('Human Resources') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $hrActive ? 'show' : '' }}" id="hrMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/modules/human-resource.index') ? 'active' : '' }}"
                    href="{{ url('/tenant/modules/human-resource') }}">
                    <span class="bi bi-house-door me-2"></span>{{ __('HR Overview') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/modules/human-resource.employees.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/modules/human-resource/employees') }}">
                    <span class="bi bi-people me-2"></span>{{ __('Employees') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/modules/human-resource.departments.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/modules/human-resource/departments') }}">
                    <span class="bi bi-diagram-3 me-2"></span>{{ __('Departments') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/modules/human-resource.positions.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/modules/human-resource/positions') }}">
                    <span class="bi bi-briefcase me-2"></span>{{ __('Positions') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/modules/human-resource.salary-scales.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/modules/human-resource/salary-scales') }}">
                    <span class="bi bi-cash-coin me-2"></span>{{ __('Salary Scales') }}
                </a>
                <div class="border-top my-2"></div>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/modules/human-resource.leave-types.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/modules/human-resource/leave-types') }}">
                    <span class="bi bi-calendar-x me-2"></span>{{ __('Leave Types') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/modules/human-resource.leave-requests.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/modules/human-resource/leave-requests') }}">
                    <span class="bi bi-calendar-event me-2"></span>{{ __('Leave Requests') }}
                </a>
                <div class="border-top my-2"></div>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/modules/human-resource.payroll-settings.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/modules/human-resource/payroll-settings') }}">
                    <span class="bi bi-gear me-2"></span>{{ __('Payroll Settings') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/modules/human-resource.payroll-payslip.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/modules/human-resource/payroll-payslip') }}">
                    <span class="bi bi-receipt-cutoff me-2"></span>{{ __('Payslips') }}
                </a>
            </div>
        </div>

        @php($attendanceActive = request()->is('admin/attendance.*') || request()->is('admin/staff-attendance.*') || request()->is('admin/exam-attendance.*') || request()->is('admin/qr-scanner.*') || request()->is('admin/biometric.*') || request()->is('admin/omr.*') || request()->is('admin/attendance-analytics.*') || request()->is('admin/device-monitoring.*') || request()->is('tenant/attendance/settings.*'))
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $attendanceActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#attendanceMenu" role="button"
            aria-expanded="{{ $attendanceActive ? 'true' : 'false' }}" aria-controls="attendanceMenu">
            <span><span class="bi bi-calendar-check-fill me-2"></span>{{ __('Attendance') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $attendanceActive ? 'show' : '' }}" id="attendanceMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('admin/attendance.*') && !request()->is('admin/attendance-analytics.*') ? 'active' : '' }}"
                    href="{{ url('/admin/attendance') }}">
                    <span class="bi bi-people me-2"></span>{{ __('Student Attendance') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('admin/staff-attendance.*') ? 'active' : '' }}"
                    href="{{ url('/admin/staff-attendance') }}">
                    <span class="bi bi-person-badge me-2"></span>{{ __('Staff Attendance') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('admin/exam-attendance.*') ? 'active' : '' }}"
                    href="{{ url('/admin/exam-attendance') }}">
                    <span class="bi bi-clipboard2-check me-2"></span>{{ __('Exam Attendance') }}
                </a>
                <div class="border-top my-2"></div>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('admin/qr-scanner.*') ? 'active' : '' }}"
                    href="{{ url('/admin/qr-scanner') }}">
                    <span class="bi bi-qr-code-scan me-2"></span>{{ __('QR Scanner') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('admin/biometric.*') ? 'active' : '' }}"
                    href="{{ url('/admin/biometric') }}">
                    <span class="bi bi-fingerprint me-2"></span>{{ __('Biometric Enrollment') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('admin/omr.*') ? 'active' : '' }}"
                    href="{{ url('/admin/omr') }}">
                    <span class="bi bi-file-earmark-ruled me-2"></span>{{ __('OMR Template') }}
                </a>
                <div class="border-top my-2"></div>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('admin/attendance-analytics.*') ? 'active' : '' }}"
                    href="{{ url('/admin/attendance-analytics') }}">
                    <span class="bi bi-graph-up me-2"></span>{{ __('Analytics Dashboard') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('admin/device-monitoring.*') ? 'active' : '' }}"
                    href="{{ url('/admin/device-monitoring') }}">
                    <span class="bi bi-hdd-network me-2"></span>{{ __('Device Monitoring') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->is('tenant/attendance/settings.*') ? 'active' : '' }}"
                    href="{{ url('/tenant/attendance/settings') }}">
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

        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $portalsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#portalsMenu" role="button"
            aria-expanded="{{ $portalsActive ? 'true' : 'false' }}" aria-controls="portalsMenu">
            <span><span class="bi bi-door-open me-2"></span>{{ __('User Portals') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $portalsActive ? 'show' : '' }}" id="portalsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none @if(!$portalLinks['student_basic']) disabled @endif"
                    href="{{ $portalLinks['student_basic'] ?? '#' }}" target="_blank"
                    @if(!$portalLinks['student_basic']) aria-disabled="true" @endif>
                    <span class="bi bi-mortarboard me-2"></span>{{ __('Student Portal') }}
                    <small class="text-muted ms-1">(Basic)</small>
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none @if(!$portalLinks['student_full']) disabled @endif"
                    href="{{ $portalLinks['student_full'] ?? '#' }}" target="_blank"
                    @if(!$portalLinks['student_full']) aria-disabled="true" @endif>
                    <span class="bi bi-mortarboard-fill me-2"></span>{{ __('Student Portal') }}
                    <small class="text-muted ms-1">(Full)</small>
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none @if(!$portalLinks['staff_basic']) disabled @endif"
                    href="{{ $portalLinks['staff_basic'] ?? '#' }}" target="_blank"
                    @if(!$portalLinks['staff_basic']) aria-disabled="true" @endif>
                    <span class="bi bi-person-badge me-2"></span>{{ __('Staff Portal') }}
                    <small class="text-muted ms-1">(Basic)</small>
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none @if(!$portalLinks['teacher_full']) disabled @endif"
                    href="{{ $portalLinks['teacher_full'] ?? '#' }}" target="_blank"
                    @if(!$portalLinks['teacher_full']) aria-disabled="true" @endif>
                    <span class="bi bi-person-badge-fill me-2"></span>{{ __('Teacher Portal') }}
                    <small class="text-muted ms-1">(Full)</small>
                </a>
            </div>
        </div>

        @php($notificationsActive = request()->routeIs('admin.notifications.*') || request()->routeIs('admin.messages.*'))
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
                <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}"
                    href="{{ route('admin.messages.index') }}">
                    <span class="bi bi-chat-dots me-2"></span>{{ __('Messages') }}
                </a>
            </div>
        </div>

        {{-- Logout at the bottom --}}
        <div class="mt-auto">
            <form method="POST" action="{{ route('tenant.logout') }}" class="m-0">
                @csrf
                <button type="submit"
                    class="list-group-item list-group-item-action text-decoration-none border-0 bg-transparent text-start w-100">
                    <span class="bi bi-box-arrow-right me-2"></span>{{ __('Logout') }}
                </button>
            </form>
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

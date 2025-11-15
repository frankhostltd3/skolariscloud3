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
            </div>
        </div>

        @php($reportsActive = false)
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $reportsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#reportsMenu" role="button"
            aria-expanded="{{ $reportsActive ? 'true' : 'false' }}" aria-controls="reportsMenu">
            <span><span class="bi bi-bar-chart-line me-2"></span>{{ __('Reports') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $reportsActive ? 'show' : '' }}" id="reportsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-house-door me-2"></span>{{ __('Overview') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-mortarboard me-2"></span>{{ __('Academic') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-people-check me-2"></span>{{ __('Attendance') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-cash me-2"></span>{{ __('Financial') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-person-plus me-2"></span>{{ __('Enrollment') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-clock-history me-2"></span>{{ __('Late Submissions') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-file-earmark-text me-2"></span>{{ __('Report Cards') }}
                </a>
            </div>
        </div>



        @php($academicsActive = false)
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $academicsActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#academicsMenu" role="button"
            aria-expanded="{{ $academicsActive ? 'true' : 'false' }}" aria-controls="academicsMenu">
            <span><span class="bi bi-mortarboard me-2"></span>{{ __('Academics') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $academicsActive ? 'show' : '' }}" id="academicsMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-house-door me-2"></span>{{ __('Classes') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-diagram-2 me-2"></span>{{ __('Class streams') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-book me-2"></span>{{ __('Subjects') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-calendar-event me-2"></span>{{ __('Terms') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-journal-check me-2"></span>{{ __('Grading Systems') }}
                </a>
            </div>
        </div>

        @php($modulesActive = false)
        <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $modulesActive ? 'active' : '' }}"
            data-bs-toggle="collapse" href="#modulesMenu" role="button"
            aria-expanded="{{ $modulesActive ? 'true' : 'false' }}" aria-controls="modulesMenu">
            <span><span class="bi bi-boxes me-2"></span>{{ __('Modules') }}</span>
            <span class="bi bi-chevron-down small"></span>
        </a>
        <div class="collapse {{ $modulesActive ? 'show' : '' }}" id="modulesMenu">
            <div class="list-group list-group-flush ms-3">
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-cash-stack me-2"></span>{{ __('Financials') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-briefcase-fill me-2"></span>{{ __('Human Resource') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-journal-bookmark me-2"></span>{{ __('Library') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-shop me-2"></span>{{ __('Bookstore') }}
                </a>
                <a class="list-group-item list-group-item-action text-decoration-none" href="#">
                    <span class="bi bi-calendar-week me-2"></span>{{ __('Timetable') }}
                </a>
            </div>
        </div>

        <a class="list-group-item list-group-item-action" href="#">
            <span class="bi bi-calendar-check-fill me-2"></span>{{ __('Attendance') }}
        </a>

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
            <form method="POST" action="{{ route('logout') }}" class="m-0">
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

@extends('tenant.layouts.app')

@php
    $currentExamBody = null;

    if (\Illuminate\Support\Facades\Schema::hasTable('examination_bodies')) {
        try {
            $examBodyQuery = \App\Models\ExaminationBody::query();

            if (\Illuminate\Support\Facades\Schema::hasColumn('examination_bodies', 'is_current')) {
                $examBodyQuery->where('is_current', true);
            } elseif (\Illuminate\Support\Facades\Schema::hasColumn('examination_bodies', 'status')) {
                $examBodyQuery->where('status', 'current');
            }

            if (\Illuminate\Support\Facades\Schema::hasColumn('examination_bodies', 'updated_at')) {
                $examBodyQuery->orderByDesc('updated_at');
            }

            $currentExamBody = $examBodyQuery->first();

            if (!$currentExamBody) {
                $currentExamBody = \App\Models\ExaminationBody::orderByDesc('id')->first();
            }
        } catch (\Throwable $e) {
            $currentExamBody = null;
        }
    }
@endphp

@section('sidebar')
    <div class="card shadow-sm">
        <div class="card-header fw-semibold">{{ __('Staff menu') }}</div>
        <div class="list-group list-group-flush">
            <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.staff') ? 'active' : '' }}"
                href="{{ route('tenant.staff') }}" @if (request()->routeIs('tenant.staff')) aria-current="page" @endif>
                <span class="bi bi-speedometer me-2"></span>{{ __('Overview') }}
            </a>

            <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.settings.staff.*') ? 'active' : '' }}"
                href="{{ route('tenant.settings.staff.index') }}"
                @if (request()->routeIs('tenant.settings.staff.*')) aria-current="page" @endif>
                <span class="bi bi-gear me-2"></span>{{ __('Settings') }}
            </a>

            <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.profile.staff.*') ? 'active' : '' }}"
                href="{{ route('tenant.profile.staff.index') }}"
                @if (request()->routeIs('tenant.profile.staff.*')) aria-current="page" @endif>
                <span class="bi bi-person me-2"></span>{{ __('My Profile') }}
            </a>

            @canany(['manage attendance', 'view attendance'])
                <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.modules.attendance.*') ? 'active' : '' }}"
                    href="{{ route('tenant.modules.attendance.index') }}"
                    @if (request()->routeIs('tenant.modules.attendance.*')) aria-current="page" @endif>
                    <span class="bi bi-clipboard-check me-2"></span>{{ __('Attendance tracker') }}
                </a>
            @endcanany

            @canany(['manage timetable', 'view timetable'])
                <a class="list-group-item list-group-item-action {{ request()->routeIs('tenant.academics.timetable.*') ? 'active' : '' }}"
                    href="{{ route('tenant.academics.timetable.index') }}"
                    @if (request()->routeIs('tenant.academics.timetable.*')) aria-current="page" @endif>
                    <span class="bi bi-calendar-event me-2"></span>{{ __('Timetable') }}
                </a>
            @endcanany

            @php($academicsActive = request()->routeIs('tenant.academics.*'))
            <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-decoration-none {{ $academicsActive ? 'active' : '' }}"
                data-bs-toggle="collapse" href="#academicsMenuStaff" role="button"
                aria-expanded="{{ $academicsActive ? 'true' : 'false' }}" aria-controls="academicsMenuStaff">
                <span><span class="bi bi-mortarboard me-2"></span>{{ __('Academics') }}</span>
                <span class="bi bi-chevron-down small"></span>
            </a>
            <div class="collapse {{ $academicsActive ? 'show' : '' }}" id="academicsMenuStaff">
                <div class="list-group list-group-flush ms-3">
                    <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.classes.*') ? 'active' : '' }}"
                        href="{{ route('tenant.academics.classes.index') }}">
                        {{ __('Classes') }}
                    </a>
                    <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.class_streams.*') ? 'active' : '' }}"
                        href="{{ route('tenant.academics.class_streams.index') }}">
                        {{ __('Class streams') }}
                    </a>
                    <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.subjects.*') ? 'active' : '' }}"
                        href="{{ route('tenant.academics.subjects.index') }}">
                        {{ __('Subjects') }}
                    </a>
                    <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.timetable.*') ? 'active' : '' }}"
                        href="{{ route('tenant.academics.timetable.index') }}">
                        {{ __('Timetable') }}
                    </a>
                    <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.terms.*') ? 'active' : '' }}"
                        href="{{ route('tenant.academics.terms.index') }}">
                        {{ __('Terms') }}
                    </a>
                    @canany(['manage grades', 'view grades'])
                        <a class="list-group-item list-group-item-action text-decoration-none {{ request()->routeIs('tenant.academics.grades.*') ? 'active' : '' }}"
                            href="{{ route('tenant.academics.grades.index') }}">
                            {{ __('Grades') }}
                        </a>
                    @endcanany
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const trigger = document.querySelector('a[href="#academicsMenuStaff"][data-bs-toggle="collapse"]');
                    const menu = document.getElementById('academicsMenuStaff');
                    if (!trigger || !menu) return;
                    let hideTimer;
                    const showMenu = () => {
                        menu.classList.add('show');
                    };
                    const scheduleHide = () => {
                        hideTimer = setTimeout(() => menu.classList.remove('show'), 150);
                    };
                    const cancelHide = () => {
                        if (hideTimer) {
                            clearTimeout(hideTimer);
                            hideTimer = null;
                        }
                    };
                    trigger.addEventListener('mouseenter', () => {
                        cancelHide();
                        showMenu();
                    });
                    menu.addEventListener('mouseenter', cancelHide);
                    trigger.addEventListener('mouseleave', scheduleHide);
                    menu.addEventListener('mouseleave', scheduleHide);
                });
            </script>
        </div>
    </div>
@endsection

@section('content')
    <h1 class="h4 fw-semibold mb-3">{{ __('Staff dashboard') }}</h1>
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="small text-secondary">{{ __('Classes today') }}</div>
                    <div class="display-6">4</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="small text-secondary">{{ __('Assignments to grade') }}</div>
                    <div class="display-6">18</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="small text-secondary">{{ __('Messages') }}</div>
                    <div class="display-6">7</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="small text-secondary">{{ __('Exam body') }}</div>
                    <div class="fw-semibold">
                        {{ $currentExamBody ? $currentExamBody->name_translations[app()->getLocale()] ?? $currentExamBody->name : __('Not set') }}
                    </div>
                    @hasanyrole('Admin|Staff')
                        <a class="small d-inline-block mt-1"
                            href="{{ route('tenant.academics.examination_bodies.index') }}">{{ $currentExamBody ? __('Manage') : __('Set now') }}</a>
                    @endhasanyrole
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h2 class="h6 fw-semibold mb-3">{{ __('Quick links') }}</h2>
            <div class="d-flex flex-wrap gap-2 small">
                <a class="btn btn-outline-secondary btn-sm"
                    href="{{ route('tenant.storefront.home') }}">{{ __('Bookstore') }}</a>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="h6 fw-semibold mb-3">{{ __('Recent activity') }}</h2>
            <ul class="small mb-0">
                <li>{{ __('Submitted assignment: Physics Lab 2') }} · {{ now()->subHours(5)->diffForHumans() }}</li>
                <li>{{ __('Attendance taken for Form 3A') }} · {{ now()->subDay()->diffForHumans() }}</li>
            </ul>
        </div>
    </div>

    <!-- Bookstore Widget -->
    @include('tenant.components.bookstore.staff-widget')
@endsection

@extends('tenant.layouts.app')

@section('sidebar')
    @include('tenant.teacher._sidebar')
@endsection

@section('title', 'My Teaching Schedule')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h1 class="h3 fw-semibold mb-0">
                    <i class="bi bi-calendar-week me-2"></i>{{ __('My Teaching Schedule') }}
                </h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('tenant.teacher.timetable.today') }}" class="btn btn-outline-primary">
                        <i class="bi bi-calendar-day me-2"></i>{{ __('Today\'s Classes') }}
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(!$teacher)
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ __('You are not registered as a teacher in the system. Please contact your administrator.') }}
                </div>
            @endif
        </div>
    </div>

    @if($teacher)
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th width="120">{{ __('Day') }}</th>
                                        <th>{{ __('Classes to Teach') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $hasAnyClasses = false;
                                        foreach($schedule as $day => $slots) {
                                            if(count($slots) > 0) {
                                                $hasAnyClasses = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    
                                    @if(!$hasAnyClasses)
                                        <tr>
                                            <td colspan="2" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                                                    <p class="mt-3 mb-0">{{ __('No classes scheduled yet.') }}</p>
                                                    <small>{{ __('Contact your administrator to assign classes to your schedule.') }}</small>
                                                </div>
                                            </td>
                                        </tr>
                                    @else
                                        @foreach($schedule as $day => $slots)
                                            <tr>
                                                <td class="fw-semibold align-top">
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-calendar3 me-2 text-primary"></i>
                                                        {{ __($day) }}
                                                    </div>
                                                </td>
                                                <td>
                                                    @if(count($slots) > 0)
                                                        <div class="row g-2">
                                                            @foreach($slots as $slot)
                                                                <div class="col-md-6">
                                                                    <div class="card border-start border-success border-3 h-100">
                                                                        <div class="card-body p-3">
                                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                                <div>
                                                                                    <h6 class="mb-1 fw-bold">
                                                                                        <i class="bi bi-book me-2 text-success"></i>
                                                                                        {{ $slot['subject'] }}
                                                                                        @if($slot['subject_code'])
                                                                                            <small class="text-muted">({{ $slot['subject_code'] }})</small>
                                                                                        @endif
                                                                                    </h6>
                                                                                    <div class="small text-muted">
                                                                                        <i class="bi bi-people me-1"></i>
                                                                                        <strong>{{ __('Class:') }}</strong> {{ $slot['class'] }}
                                                                                        @if($slot['stream'])
                                                                                            <span class="badge bg-info ms-1">{{ $slot['stream'] }}</span>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                                <span class="badge bg-success">
                                                                                    {{ date('g:i A', strtotime($slot['start_time'])) }} - 
                                                                                    {{ date('g:i A', strtotime($slot['end_time'])) }}
                                                                                </span>
                                                                            </div>
                                                                            
                                                                            <div class="small text-muted">
                                                                                @if($slot['room'])
                                                                                    <div class="mb-1">
                                                                                        <i class="bi bi-door-open me-1"></i>
                                                                                        <strong>{{ __('Room:') }}</strong> {{ $slot['room'] }}
                                                                                    </div>
                                                                                @endif
                                                                                
                                                                                @if($slot['notes'])
                                                                                    <div class="mt-2 fst-italic border-top pt-2">
                                                                                        <i class="bi bi-sticky me-1"></i>
                                                                                        {{ $slot['notes'] }}
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="text-muted fst-italic">
                                                            <i class="bi bi-x-circle me-1"></i>
                                                            {{ __('No classes scheduled') }}
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-success">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-lightbulb text-success me-2"></i>
                            {{ __('Teaching Tips') }}
                        </h6>
                        <ul class="small mb-0">
                            <li>{{ __('Review lesson plans before each class') }}</li>
                            <li>{{ __('Arrive early to set up classroom') }}</li>
                            <li>{{ __('Check room number and materials needed') }}</li>
                            <li>{{ __('Keep attendance records up to date') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card border-primary">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="bi bi-info-circle text-primary me-2"></i>
                            {{ __('Schedule Information') }}
                        </h6>
                        <ul class="small mb-0">
                            <li>{{ __('This schedule is set by the school administrator') }}</li>
                            <li>{{ __('Contact admin for any schedule conflicts') }}</li>
                            <li>{{ __('Check "Today\'s Classes" for current day view') }}</li>
                            <li>{{ __('Note stream assignments for each class') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection



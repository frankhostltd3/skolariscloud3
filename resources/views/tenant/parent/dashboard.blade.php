@extends('tenant.layouts.app')

@section('sidebar')
  @include('tenant.parent._sidebar')
@endsection

@section('content')
@php
	$guardianName = auth()->user()->name ?? __('Guardian');
	$currentDate = now();
@endphp

<div class="card stats-card border-0 mb-4">
	<div class="card-body d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
		<div>
			<div class="text-white-50 small mb-1">{{ $currentDate->format('l, F j, Y') }}</div>
			<h2 class="fw-bold mb-1">{{ __('Welcome back, :name!', ['name' => $guardianName]) }}</h2>
			<p class="mb-0 text-white-75">{{ __('Here is your family overview for today.') }}</p>
		</div>
		<div class="text-lg-end">
			<div class="fw-bold fs-3" id="parent-dashboard-clock">{{ $currentDate->format('g:i A') }}</div>
			<div class="text-white-75 small">{{ __('Stay informed and engaged every day.') }}</div>
		</div>
	</div>
</div>

<div class="row g-3 mb-4">
	<div class="col-12 col-md-6 col-xl-3">
		<div class="card stats-card h-100">
			<div class="card-body d-flex align-items-center justify-content-between">
				<div>
					<div class="small text-white-75">{{ __('Children linked') }}</div>
					<div class="fs-3 fw-bold">{{ number_format($stats['wards']) }}</div>
				</div>
				<i class="fas fa-children fa-2x opacity-75"></i>
			</div>
		</div>
	</div>
	<div class="col-12 col-md-6 col-xl-3">
		<div class="card stats-card h-100">
			<div class="card-body d-flex align-items-center justify-content-between">
				<div>
					<div class="small text-white-75">{{ __('Outstanding fees') }}</div>
					<div class="fs-3 fw-bold">{{ format_money($stats['outstanding_fees'] ?? 0) }}</div>
				</div>
				<i class="fas fa-file-invoice-dollar fa-2x opacity-75"></i>
			</div>
		</div>
	</div>
	<div class="col-12 col-md-6 col-xl-3">
		<div class="card stats-card h-100">
			<div class="card-body d-flex align-items-center justify-content-between">
				<div>
					<div class="small text-white-75">{{ __('Average attendance') }}</div>
					<div class="fs-3 fw-bold">{{ $stats['average_attendance'] !== null ? number_format($stats['average_attendance'], 1) . '%' : __('N/A') }}</div>
					<small class="text-white-75">{{ $attendanceWindowLabel ?? __('Last 30 days') }}</small>
				</div>
				<i class="fas fa-calendar-check fa-2x opacity-75"></i>
			</div>
		</div>
	</div>
	<div class="col-12 col-md-6 col-xl-3">
		<div class="card stats-card h-100">
			<div class="card-body d-flex align-items-center justify-content-between">
				<div>
					<div class="small text-white-75">{{ __('Unread items') }}</div>
					<div class="fs-3 fw-bold">{{ number_format(($stats['unread_messages'] ?? 0) + ($stats['unread_notifications'] ?? 0)) }}</div>
					<small class="text-white-75">{{ __('Messages & alerts') }}</small>
				</div>
				<i class="fas fa-inbox fa-2x opacity-75"></i>
			</div>
		</div>
	</div>
</div>

<div class="card mb-4">
	<div class="card-header d-flex justify-content-between align-items-center">
		<h5 class="mb-0"><i class="fas fa-users me-2"></i>{{ __('Your children') }}</h5>
		<span class="small text-white-75">{{ __('Live academic and wellness snapshot') }}</span>
	</div>
	<div class="card-body">
		@if($wardSummaries->isEmpty())
			<div class="text-center py-4">
				<i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
				<p class="text-muted mb-0">{{ __('No students are linked to this parent account yet.') }}</p>
				<small class="text-muted">{{ __('Please contact school administration for assistance.') }}</small>
			</div>
		@else
			<div class="row g-3">
				@foreach($wardSummaries as $ward)
					@php
						$profile = $ward['profile'];
						$latestGrade = $ward['latest_grade'];
						$fees = $ward['fees'];
						$attendanceSummary = $ward['attendance_summary'];
						$attendancePercent = $ward['attendance_percentage'];
						$averageGrade = $ward['average_grade'];
						$presentLabel = $attendanceSummary['present'] + $attendanceSummary['late'];
					@endphp
					<div class="col-12 col-lg-6">
						<div class="border rounded-4 h-100 p-3">
							<div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mb-3">
								<div>
									<h5 class="fw-bold mb-1">{{ $profile->full_name ?? $profile->name }}</h5>
									<div class="text-muted small">
										{{ $ward['relationship'] ? ucfirst($ward['relationship']) . ' · ' : '' }}
										{{ $ward['class_name'] ?? __('Class pending') }}
										@if($ward['stream_name'])
											• {{ $ward['stream_name'] }}
										@endif
									</div>
								</div>
								<div class="text-sm-end">
									@if($averageGrade !== null)
										<span class="badge bg-success bg-opacity-10 text-success">{{ __('Avg') }} {{ number_format($averageGrade, 1) }}%</span>
									@else
										<span class="badge bg-secondary bg-opacity-10 text-secondary">{{ __('No grades yet') }}</span>
									@endif
									@if($ward['class_teacher'])
										<div class="small text-muted mt-1">{{ __('Class teacher:') }} {{ $ward['class_teacher'] }}</div>
									@endif
								</div>
							</div>

							<div class="mb-3">
								<div class="d-flex justify-content-between align-items-center">
									<span class="fw-semibold text-muted">{{ __('Attendance') }}</span>
									<span class="fw-semibold">{{ $attendancePercent !== null ? number_format($attendancePercent, 1) . '%' : __('No data') }}</span>
								</div>
								<div class="progress" style="height: 6px;">
									<div class="progress-bar bg-success" role="progressbar" style="width: {{ $attendancePercent !== null ? min($attendancePercent, 100) : 0 }}%"></div>
								</div>
								<div class="d-flex gap-3 mt-1 small text-muted">
									<span><i class="fas fa-check text-success me-1"></i>{{ $presentLabel }} {{ __('present/late') }}</span>
									<span><i class="fas fa-times text-danger me-1"></i>{{ $attendanceSummary['absent'] }} {{ __('absent') }}</span>
								</div>
							</div>

							<div class="mb-3">
								<div class="d-flex justify-content-between align-items-center mb-2">
									<span class="fw-semibold text-muted">{{ __('Latest assessment') }}</span>
									@if($latestGrade)
										@php
											$latestPercent = $latestGrade->total_marks > 0 ? round(($latestGrade->marks_obtained / $latestGrade->total_marks) * 100, 1) : null;
										@endphp
										<span class="badge bg-primary">{{ $latestPercent !== null ? $latestPercent . '%' : __('N/A') }}</span>
									@endif
								</div>
								@if($latestGrade)
									<div class="d-flex justify-content-between align-items-center">
										<div>
											<div class="fw-semibold">{{ $latestGrade->subject->name ?? __('Subject') }}</div>
											<small class="text-muted">{{ $latestGrade->assessment_type_display }}</small>
										</div>
										<div class="text-end">
											@if($latestGrade->grade_letter)
												<span class="badge bg-success-subtle text-success">{{ $latestGrade->grade_letter }}</span>
											@endif
											<div class="small text-muted">{{ optional($latestGrade->assessment_date)->format('M j, Y') }}</div>
										</div>
									</div>
								@else
									<div class="text-muted small">{{ __('No published grades yet.') }}</div>
								@endif
							</div>

							<div>
								<div class="fw-semibold text-muted mb-2">{{ __('Upcoming fees') }}</div>
								@if($fees->isEmpty())
									<div class="text-muted small">{{ __('No pending balances for this child.') }}</div>
								@else
									<ul class="list-unstyled small mb-0">
										@foreach($fees as $fee)
											<li class="d-flex justify-content-between align-items-center py-1 border-bottom border-light">
												<div>
													<div class="fw-semibold">{{ $fee['name'] }}</div>
													<div class="text-muted">
														{{ $fee['due_date'] ? $fee['due_date']->format('M j, Y') : __('No due date') }}
														• {{ $fee['status'] }}
													</div>
												</div>
												<span class="badge bg-warning bg-opacity-25 text-warning">{{ format_money($fee['balance']) }}</span>
											</li>
										@endforeach
									</ul>
								@endif
							</div>
						</div>
					</div>
				@endforeach
			</div>
		@endif
	</div>
</div>

<div class="row g-3 mb-4">
	<div class="col-12 col-xl-6">
		<div class="card h-100">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>{{ __('Fees & deadlines') }}</h5>
				<span class="badge bg-light text-dark">{{ __('Top reminders') }}</span>
			</div>
			<div class="card-body">
				@if($feesDue->isEmpty())
					<div class="text-center py-4">
						<i class="fas fa-check-circle fa-3x text-success mb-3"></i>
						<p class="text-muted mb-0">{{ __('No outstanding fees at the moment.') }}</p>
					</div>
				@else
					<div class="table-responsive">
						<table class="table table-sm align-middle">
							<thead>
								<tr class="text-muted small">
									<th>{{ __('Student') }}</th>
									<th>{{ __('Fee') }}</th>
									<th>{{ __('Due date') }}</th>
									<th class="text-end">{{ __('Balance') }}</th>
								</tr>
							</thead>
							<tbody>
								@foreach($feesDue as $item)
									<tr>
										<td class="fw-semibold">{{ $item['student_name'] }}</td>
										<td>{{ $item['fee_name'] }}</td>
										<td>
											@if($item['due_date'])
												<span class="badge {{ optional($item['due_date'])->isPast() ? 'bg-danger' : 'bg-primary' }}">{{ $item['due_date']->format('M j, Y') }}</span>
											@else
												<span class="badge bg-secondary">{{ __('TBD') }}</span>
											@endif
										</td>
										<td class="text-end fw-semibold">{{ format_money($item['balance']) }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
			</div>
		</div>
	</div>

	<div class="col-12 col-xl-6">
		<div class="card h-100">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i>{{ __('Upcoming school events') }}</h5>
				<span class="badge bg-light text-dark">{{ $stats['upcoming_events'] ?? 0 }}</span>
			</div>
			<div class="card-body">
				@if($upcomingEvents->isEmpty())
					<div class="text-center py-4">
						<i class="fas fa-sparkles fa-3x text-muted mb-3"></i>
						<p class="text-muted mb-0">{{ __('No events scheduled in the next days.') }}</p>
					</div>
				@else
					<div class="list-group list-group-flush">
						@foreach($upcomingEvents as $event)
							<div class="list-group-item px-0">
								<div class="d-flex justify-content-between align-items-start">
									<div>
										<div class="fw-semibold">{{ $event->title }}</div>
										<div class="text-muted small">{{ $event->formatted_date_range }}</div>
										@if($event->location)
											<div class="text-muted small"><i class="fas fa-location-dot me-1"></i>{{ $event->location }}</div>
										@endif
									</div>
									<span class="badge" style="background: {{ $event->priority_color }};">{{ ucfirst($event->priority ?? 'normal') }}</span>
								</div>
							</div>
						@endforeach
					</div>
				@endif
			</div>
		</div>
	</div>
</div>

<div class="row g-3 mb-4">
	<div class="col-12 col-xl-6">
		<div class="card h-100">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><i class="fas fa-star me-2"></i>{{ __('Latest academic updates') }}</h5>
				<span class="badge bg-light text-dark">{{ $recentGrades->count() }}</span>
			</div>
			<div class="card-body">
				@if($recentGrades->isEmpty())
					<div class="text-center py-4">
						<i class="fas fa-graduation-cap fa-3x text-muted mb-3"></i>
						<p class="text-muted mb-0">{{ __('Grades will appear here once published.') }}</p>
					</div>
				@else
					<div class="list-group list-group-flush">
						@foreach($recentGrades as $grade)
							<div class="list-group-item px-0">
								<div class="d-flex justify-content-between align-items-start gap-3">
									<div>
										<div class="fw-semibold">{{ $grade['student_name'] }}</div>
										<div class="text-muted small">{{ $grade['subject'] }} • {{ $grade['assessment_label'] }}</div>
										<div class="text-muted small">{{ optional($grade['recorded_at'])->format('M j, Y') }}</div>
									</div>
									<div class="text-end">
										@if($grade['grade_letter'])
											<span class="badge bg-success bg-opacity-25 text-success">{{ $grade['grade_letter'] }}</span>
										@endif
										<div class="fw-bold mt-1">{{ $grade['percentage'] !== null ? number_format($grade['percentage'], 1) . '%' : __('N/A') }}</div>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@endif
			</div>
		</div>
	</div>

	<div class="col-12 col-xl-6">
		<div class="card h-100">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><i class="fas fa-comments me-2"></i>{{ __('Messages & notices') }}</h5>
				<span class="badge bg-light text-dark">{{ $recentMessages->count() }}</span>
			</div>
			<div class="card-body">
				@if($recentMessages->isEmpty())
					<div class="text-center py-4">
						<i class="fas fa-envelope-open-text fa-3x text-muted mb-3"></i>
						<p class="text-muted mb-0">{{ __('No new messages right now.') }}</p>
					</div>
				@else
					<div class="list-group list-group-flush">
						@foreach($recentMessages as $recipient)
							@php
								$message = $recipient->message;
								$sender = $message?->sender;
							@endphp
							<div class="list-group-item px-0">
								<div class="d-flex justify-content-between align-items-start gap-3">
									<div>
										<div class="fw-semibold">{{ $sender?->name ?? __('School Staff') }}</div>
										<div class="text-muted small">{{ \Illuminate\Support\Str::limit($message?->content ?? __('Message unavailable'), 120) }}</div>
									</div>
									<div class="text-end small text-muted">{{ $recipient->created_at->diffForHumans() }}</div>
								</div>
							</div>
						@endforeach
					</div>
				@endif
			</div>
		</div>
	</div>
</div>

@if(isset($plans) && $plans->isNotEmpty())
	<div class="card mb-4">
		<div class="card-header d-flex justify-content-between align-items-center">
			<h5 class="mb-0"><i class="fas fa-seedling me-2"></i>{{ __('Skolaris packages') }}</h5>
			<span class="badge bg-light text-dark">{{ __('Learn more') }}</span>
		</div>
		<div class="card-body">
			<div class="row g-3">
				@foreach($plans as $plan)
					<div class="col-12 col-lg-4">
						<div class="border rounded-4 h-100 p-3 d-flex flex-column gap-2 @if($plan->is_highlighted) border-success @endif">
							@if($plan->is_highlighted)
								<span class="badge bg-success w-auto">{{ __('Most popular') }}</span>
							@endif
							<div class="d-flex justify-content-between align-items-start">
								<div>
									<h6 class="fw-bold mb-0">{{ $plan->name }}</h6>
									<small class="text-muted">{{ $plan->billing_period_label }}</small>
								</div>
								<span class="fw-semibold text-success">{{ $plan->display_price }}</span>
							</div>
							@if($plan->tagline)
								<p class="text-muted small mb-0">{{ $plan->tagline }}</p>
							@endif
							@if($plan->features_list !== [])
								<ul class="list-unstyled small d-grid gap-1 mb-0">
									@foreach($plan->features_list as $feature)
										<li class="d-flex align-items-start gap-2">
											<i class="fas fa-circle-check text-success mt-1"></i>
											<span>{{ $feature }}</span>
										</li>
									@endforeach
								</ul>
							@endif
							<div class="mt-auto pt-2">
								@php $subject = rawurlencode(__('Interested in :plan plan', ['plan' => $plan->name])); @endphp
								<a class="btn btn-outline-parent w-100" href="mailto:hello@skolariscloud.com?subject={{ $subject }}">
									<i class="fas fa-envelope me-1"></i>{{ __('Request details') }}
								</a>
							</div>
						</div>
					</div>
				@endforeach
			</div>
		</div>
	</div>
@endif
@endsection

@section('scripts')
<script>
	setInterval(function () {
		var now = new Date();
		var options = { hour: 'numeric', minute: '2-digit', hour12: true };
		var clock = document.getElementById('parent-dashboard-clock');
		if (clock) {
			clock.textContent = now.toLocaleTimeString('en-US', options);
		}
	}, 60000);
</script>
@endsection

<?php

namespace App\Services;

use App\Enums\UserType;
use App\Models\Academic\Enrollment;
use App\Models\Student as StudentProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class RegistrationPipelineService
{
    protected string $approvalMode;
    protected bool $studentTableExists;
    protected bool $enrollmentTableExists;
    protected bool $usersHasUserTypeColumn;
    protected bool $usersHasClassColumn;
    protected bool $usersHasApprovalColumn;

    public function __construct()
    {
        $connection = config('database.default', 'tenant');
        $schema = Schema::connection($connection);

        $this->studentTableExists = $schema->hasTable('students');
        $this->enrollmentTableExists = $schema->hasTable('enrollments');
        $this->usersHasUserTypeColumn = $schema->hasColumn('users', 'user_type');
        $this->usersHasClassColumn = $schema->hasColumn('users', 'class_id');
        $this->usersHasApprovalColumn = $schema->hasColumn('users', 'approval_status');

        $this->approvalMode = $this->resolveApprovalMode();
    }

    public function approvalMode(): string
    {
        return $this->approvalMode;
    }

    public function adminOverview(): array
    {
        $studentsQuery = $this->studentUserQuery();
        $totalStudents = (clone $studentsQuery)->count();

        $pendingApprovals = $this->usersHasApprovalColumn
            ? (clone $studentsQuery)->where('approval_status', 'pending')->count()
            : 0;

        $approvedStudents = $this->usersHasApprovalColumn
            ? (clone $studentsQuery)->where('approval_status', 'approved')->count()
            : $totalStudents;

        $emailVerified = (clone $studentsQuery)->whereNotNull('email_verified_at')->count();

        $placedStudents = $this->studentTableExists
            ? StudentProfile::query()->whereNotNull('class_id')->count()
            : 0;

        $awaitingPlacement = max(0, $approvedStudents - $placedStudents);

        $recentWindow = Carbon::now()->subDays(30);
        $recentSubmissions = (clone $studentsQuery)
            ->where('created_at', '>=', $recentWindow)
            ->count();

        $avgApprovalHours = null;
        if ($this->usersHasApprovalColumn) {
            $durations = (clone $studentsQuery)
                ->where('approval_status', 'approved')
                ->whereNotNull('approved_at')
                ->where('approved_at', '>=', $recentWindow)
                ->get(['created_at', 'approved_at'])
                ->map(function (User $user) {
                    if (! $user->created_at || ! $user->approved_at) {
                        return null;
                    }

                    return $user->created_at->diffInHours($user->approved_at);
                })
                ->filter();

            if ($durations->isNotEmpty()) {
                $avgApprovalHours = round($durations->avg(), 1);
            }
        }

        $latest = (clone $studentsQuery)
            ->latest('created_at')
            ->limit(5)
            ->get(['name', 'email', 'approval_status', 'created_at', 'email_verified_at']);

        $approvalRate = $totalStudents > 0
            ? round(($approvedStudents / $totalStudents) * 100, 1)
            : null;

        return [
            'stages' => [
                [
                    'key' => 'submitted',
                    'label' => __('Applications (30 days)'),
                    'description' => __('New student submissions'),
                    'primary' => number_format($recentSubmissions),
                    'status' => $recentSubmissions > 0 ? 'complete' : 'upcoming',
                ],
                [
                    'key' => 'pending',
                    'label' => __('Awaiting approval'),
                    'description' => __('Need admissions review'),
                    'primary' => number_format($pendingApprovals),
                    'status' => $pendingApprovals > 0 ? 'current' : 'complete',
                ],
                [
                    'key' => 'approved',
                    'label' => __('Approved accounts'),
                    'description' => __('Ready for activation'),
                    'primary' => number_format($approvedStudents),
                    'status' => $approvedStudents > 0 ? 'complete' : 'upcoming',
                ],
                [
                    'key' => 'placement',
                    'label' => __('Placed in classes'),
                    'description' => __('Students with class assignments'),
                    'primary' => number_format($placedStudents),
                    'status' => $placedStudents > 0 ? 'complete' : 'upcoming',
                ],
            ],
            'summary' => [
                [
                    'label' => __('Pending approvals'),
                    'value' => number_format($pendingApprovals),
                    'hint' => __('Awaiting review'),
                ],
                [
                    'label' => __('Awaiting placement'),
                    'value' => number_format($awaitingPlacement),
                    'hint' => __('Students to assign to classes'),
                ],
                [
                    'label' => __('Verified accounts'),
                    'value' => number_format($emailVerified),
                    'hint' => __('Students who confirmed email'),
                ],
            ],
            'latest' => $latest,
            'stats' => [
                'total' => $totalStudents,
                'pending' => $pendingApprovals,
                'approved' => $approvedStudents,
                'awaiting_placement' => $awaitingPlacement,
                'avg_approval_hours' => $avgApprovalHours,
                'approval_rate' => $approvalRate,
                'mode' => $this->approvalMode,
            ],
        ];
    }

    public function landingSummary(): array
    {
        $admin = $this->adminOverview();
        $stats = $admin['stats'];

        return [
            'stages' => $admin['stages'],
            'summary' => [
                [
                    'label' => __('Avg. approval time'),
                    'value' => $stats['avg_approval_hours']
                        ? $stats['avg_approval_hours'] . 'h'
                        : __('Same-day decisions'),
                    'hint' => __('Rolling 30 days'),
                ],
                [
                    'label' => __('Approval rate'),
                    'value' => $stats['approval_rate'] !== null
                        ? $stats['approval_rate'] . '%'
                        : __('N/A'),
                    'hint' => __('Students approved'),
                ],
                [
                    'label' => __('Waiting in queue'),
                    'value' => number_format($stats['pending'] ?? 0),
                    'hint' => __('Reviewed several times per day'),
                ],
            ],
            'stats' => $stats,
            'latest' => $admin['latest'],
        ];
    }

    public function studentTimeline(User $student, array $options = []): array
    {
        $profile = $this->findStudentProfile($student);
        $className = $options['class_name'] ?? null;

        if (! $className && $profile?->class) {
            $className = $profile->class->name;
        }

        $placed = $this->hasPlacement($profile, $student);
        $approvalStatus = $this->usersHasApprovalColumn
            ? ($student->approval_status ?? 'pending')
            : 'approved';
        $emailVerified = ! empty($student->email_verified_at);

        $stages = $this->buildIndividualStages([
            'submitted_at' => $student->created_at,
            'approval' => $approvalStatus,
            'email_verified' => $emailVerified,
            'placed' => $placed,
            'class_name' => $className,
        ]);

        $nextAction = $this->determineStudentNextAction($approvalStatus, $emailVerified, $placed);

        return [
            'stages' => $stages,
            'summary' => [
                [
                    'label' => __('Approval status'),
                    'value' => ucfirst($approvalStatus),
                ],
                [
                    'label' => __('Email verification'),
                    'value' => $emailVerified ? __('Verified') : __('Pending'),
                ],
                [
                    'label' => __('Class placement'),
                    'value' => $placed
                        ? ($className ? __('Assigned to :class', ['class' => $className]) : __('Assigned'))
                        : __('Pending'),
                ],
            ],
            'next_action' => $nextAction,
            'mode' => $this->approvalMode,
        ];
    }

    public function parentOverview(User $parent): array
    {
        $parent->loadMissing(['parentProfile.students.class']);
        $wards = $parent->parentProfile?->students ?? collect();

        $emails = $wards->pluck('email')->filter()->unique()->values();
        $studentUsers = $emails->isNotEmpty()
            ? $this->studentUserQuery()->whereIn('email', $emails)->get()->keyBy('email')
            : collect();

        $pending = 0;
        if ($this->usersHasApprovalColumn && $studentUsers->isNotEmpty()) {
            $pending = $studentUsers->where('approval_status', 'pending')->count();
        }

        $awaitingPlacement = $wards->where(fn ($student) => empty($student->class_id))->count();
        $linked = $wards->count();

        $nextAction = null;
        if ($linked === 0) {
            $nextAction = __('Add your child to begin the admissions review.');
        } elseif ($pending > 0) {
            $nextAction = trans_choice(
                'Admissions is reviewing :count application|Admissions is reviewing :count applications',
                $pending,
                ['count' => $pending]
            );
        } elseif ($awaitingPlacement > 0) {
            $nextAction = trans_choice(
                ':count student waiting for class placement|:count students waiting for class placement',
                $awaitingPlacement,
                ['count' => $awaitingPlacement]
            );
        }

        $stages = [
            [
                'key' => 'link',
                'label' => __('Submit student details'),
                'description' => __('Share basic enrollment data'),
                'primary' => number_format($linked),
                'status' => $linked > 0 ? 'complete' : 'current',
            ],
            [
                'key' => 'review',
                'label' => __('School review'),
                'description' => __('Admissions approval step'),
                'primary' => number_format($pending),
                'status' => $pending > 0 ? 'current' : ($linked > 0 ? 'complete' : 'upcoming'),
            ],
            [
                'key' => 'account',
                'label' => __('Portal access'),
                'description' => __('Students can log in after approval'),
                'primary' => number_format(max($linked - $pending, 0)),
                'status' => $linked - $pending > 0 ? 'complete' : 'upcoming',
            ],
            [
                'key' => 'placement',
                'label' => __('Class placement'),
                'description' => __('School assigns the classroom'),
                'primary' => number_format($linked - $awaitingPlacement),
                'status' => $awaitingPlacement > 0 ? 'current' : ($linked > 0 ? 'complete' : 'upcoming'),
            ],
        ];

        return [
            'stages' => $stages,
            'summary' => [
                [
                    'label' => __('Children linked'),
                    'value' => number_format($linked),
                ],
                [
                    'label' => __('Awaiting approval'),
                    'value' => number_format($pending),
                ],
                [
                    'label' => __('Pending placement'),
                    'value' => number_format($awaitingPlacement),
                ],
            ],
            'next_action' => $nextAction,
            'mode' => $this->approvalMode,
        ];
    }

    public function teacherOverview(User $teacher, Collection $classes): array
    {
        $classIds = $classes->pluck('id')->filter()->unique()->values();

        if ($classIds->isEmpty()) {
            return [
                'stages' => $this->buildTeacherStages(0, 0, 0, 0),
                'summary' => [],
                'recent' => collect(),
                'next_action' => __('Assign yourself to at least one class to track registrations.'),
                'mode' => $this->approvalMode,
            ];
        }

        $recentWindow = Carbon::now()->subDays(30);
        $recentStudents = collect();

        if ($this->usersHasClassColumn) {
            $baseQuery = $this->studentUserQuery()->whereIn('class_id', $classIds);

            $recentSubmissions = (clone $baseQuery)
                ->where('created_at', '>=', $recentWindow)
                ->count();

            $pendingApprovals = $this->usersHasApprovalColumn
                ? (clone $baseQuery)->where('approval_status', 'pending')->count()
                : 0;

            $approvedUnverified = $this->usersHasApprovalColumn
                ? (clone $baseQuery)
                    ->where('approval_status', 'approved')
                    ->whereNull('email_verified_at')
                    ->count()
                : 0;

            $readyStudents = $this->usersHasApprovalColumn
                ? (clone $baseQuery)
                    ->where('approval_status', 'approved')
                    ->whereNotNull('email_verified_at')
                    ->count()
                : (clone $baseQuery)->count();

            $recentStudents = (clone $baseQuery)
                ->orderByDesc('created_at')
                ->limit(6)
                ->get(['name', 'email', 'approval_status', 'created_at', 'email_verified_at']);
        } elseif ($this->studentTableExists) {
            $emails = StudentProfile::query()
                ->whereIn('class_id', $classIds)
                ->pluck('email')
                ->filter()
                ->unique()
                ->values();

            if ($emails->isEmpty()) {
                return [
                    'stages' => $this->buildTeacherStages(0, 0, 0, 0),
                    'summary' => [],
                    'recent' => collect(),
                    'next_action' => __('No student email addresses are stored for your classes yet.'),
                    'mode' => $this->approvalMode,
                ];
            }

            $baseQuery = $this->studentUserQuery()->whereIn('email', $emails);

            $recentSubmissions = (clone $baseQuery)
                ->where('created_at', '>=', $recentWindow)
                ->count();

            $pendingApprovals = $this->usersHasApprovalColumn
                ? (clone $baseQuery)->where('approval_status', 'pending')->count()
                : 0;

            $approvedUnverified = $this->usersHasApprovalColumn
                ? (clone $baseQuery)
                    ->where('approval_status', 'approved')
                    ->whereNull('email_verified_at')
                    ->count()
                : 0;

            $readyStudents = $this->usersHasApprovalColumn
                ? (clone $baseQuery)
                    ->where('approval_status', 'approved')
                    ->whereNotNull('email_verified_at')
                    ->count()
                : (clone $baseQuery)->count();

            $recentStudents = (clone $baseQuery)
                ->orderByDesc('created_at')
                ->limit(6)
                ->get(['name', 'email', 'approval_status', 'created_at', 'email_verified_at']);
        } else {
            return [
                'stages' => $this->buildTeacherStages(0, 0, 0, 0),
                'summary' => [],
                'recent' => collect(),
                'next_action' => __('Student records are not available yet.'),
                'mode' => $this->approvalMode,
            ];
        }

        $stages = $this->buildTeacherStages(
            $recentSubmissions,
            $pendingApprovals,
            $approvedUnverified,
            $readyStudents
        );

        $nextAction = null;
        if ($pendingApprovals > 0) {
            $nextAction = trans_choice(
                'Admissions is still reviewing :count learner assigned to you|Admissions is still reviewing :count learners assigned to you',
                $pendingApprovals,
                ['count' => $pendingApprovals]
            );
        } elseif ($approvedUnverified > 0) {
            $nextAction = trans_choice(
                'Remind :count student to verify their email|Remind :count students to verify their email',
                $approvedUnverified,
                ['count' => $approvedUnverified]
            );
        }

        return [
            'stages' => $stages,
            'summary' => [
                [
                    'label' => __('New this month'),
                    'value' => number_format($recentSubmissions),
                    'hint' => __('Students who joined your classes'),
                ],
                [
                    'label' => __('Awaiting approval'),
                    'value' => number_format($pendingApprovals),
                ],
                [
                    'label' => __('Ready to welcome'),
                    'value' => number_format($readyStudents),
                ],
            ],
            'recent' => $recentStudents,
            'next_action' => $nextAction,
            'mode' => $this->approvalMode,
        ];
    }

    protected function buildTeacherStages(int $recent, int $pending, int $awaitingVerification, int $ready): array
    {
        return [
            [
                'key' => 'submitted',
                'label' => __('Applications to your classes'),
                'description' => __('Last 30 days'),
                'primary' => number_format($recent),
                'status' => $recent > 0 ? 'complete' : 'upcoming',
            ],
            [
                'key' => 'pending',
                'label' => __('Pending approval'),
                'description' => __('Waiting for admin review'),
                'primary' => number_format($pending),
                'status' => $pending > 0 ? 'current' : 'complete',
            ],
            [
                'key' => 'verification',
                'label' => __('Need email verification'),
                'description' => __('Approved but not verified'),
                'primary' => number_format($awaitingVerification),
                'status' => $awaitingVerification > 0 ? 'current' : 'complete',
            ],
            [
                'key' => 'ready',
                'label' => __('Ready for onboarding'),
                'description' => __('Approved & verified'),
                'primary' => number_format($ready),
                'status' => $ready > 0 ? 'complete' : 'upcoming',
            ],
        ];
    }

    protected function studentUserQuery(): Builder
    {
        $query = User::query();

        if ($this->usersHasUserTypeColumn) {
            $query->where('user_type', UserType::STUDENT->value);
        } else {
            $query->whereHas('roles', function ($sub) {
                $sub->where('name', 'Student');
            });
        }

        return $query;
    }

    protected function resolveApprovalMode(): string
    {
        try {
            $mode = setting('user_approval_mode');
            return $mode ?: 'manual';
        } catch (\Throwable $e) {
            return 'manual';
        }
    }

    protected function findStudentProfile(User $student): ?StudentProfile
    {
        if (! $this->studentTableExists || empty($student->email)) {
            return null;
        }

        try {
            return StudentProfile::with('class')->where('email', $student->email)->first();
        } catch (\Throwable $e) {
            return null;
        }
    }

    protected function hasPlacement(?StudentProfile $profile, User $student): bool
    {
        if ($profile && $profile->class_id) {
            return true;
        }

        if (! $this->enrollmentTableExists) {
            return false;
        }

        try {
            return Enrollment::where('student_id', $student->id)
                ->where(function ($query) {
                    $query->whereNull('status')->orWhere('status', 'active');
                })
                ->exists();
        } catch (\Throwable $e) {
            return false;
        }
    }

    protected function buildIndividualStages(array $context): array
    {
        $approval = $context['approval'] ?? 'pending';
        $emailVerified = (bool) ($context['email_verified'] ?? false);
        $placed = (bool) ($context['placed'] ?? false);
        $submittedAt = $context['submitted_at'] ?? null;
        $className = $context['class_name'] ?? null;

        return [
            [
                'key' => 'submitted',
                'label' => __('Application submitted'),
                'description' => $submittedAt
                    ? __('Completed on :date', ['date' => $submittedAt->format('M j, Y')])
                    : __('Completed'),
                'primary' => $submittedAt ? $submittedAt->format('M j') : __('Now'),
                'status' => 'complete',
            ],
            [
                'key' => 'review',
                'label' => __('School review'),
                'description' => $approval === 'approved'
                    ? __('Approved and ready for onboarding')
                    : __('Admissions team is reviewing your application'),
                'primary' => ucfirst($approval),
                'status' => match ($approval) {
                    'approved' => 'complete',
                    'rejected' => 'blocked',
                    default => 'current',
                },
            ],
            [
                'key' => 'verification',
                'label' => __('Account security'),
                'description' => $emailVerified
                    ? __('Email verified')
                    : __('Verify your email to unlock every module'),
                'primary' => $emailVerified ? __('Verified') : __('Pending'),
                'status' => $emailVerified ? 'complete' : ($approval === 'approved' ? 'current' : 'upcoming'),
            ],
            [
                'key' => 'placement',
                'label' => __('Class placement'),
                'description' => $placed
                    ? ($className ? __('Assigned to :class', ['class' => $className]) : __('Assigned to a class'))
                    : __('We will notify you once a class is assigned'),
                'primary' => $placed ? __('Ready') : __('Pending'),
                'status' => $placed ? 'complete' : 'upcoming',
            ],
        ];
    }

    protected function determineStudentNextAction(string $approval, bool $emailVerified, bool $placed): ?string
    {
        if ($approval === 'pending') {
            return __('Admissions is reviewing your information. You will receive an email once approved.');
        }

        if ($approval === 'approved' && ! $emailVerified) {
            return __('Verify your email address to activate the student portal.');
        }

        if (! $placed) {
            return __('Finish updating your profile while we complete your class assignment.');
        }

        return null;
    }
}

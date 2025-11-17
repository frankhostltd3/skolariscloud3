<?php

namespace App\Http\Controllers\Tenant\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\BillingPlan;
use App\Models\Event;
use App\Models\Fee;
use App\Models\Finance\FeePayment;
use App\Models\Grade;
use App\Models\MessageRecipient;
use App\Models\Student as StudentProfile;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile()->with([
            'students' => function ($query) {
                $query->with(['class.classTeacher', 'stream']);
            },
        ])->first();

        $wards = $parentProfile?->students ?? collect();
        $plans = BillingPlan::query()->active()->ordered()->get();

        $stats = [
            'wards' => $wards->count(),
            'average_attendance' => null,
            'average_grade' => null,
            'outstanding_fees' => 0.0,
            'unread_messages' => MessageRecipient::forRecipient($user->id)->unread()->count(),
            'unread_notifications' => 0,
            'upcoming_events' => 0,
        ];

        try {
            $stats['unread_notifications'] = $user->unreadNotifications()->count();
        } catch (\Throwable $e) {
            $stats['unread_notifications'] = 0;
        }

        $attendanceWindowStart = now()->subDays(30)->startOfDay();

        if ($wards->isEmpty()) {
            $upcomingEvents = $this->loadUpcomingEvents();
            $recentMessages = $this->loadRecentMessages($user->id);

            return view('tenant.parent.dashboard', [
                'parentProfile' => $parentProfile,
                'wardSummaries' => collect(),
                'stats' => $stats,
                'recentGrades' => collect(),
                'upcomingEvents' => $upcomingEvents,
                'feesDue' => collect(),
                'recentMessages' => $recentMessages,
                'plans' => $plans,
                'attendanceWindowLabel' => null,
            ]);
        }

        $wardsById = $wards->keyBy('id');
        $wardsByClassId = $wards->groupBy('class_id');
        $wardEmails = $wards->pluck('email')->filter()->unique()->values();

        $wardUsers = $wardEmails->isNotEmpty()
            ? User::whereIn('email', $wardEmails)->get()->keyBy('email')
            : collect();

        $studentUserMap = [];
        $userIdToStudent = [];

        foreach ($wards as $studentProfile) {
            $studentUser = $studentProfile->email ? $wardUsers->get($studentProfile->email) : null;
            if ($studentUser) {
                $studentUserMap[$studentProfile->id] = $studentUser;
                $userIdToStudent[$studentUser->id] = $studentProfile;
            }
        }

        $wardUserIds = array_values(array_unique(array_map(static function ($user) {
            return $user->id;
        }, $studentUserMap ? array_values($studentUserMap) : [])));

        $attendanceAggregates = $this->loadAttendanceAggregates($wardUserIds, $attendanceWindowStart);
        $gradeSnapshots = $this->loadGradeSnapshots($wardUserIds);
        $gradeAverages = $gradeSnapshots['averages'];
        $gradesByStudent = $gradeSnapshots['by_student'];
        $recentGrades = $gradeSnapshots['recent'];

        $feeSnapshot = $this->loadFeeSnapshots(
            $wards,
            $wardsByClassId,
            $studentUserMap,
            $wardUserIds
        );

        $feesByStudent = $feeSnapshot['by_student'];
        $feesDue = $feeSnapshot['summary'];
        $stats['outstanding_fees'] = $feeSnapshot['total'];

        $attendancePercentages = $attendanceAggregates->map(function ($item) {
            $effectiveTotal = (int) $item->total_count;
            if ($effectiveTotal === 0) {
                return null;
            }

            $positive = (int) $item->present_count + (int) $item->late_count;
            return round(($positive / $effectiveTotal) * 100, 1);
        })->filter();

        if ($attendancePercentages->isNotEmpty()) {
            $stats['average_attendance'] = round($attendancePercentages->avg(), 1);
        }

        if ($gradeAverages->isNotEmpty()) {
            $stats['average_grade'] = round($gradeAverages->avg(), 1);
        }

        $upcomingEvents = $this->loadUpcomingEvents();
        $stats['upcoming_events'] = $upcomingEvents->count();
        $recentMessages = $this->loadRecentMessages($user->id);

        $wardSummaries = $wards->map(function (StudentProfile $student) use (
            $studentUserMap,
            $attendanceAggregates,
            $gradeAverages,
            $gradesByStudent,
            $feesByStudent
        ) {
            $studentUser = $studentUserMap[$student->id] ?? null;
            $attendanceStat = $studentUser ? $attendanceAggregates->get($studentUser->id) : null;

            $attendancePercentage = null;
            if ($attendanceStat && (int) $attendanceStat->total_count > 0) {
                $positive = (int) $attendanceStat->present_count + (int) $attendanceStat->late_count;
                $attendancePercentage = round(($positive / (int) $attendanceStat->total_count) * 100, 1);
            }

            $averageGrade = $studentUser ? ($gradeAverages[$studentUser->id] ?? null) : null;
            $latestGrade = $studentUser ? optional($gradesByStudent->get($studentUser->id))->first() : null;
            $childFees = $feesByStudent->get($student->id) ?? collect();

            return [
                'profile' => $student,
                'user' => $studentUser,
                'relationship' => $student->pivot?->relationship,
                'class_name' => optional($student->class)->name,
                'class_teacher' => optional($student->class?->classTeacher)->name,
                'stream_name' => optional($student->stream)->name,
                'attendance_percentage' => $attendancePercentage,
                'attendance_summary' => $attendanceStat ? [
                    'present' => (int) $attendanceStat->present_count,
                    'late' => (int) $attendanceStat->late_count,
                    'absent' => (int) $attendanceStat->absent_count,
                    'excused' => (int) $attendanceStat->excused_count,
                    'total' => (int) $attendanceStat->total_count,
                ] : [
                    'present' => 0,
                    'late' => 0,
                    'absent' => 0,
                    'excused' => 0,
                    'total' => 0,
                ],
                'average_grade' => $averageGrade,
                'latest_grade' => $latestGrade,
                'fees' => $childFees->take(3),
            ];
        })->values();

        $recentGradeEntries = $recentGrades->map(function (Grade $grade) use ($userIdToStudent) {
            $studentProfile = $userIdToStudent[$grade->student_id] ?? null;

            return [
                'id' => $grade->id,
                'student_name' => $studentProfile?->full_name ?? $studentProfile?->name ?? optional($grade->student)->name ?? 'Student',
                'subject' => optional($grade->subject)->name ?? 'Subject',
                'percentage' => $grade->total_marks > 0 ? round(($grade->marks_obtained / $grade->total_marks) * 100, 1) : null,
                'grade_letter' => $grade->grade_letter,
                'assessment_label' => $grade->assessment_type_display,
                'recorded_at' => $grade->assessment_date ?? $grade->created_at,
            ];
        })->values();

        $attendanceWindowLabel = $attendanceWindowStart->format('M j, Y') . ' - ' . now()->format('M j, Y');

        return view('tenant.parent.dashboard', [
            'parentProfile' => $parentProfile,
            'wardSummaries' => $wardSummaries,
            'stats' => $stats,
            'recentGrades' => $recentGradeEntries,
            'upcomingEvents' => $upcomingEvents,
            'feesDue' => $feesDue,
            'recentMessages' => $recentMessages,
            'plans' => $plans,
            'attendanceWindowLabel' => $attendanceWindowLabel,
        ]);
    }

    protected function loadAttendanceAggregates(array $wardUserIds, $windowStart): Collection
    {
        if (empty($wardUserIds)) {
            return collect();
        }

        return Attendance::select(
            'student_id',
            DB::raw("SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present_count"),
            DB::raw("SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late_count"),
            DB::raw("SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent_count"),
            DB::raw("SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused_count"),
            DB::raw('COUNT(*) as total_count')
        )
        ->whereIn('student_id', $wardUserIds)
        ->whereBetween('attendance_date', [$windowStart, now()])
        ->groupBy('student_id')
        ->get()
        ->keyBy('student_id');
    }

    protected function loadGradeSnapshots(array $wardUserIds): array
    {
        if (empty($wardUserIds)) {
            return [
                'averages' => collect(),
                'by_student' => collect(),
                'recent' => collect(),
            ];
        }

        $averages = Grade::select(
            'student_id',
            DB::raw(
                "AVG(CASE WHEN total_marks > 0 THEN (CAST(marks_obtained AS REAL) / total_marks) * 100 ELSE NULL END) as avg_percentage"
            )
        )
        ->whereIn('student_id', $wardUserIds)
        ->whereNotNull('marks_obtained')
        ->whereNotNull('total_marks')
        ->where('total_marks', '>', 0)
        ->groupBy('student_id')
        ->get()
        ->mapWithKeys(function ($row) {
            return [(int) $row->student_id => round((float) $row->avg_percentage, 2)];
        });

        $recentGrades = Grade::with(['subject', 'student'])
            ->whereIn('student_id', $wardUserIds)
            ->orderByDesc('assessment_date')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $gradesByStudent = $recentGrades->groupBy('student_id');

        return [
            'averages' => $averages,
            'by_student' => $gradesByStudent,
            'recent' => $recentGrades->take(12),
        ];
    }

    protected function loadFeeSnapshots($wards, $wardsByClassId, array $studentUserMap, array $wardUserIds): array
    {
        $wardIds = $wards->pluck('id')->all();
        $wardClassIds = $wards->pluck('class_id')->filter()->unique()->values()->all();

        $feesByStudent = collect();
        $summary = collect();
        $total = 0.0;

        if (empty($wardIds) && empty($wardClassIds)) {
            return [
                'by_student' => $feesByStudent,
                'summary' => $summary,
                'total' => $total,
            ];
        }

        $fees = Fee::query()
            ->where(function ($query) use ($wardIds, $wardClassIds) {
                $query->whereHas('assignments', function ($q) use ($wardIds) {
                    $q->where('assignment_type', 'student')
                        ->whereIn('student_id', $wardIds)
                        ->where('is_active', true);
                });

                if (!empty($wardClassIds)) {
                    $query->orWhereHas('assignments', function ($q) use ($wardClassIds) {
                        $q->where('assignment_type', 'class')
                            ->whereIn('class_id', $wardClassIds)
                            ->where('is_active', true);
                    });
                }
            })
            ->with(['assignments' => function ($q) use ($wardIds, $wardClassIds) {
                $q->where('is_active', true)
                    ->where(function ($inner) use ($wardIds, $wardClassIds) {
                        $inner->where(function ($qq) use ($wardIds) {
                            $qq->where('assignment_type', 'student')
                                ->whereIn('student_id', $wardIds);
                        });

                        if (!empty($wardClassIds)) {
                            $inner->orWhere(function ($qq) use ($wardClassIds) {
                                $qq->where('assignment_type', 'class')
                                    ->whereIn('class_id', $wardClassIds);
                            });
                        }
                    });
            }])
            ->orderBy('due_date')
            ->get();

        if ($fees->isEmpty()) {
            return [
                'by_student' => $feesByStudent,
                'summary' => $summary,
                'total' => $total,
            ];
        }

        $paymentLookup = [];
        if (!empty($wardUserIds)) {
            $payments = FeePayment::whereIn('student_id', $wardUserIds)
                ->where('status', 'confirmed')
                ->get();

            foreach ($payments as $payment) {
                $feeIds = data_get($payment->meta, 'fee_id');
                if (is_null($feeIds)) {
                    $feeIds = data_get($payment->meta, 'fee.id');
                }

                if (!is_array($feeIds)) {
                    $feeIds = array_filter([$feeIds]);
                }

                foreach ($feeIds as $feeId) {
                    if (!$feeId) {
                        continue;
                    }

                    if (!isset($paymentLookup[$payment->student_id][$feeId])) {
                        $paymentLookup[$payment->student_id][$feeId] = 0.0;
                    }

                    $paymentLookup[$payment->student_id][$feeId] += (float) $payment->amount;
                }
            }
        }

        $feesCollected = [];

        foreach ($fees as $fee) {
            foreach ($fee->assignments as $assignment) {
                $targetStudentIds = [];

                if ($assignment->assignment_type === 'student' && $assignment->student_id) {
                    $targetStudentIds[] = $assignment->student_id;
                }

                if ($assignment->assignment_type === 'class' && $assignment->class_id) {
                    $classStudents = $wardsByClassId->get($assignment->class_id);
                    if ($classStudents) {
                        $targetStudentIds = array_merge($targetStudentIds, $classStudents->pluck('id')->all());
                    }
                }

                foreach ($targetStudentIds as $studentId) {
                    $studentUser = $studentUserMap[$studentId] ?? null;
                    if (!$studentUser) {
                        continue;
                    }

                    $collectionKey = $studentUser->id . ':' . $fee->id;
                    if (isset($feesCollected[$collectionKey])) {
                        continue;
                    }
                    $feesCollected[$collectionKey] = true;

                    $assignedAmount = (float) ($fee->amount ?? 0.0);
                    $paid = $paymentLookup[$studentUser->id][$fee->id] ?? 0.0;
                    $balance = max(0.0, $assignedAmount - $paid);

                    if ($balance <= 0) {
                        continue;
                    }

                    $total += $balance;

                    $feesByStudent[$studentId][] = [
                        'fee_id' => $fee->id,
                        'name' => $fee->name,
                        'due_date' => $fee->due_date,
                        'balance' => $balance,
                        'assigned_amount' => $assignedAmount,
                        'paid' => $paid,
                        'status' => $fee->getStatusText(),
                        'category' => $fee->category,
                    ];

                    $summary->push([
                        'student_id' => $studentId,
                        'student_name' => optional($wards->firstWhere('id', $studentId))->full_name
                            ?? optional($wards->firstWhere('id', $studentId))->name
                            ?? 'Student',
                        'fee_name' => $fee->name,
                        'due_date' => $fee->due_date,
                        'balance' => $balance,
                        'status' => $fee->getStatusText(),
                    ]);
                }
            }
        }

        $feesByStudent = collect($feesByStudent)->map(function ($entries) {
            return collect($entries)->sortBy(function ($entry) {
                return optional($entry['due_date'])->timestamp ?? PHP_INT_MAX;
            })->values();
        });

        $summary = $summary
            ->sortBy(function ($entry) {
                return optional($entry['due_date'])->timestamp ?? PHP_INT_MAX;
            })
            ->take(8)
            ->values();

        return [
            'by_student' => $feesByStudent,
            'summary' => $summary,
            'total' => round($total, 2),
        ];
    }

    protected function loadUpcomingEvents(): Collection
    {
        return Event::active()
            ->where('start_date', '>=', now()->subDay())
            ->orderBy('start_date')
            ->limit(12)
            ->get()
            ->filter(function (Event $event) {
                $audience = $event->target_audience;

                if (is_null($audience) || $audience === [] || $audience === '[]') {
                    return true;
                }

                if (is_string($audience)) {
                    $decoded = json_decode($audience, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $audience = $decoded;
                    }
                }

                if (!is_array($audience)) {
                    return false;
                }

                return in_array('parents', $audience, true) || in_array('students', $audience, true);
            })
            ->take(6)
            ->values();
    }

    protected function loadRecentMessages(int $recipientId): Collection
    {
        return MessageRecipient::with(['message.sender', 'message.thread'])
            ->forRecipient($recipientId)
            ->latest('created_at')
            ->limit(5)
            ->get();
    }
}

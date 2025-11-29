<?php

namespace App\Http\Controllers\Tenant\Parent;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\AttendanceRecord;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\MessageRecipient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $parent = $user->parentProfile;

        if (!$parent) {
            return view('tenant.parent.dashboard', [
                'wardSummaries' => collect(),
                'feesDue' => collect(),
                'upcomingEvents' => collect(),
                'recentGrades' => collect(),
                'recentMessages' => collect(),
                'stats' => [
                    'average_attendance' => null,
                    'unread_messages' => 0,
                    'unread_notifications' => 0,
                    'upcoming_events' => 0,
                ],
                'plans' => collect(),
                'students' => collect(), // Keep for compatibility if needed
                'announcements' => collect(), // Keep for compatibility if needed
            ]);
        }

        // Eager load relationships
        $students = $parent->students()
            ->with([
                'class.teacher',
                'stream',
                'account.grades.subject',
                'account.attendanceRecords',
                'account.invoices.feeStructure',
                'account.invoices.payments'
            ])
            ->get();

        $wardSummaries = $students->map(function ($student) use ($parent) {
            $account = $student->account;

            // Attendance
            $attendanceRecords = $account ? $account->attendanceRecords : collect();
            $totalAttendance = $attendanceRecords->count();
            $present = $attendanceRecords->where('status', 'present')->count();
            $late = $attendanceRecords->where('status', 'late')->count();
            $absent = $attendanceRecords->where('status', 'absent')->count();
            $attendancePercentage = $totalAttendance > 0 ? (($present + $late) / $totalAttendance) * 100 : null;

            // Grades
            $grades = $account ? $account->grades : collect();
            $latestGrade = $grades->sortByDesc('assessment_date')->first();
            $averageGrade = $grades->avg('marks_obtained');

            // Fees
            $invoices = $account ? $account->invoices : collect();
            $pendingFees = $invoices->where('balance', '>', 0)->map(function ($invoice) {
                return [
                    'name' => $invoice->feeStructure->name ?? 'Fee',
                    'due_date' => $invoice->due_date,
                    'status' => $invoice->status,
                    'balance' => $invoice->balance,
                ];
            });

            return [
                'profile' => $student,
                'relationship' => $student->pivot->relationship ?? 'Child',
                'class_name' => $student->class->name ?? null,
                'stream_name' => $student->stream->name ?? null,
                'class_teacher' => $student->class->teacher->name ?? null,
                'latest_grade' => $latestGrade,
                'average_grade' => $averageGrade,
                'attendance_summary' => [
                    'present' => $present,
                    'late' => $late,
                    'absent' => $absent,
                ],
                'attendance_percentage' => $attendancePercentage,
                'fees' => $pendingFees,
            ];
        });

        // Fees Due (Top reminders)
        $feesDue = collect();
        foreach ($wardSummaries as $ward) {
            foreach ($ward['fees'] as $fee) {
                $feesDue->push([
                    'student_name' => $ward['profile']->name,
                    'fee_name' => $fee['name'],
                    'due_date' => $fee['due_date'],
                    'balance' => $fee['balance'],
                ]);
            }
        }
        $feesDue = $feesDue->sortBy('due_date')->take(5);

        // Recent Grades
        $recentGrades = collect();
        foreach ($students as $student) {
            if ($student->account) {
                foreach ($student->account->grades as $grade) {
                    $recentGrades->push([
                        'student_name' => $student->name,
                        'subject' => $grade->subject->name ?? 'Subject',
                        'assessment_label' => $grade->assessment_type ?? 'Assessment',
                        'recorded_at' => $grade->created_at,
                        'grade_letter' => $grade->grade_letter,
                        'percentage' => $grade->marks_obtained,
                    ]);
                }
            }
        }
        $recentGrades = $recentGrades->sortByDesc('recorded_at')->take(5);

        // Upcoming Events - wrap in try-catch in case table doesn't exist
        try {
            $upcomingEvents = Event::where('start_date', '>=', now())
                ->orderBy('start_date')
                ->take(5)
                ->get()
                ->map(function ($event) {
                    $event->formatted_date_range = $event->start_date->format('M d') . ' - ' . $event->end_date->format('M d');
                    $event->priority_color = match($event->priority) {
                        'high' => '#dc3545',
                        'medium' => '#ffc107',
                        default => '#0d6efd',
                    };
                    return $event;
                });
        } catch (\Exception $e) {
            $upcomingEvents = collect();
        }

        // Recent Messages - wrap in try-catch in case table doesn't exist
        try {
            $recentMessages = MessageRecipient::where('recipient_id', $user->id)
                ->with('message.sender')
                ->latest()
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            $recentMessages = collect();
        }

        $stats = [
            'upcoming_events' => $upcomingEvents->count(),
            'average_attendance' => $wardSummaries->avg('attendance_percentage'),
            'unread_messages' => $recentMessages->whereNull('read_at')->count(),
            'unread_notifications' => $user->unreadNotifications()->count(),
        ];

        $plans = collect();

        return view('tenant.parent.dashboard', compact(
            'wardSummaries',
            'feesDue',
            'upcomingEvents',
            'recentGrades',
            'recentMessages',
            'stats',
            'plans',
            'students'
        ));
    }
}

<?php

namespace App\Http\Controllers\Tenant\Guardian;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $user = Auth::user();
        $parentProfile = $user->parentProfile()->with([
            'students' => function ($query) {
                $query->with(['class.classTeacher', 'stream']);
            },
        ])->first();

        $wards = $parentProfile?->students ?? collect();
        $dateRange = $this->resolveDateRange($request);
        $selectedWardId = (int) $request->input('student_id', 0);

        if ($wards->isEmpty()) {
            return view('tenant.parent.attendance.index', [
                'parentProfile' => $parentProfile,
                'wards' => $wards,
                'selectedWard' => null,
                'selectedWardUser' => null,
                'attendanceRecords' => collect(),
                'summary' => $this->summarizeAttendance(collect()),
                'trend' => collect(),
                'dateFilters' => $dateRange,
            ]);
        }

        if ($selectedWardId !== 0 && !$wards->contains('id', $selectedWardId)) {
            abort(403, __('You are not authorized to view this student.'));
        }

        $selectedWard = $selectedWardId !== 0
            ? $wards->firstWhere('id', $selectedWardId)
            : $wards->first();

        $wardUsers = $this->resolveWardUsers($wards);
        $selectedWardUser = $selectedWard ? $wardUsers->get($selectedWard->id) : null;

        if (!$selectedWardUser) {
            return view('tenant.parent.attendance.index', [
                'parentProfile' => $parentProfile,
                'wards' => $wards,
                'selectedWard' => $selectedWard,
                'selectedWardUser' => null,
                'attendanceRecords' => collect(),
                'summary' => $this->summarizeAttendance(collect()),
                'trend' => collect(),
                'dateFilters' => $dateRange,
            ]);
        }

        $attendanceRecords = Attendance::query()
            ->with(['class', 'markedBy'])
            ->where('student_id', $selectedWardUser->id)
            ->whereBetween('attendance_date', [$dateRange['start'], $dateRange['end']])
            ->orderByDesc('attendance_date')
            ->get();

        return view('tenant.parent.attendance.index', [
            'parentProfile' => $parentProfile,
            'wards' => $wards,
            'selectedWard' => $selectedWard,
            'selectedWardUser' => $selectedWardUser,
            'attendanceRecords' => $attendanceRecords,
            'summary' => $this->summarizeAttendance($attendanceRecords),
            'trend' => $this->buildTrend($attendanceRecords),
            'dateFilters' => $dateRange,
        ]);
    }

    protected function resolveWardUsers(Collection $wards): Collection
    {
        $emails = $wards->pluck('email')->filter()->unique()->values();

        $users = $emails->isNotEmpty()
            ? User::whereIn('email', $emails)->get()->keyBy('email')
            : collect();

        $map = [];

        foreach ($wards as $student) {
            if (!$student->email) {
                continue;
            }

            $user = $users->get($student->email);
            if ($user) {
                $map[$student->id] = $user;
            }
        }

        return collect($map);
    }

    protected function resolveDateRange(Request $request): array
    {
        $allowedWindows = [7, 30, 60, 90];
        $defaultDays = (int) $request->input('days', 30);
        $days = in_array($defaultDays, $allowedWindows, true) ? $defaultDays : 30;

        $start = null;
        $end = null;

        if ($request->filled('start_date')) {
            $start = $this->parseDate($request->input('start_date'))->startOfDay();
        }

        if ($request->filled('end_date')) {
            $end = $this->parseDate($request->input('end_date'))->endOfDay();
        }

        if (!$start) {
            $start = now()->copy()->subDays($days)->startOfDay();
        }

        if (!$end) {
            $end = now()->copy()->endOfDay();
        }

        if ($start->greaterThan($end)) {
            [$start, $end] = [$end->copy()->subDays($days)->startOfDay(), $end];
        }

        return [
            'start' => $start,
            'end' => $end,
            'days' => $days,
        ];
    }

    protected function summarizeAttendance(Collection $records): array
    {
        $totalDays = $records->count();

        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $late = $records->where('status', 'late')->count();
        $excused = $records->where('status', 'excused')->count();

        $positive = $present + $late;
        $percentage = $totalDays > 0 ? round(($positive / $totalDays) * 100, 1) : null;

        return [
            'total_days' => $totalDays,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'percentage' => $percentage,
        ];
    }

    protected function buildTrend(Collection $records): Collection
    {
        if ($records->isEmpty()) {
            return collect();
        }

        return $records
            ->groupBy(fn ($record) => $record->attendance_date->format('Y-m-d'))
            ->map(function (Collection $items) {
                $date = $items->first()->attendance_date->copy();
                $present = $items->where('status', 'present')->count();
                $late = $items->where('status', 'late')->count();
                $absent = $items->where('status', 'absent')->count();
                $excused = $items->where('status', 'excused')->count();
                $total = $items->count();
                $percentage = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

                return [
                    'date' => $date,
                    'present' => $present,
                    'late' => $late,
                    'absent' => $absent,
                    'excused' => $excused,
                    'percentage' => $percentage,
                ];
            })
            ->sortKeys()
            ->values();
    }

    protected function parseDate(string $value): Carbon
    {
        try {
            return Carbon::parse($value);
        } catch (Throwable $exception) {
            return now();
        }
    }
}

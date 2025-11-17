<?php

namespace App\Http\Controllers\Tenant\Student;

use App\Http\Controllers\Controller;
use App\Models\TimetableEntry;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Get the student record linked to this user via email
        $student = Student::where('email', $user->email)->first();

        $schedule = [];
        $class = null;
        $stream = null;

        if ($student && $student->class_id) {
            $class = $student->class;
            $stream = $student->stream;

            // Fetch timetable entries for this student's class
            $query = TimetableEntry::with(['subject', 'teacher', 'class', 'stream'])
                ->where('class_id', $student->class_id);

            // If student is in a specific stream, filter by that stream or entries without stream
            if ($student->class_stream_id) {
                $query->where(function($q) use ($student) {
                    $q->where('class_stream_id', $student->class_stream_id)
                      ->orWhereNull('class_stream_id');
                });
            } else {
                // No stream - show entries without stream specification
                $query->whereNull('class_stream_id');
            }

            $entries = $query->orderBy('day_of_week')
                ->orderBy('starts_at')
                ->get();

            // Group by day of week
            $dayNames = [
                1 => 'Monday',
                2 => 'Tuesday',
                3 => 'Wednesday',
                4 => 'Thursday',
                5 => 'Friday',
                6 => 'Saturday',
                7 => 'Sunday'
            ];

            foreach ($entries as $entry) {
                $dayName = $dayNames[$entry->day_of_week] ?? 'Unknown';
                $schedule[$dayName][] = [
                    'subject' => $entry->subject->name ?? 'N/A',
                    'subject_code' => $entry->subject->code ?? '',
                    'start_time' => $entry->starts_at,
                    'end_time' => $entry->ends_at,
                    'room' => $entry->room,
                    'teacher' => $entry->teacher ? $entry->teacher->first_name . ' ' . $entry->teacher->last_name : 'N/A',
                    'notes' => $entry->notes,
                ];
            }
        }

        // Order days Mon..Sun
        $orderedDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $scheduleOrdered = [];
        foreach ($orderedDays as $d) {
            $scheduleOrdered[$d] = $schedule[$d] ?? [];
        }

        return view('tenant.student.schedule.index', [
            'student' => $student,
            'class' => $class,
            'stream' => $stream,
            'schedule' => $scheduleOrdered,
        ]);
    }

    /**
     * Export the student's class timetable as an iCalendar (.ics) file.
     */
    public function exportIcs()
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student || !$student->class_id) {
            return redirect()->route('tenant.student.schedule.index')->with('warning', 'No class assigned. Timetable export unavailable.');
        }

        // Fetch timetable entries
        $query = TimetableEntry::with(['subject', 'teacher', 'class', 'stream'])
            ->where('class_id', $student->class_id);

        if ($student->class_stream_id) {
            $query->where(function($q) use ($student) {
                $q->where('class_stream_id', $student->class_stream_id)
                  ->orWhereNull('class_stream_id');
            });
        } else {
            $query->whereNull('class_stream_id');
        }

        $entries = $query->orderBy('day_of_week')->orderBy('starts_at')->get();

        $tz = config('app.timezone', 'UTC');
        $refMonday = Carbon::now($tz)->startOfWeek(Carbon::MONDAY);

        $mapDay = [
            1 => 'MO', // Monday
            2 => 'TU', // Tuesday
            3 => 'WE', // Wednesday
            4 => 'TH', // Thursday
            5 => 'FR', // Friday
            6 => 'SA', // Saturday
            7 => 'SU'  // Sunday
        ];

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//SkolarisCloud//Student Timetable//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        ];

        foreach ($entries as $entry) {
            if (!isset($mapDay[$entry->day_of_week])) continue;

            $offset = $entry->day_of_week - 1; // Monday = 0, Tuesday = 1, etc.

            $dtStartLocal = $refMonday->copy()->addDays($offset)
                ->setTimeFromTimeString($entry->starts_at);
            $dtEndLocal = $refMonday->copy()->addDays($offset)
                ->setTimeFromTimeString($entry->ends_at);

            $dtStartUtc = $dtStartLocal->clone()->setTimezone('UTC');
            $dtEndUtc = $dtEndLocal->clone()->setTimezone('UTC');

            $uid = uniqid('skolaris-student-');
            $className = $student->class->name ?? 'Class';
            $subjectName = $entry->subject->name ?? 'Subject';
            $summary = $className . ': ' . $subjectName;
            $location = $entry->room ?: '';

            $lines = array_merge($lines, [
                'BEGIN:VEVENT',
                'UID:' . $uid,
                'SUMMARY:' . addcslashes($summary, ",;\\"),
                'DTSTART:' . $dtStartUtc->format('Ymd\THis\Z'),
                'DTEND:' . $dtEndUtc->format('Ymd\THis\Z'),
                'RRULE:FREQ=WEEKLY;BYDAY=' . $mapDay[$entry->day_of_week],
                ($location !== '' ? 'LOCATION:' . addcslashes($location, ",;\\") : null),
                'END:VEVENT',
            ]);
        }

        $lines[] = 'END:VCALENDAR';
        $ics = implode("\r\n", array_values(array_filter($lines, fn($l) => !is_null($l))));

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="my-timetable.ics"',
        ]);
    }
}
<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Academic\ClassRoom;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TimetableController extends Controller
{
    /**
     * Show the teacher's weekly timetable from timetable_entries.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get the teacher record linked to this user via email
        $teacher = \App\Models\Teacher::where('email', $user->email)->first();
        
        $schedule = [];
        
        if ($teacher) {
            // Fetch all timetable entries for this teacher
            $entries = \App\Models\TimetableEntry::with(['subject', 'class', 'stream'])
                ->where('teacher_id', $teacher->id)
                ->orderBy('day_of_week')
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
                    'class' => $entry->class->name ?? 'N/A',
                    'stream' => $entry->stream ? $entry->stream->name : null,
                    'start_time' => $entry->starts_at,
                    'end_time' => $entry->ends_at,
                    'room' => $entry->room,
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
        
        return view('tenant.teacher.timetable.index', [
            'teacher' => $teacher,
            'schedule' => $scheduleOrdered,
        ]);
    }

    /**
     * Edit timetable for a class (subjects on pivot class_subject)
     */
    public function edit(ClassRoom $class)
    {
        $this->authorizeClassTeacher($class);
        $subjects = $class->subjects()->with('teachers')->get();
        $weekdays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $isAdminRoute = request()->routeIs('admin.timetable.*');
        $updateRoute = $isAdminRoute ? route('admin.timetable.update', $class->id) : route('tenant.teacher.timetable.update', $class->id);
        $backRoute = $isAdminRoute ? route('admin.timetable.index') : route('tenant.teacher.timetable.index');
        $exportRoute = $isAdminRoute ? route('admin.timetable.export.ics', $class->id) : route('tenant.teacher.timetable.export.ics', $class->id);
        return view('tenant.teacher.timetable.edit', compact('class', 'subjects', 'weekdays', 'updateRoute', 'backRoute', 'exportRoute', 'isAdminRoute'));
    }

    /**
     * Persist updates to schedule fields per subject for a class.
     */
    public function update(Request $request, ClassRoom $class)
    {
        $this->authorizeClassTeacher($class);
        $data = $request->validate([
            'subjects' => ['required', 'array'],
            'subjects.*.start_time' => ['nullable', 'date_format:H:i'],
            'subjects.*.end_time' => ['nullable', 'date_format:H:i', 'after:subjects.*.start_time'],
            'subjects.*.room_number' => ['nullable', 'string', 'max:50'],
            'subjects.*.periods_per_week' => ['nullable', 'integer', 'min:0', 'max:40'],
            'subjects.*.schedule_days' => ['nullable', 'array'],
            'subjects.*.schedule_days.*' => ['in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
        ]);

        foreach ($data['subjects'] as $subjectId => $fields) {
            $days = $fields['schedule_days'] ?? [];
            $scheduleDays = is_array($days) ? implode(',', $days) : null;
            $class->subjects()->updateExistingPivot($subjectId, [
                'start_time' => $fields['start_time'] ?? null,
                'end_time' => $fields['end_time'] ?? null,
                'room_number' => $fields['room_number'] ?? null,
                'periods_per_week' => $fields['periods_per_week'] ?? null,
                'schedule_days' => $scheduleDays,
            ]);
        }

        return back()->with('success', 'Timetable updated successfully.');
    }

    /**
     * Export a class timetable as an iCalendar (.ics) file.
     */
    public function exportClassIcs(ClassRoom $class)
    {
        $this->authorizeClassTeacher($class);

        $subjects = $class->subjects()->get();
        $tz = config('app.timezone', 'UTC');
        $refMonday = Carbon::now($tz)->startOfWeek(Carbon::MONDAY);

        $mapDay = [
            'Mon' => 'MO',
            'Tue' => 'TU',
            'Wed' => 'WE',
            'Thu' => 'TH',
            'Fri' => 'FR',
            'Sat' => 'SA',
            'Sun' => 'SU'
        ];

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Skhool//Class Timetable//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        ];

        foreach ($subjects as $subject) {
            $p = $subject->pivot;
            if (!$p) {
                continue;
            }
            $days = array_filter(array_map('trim', explode(',', (string)$p->schedule_days)));
            if (empty($days) || empty($p->start_time) || empty($p->end_time)) {
                continue;
            }

            // Build BYDAY list
            $byDays = [];
            foreach ($days as $d) {
                if (isset($mapDay[$d])) {
                    $byDays[] = $mapDay[$d];
                }
            }
            if (empty($byDays)) {
                continue;
            }

            // Use the first day for DTSTART reference within the current week
            $firstDay = $days[0];
            $dowIndex = array_search($firstDay, array_keys($mapDay));
            // Resolve date by matching day name
            $offset = 0;
            switch ($firstDay) {
                case 'Mon':
                    $offset = 0;
                    break;
                case 'Tue':
                    $offset = 1;
                    break;
                case 'Wed':
                    $offset = 2;
                    break;
                case 'Thu':
                    $offset = 3;
                    break;
                case 'Fri':
                    $offset = 4;
                    break;
                case 'Sat':
                    $offset = 5;
                    break;
                case 'Sun':
                    $offset = 6;
                    break;
            }

            $dtStartLocal = $refMonday->copy()->addDays($offset)
                ->setTimeFromTimeString($p->start_time);
            $dtEndLocal = $refMonday->copy()->addDays($offset)
                ->setTimeFromTimeString($p->end_time);

            $dtStartUtc = $dtStartLocal->clone()->setTimezone('UTC');
            $dtEndUtc = $dtEndLocal->clone()->setTimezone('UTC');

            $uid = uniqid('skhool-');
            $summary = ($class->full_name ?? $class->name) . ': ' . $subject->name;
            $location = $p->room_number ?: '';

            $lines = array_merge($lines, [
                'BEGIN:VEVENT',
                'UID:' . $uid,
                'SUMMARY:' . addcslashes($summary, ",;\\"),
                'DTSTART:' . $dtStartUtc->format('Ymd\THis\Z'),
                'DTEND:' . $dtEndUtc->format('Ymd\THis\Z'),
                'RRULE:FREQ=WEEKLY;BYDAY=' . implode(',', $byDays),
                ($location !== '' ? 'LOCATION:' . addcslashes($location, ",;\\") : null),
                'END:VEVENT',
            ]);
        }

        $lines[] = 'END:VCALENDAR';
        // Remove nulls
        $ics = implode("\r\n", array_values(array_filter($lines, fn($l) => !is_null($l))));

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="class-' . $class->id . '-timetable.ics"',
        ]);
    }

    /**
     * Quick view: Today's schedule for current class teacher across their classes.
     */
    public function today()
    {
        $user = Auth::user();
        $todayCode = Carbon::now()->format('D'); // Mon/Tue...
        $classes = ClassRoom::where('class_teacher_id', $user->id)->with(['subjects' => function ($q) {
            $q->withPivot('start_time', 'end_time', 'room_number', 'schedule_days');
        }])->get();

        // Build a list of events for today
        $events = [];
        foreach ($classes as $class) {
            foreach ($class->subjects as $subject) {
                $p = $subject->pivot;
                $days = array_filter(array_map('trim', explode(',', (string)$p->schedule_days)));
                if (in_array($todayCode, $days) && $p->start_time && $p->end_time) {
                    $events[] = [
                        'class' => $class,
                        'subject' => $subject,
                        'start' => $p->start_time,
                        'end' => $p->end_time,
                        'room' => $p->room_number,
                    ];
                }
            }
        }

        // Sort by time
        usort($events, function ($a, $b) {
            return strcmp($a['start'], $b['start']);
        });

        return view('tenant.teacher.timetable.today', compact('events', 'todayCode'));
    }

    /**
     * Admin index: list all classes with subjects count.
     */
    public function adminIndex()
    {
        /** @var User $user */
        $user = Auth::user();
        // Basic gate: require admin role
        if (!($user instanceof User) || !($user->hasRole('super-admin') || $user->hasRole('school-admin'))) {
            abort(403);
        }
        $classes = ClassRoom::withCount('subjects')->orderBy('name')->get();
        return view('admin.timetable.index', compact('classes'));
    }

    private function authorizeClassTeacher(ClassRoom $class)
    {
        /** @var User|null $user */
        $user = Auth::user();
        // Allow class teacher OR admins to proceed
        $isOwner = ($user instanceof User) && ($class->class_teacher_id === $user->id);
        $isAdmin = ($user instanceof User) && ($user->hasRole('super-admin') || $user->hasRole('school-admin'));
        abort_unless($isOwner || $isAdmin, 403);
    }
}
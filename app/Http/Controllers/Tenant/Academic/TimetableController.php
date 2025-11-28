<?php

namespace App\Http\Controllers\Tenant\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTimetableEntryRequest;
use App\Http\Requests\UpdateTimetableEntryRequest;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\ClassStream;
use App\Models\Academic\Subject;
use App\Models\Academic\TimetableEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimetableController extends Controller
{
    /**
     * Display a listing of timetable entries with filters
     */
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Get filter values
        $filters = [
            'class_id' => $request->input('class_id'),
            'class_stream_id' => $request->input('class_stream_id'),
            'day_of_week' => $request->input('day_of_week'),
        ];

        // Build query with filters
        $query = TimetableEntry::forSchool($schoolId)
            ->with(['class', 'stream', 'subject', 'teacher'])
            ->orderedBySchedule();

        if ($filters['class_id']) {
            $query->forClass($filters['class_id']);
        }

        if ($filters['class_stream_id']) {
            $query->forStream($filters['class_stream_id']);
        }

        if ($filters['day_of_week']) {
            $query->forDay($filters['day_of_week']);
        }

        $entries = $query->paginate(perPage());

        // Get data for filters
        $classes = ClassRoom::where('school_id', $schoolId)->orderBy('name')->get();
        $streams = ClassStream::whereIn('class_id', $classes->pluck('id'))->orderBy('name')->get();
        $teachers = User::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where('user_type', 'teaching_staff')
            ->with('subjects')
            ->orderBy('name')
            ->get();

        return view('tenant.academics.timetable.index', compact(
            'entries',
            'classes',
            'streams',
            'teachers',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new timetable entry
     */
    public function create()
    {
        $schoolId = auth()->user()->school_id;

        $classes = ClassRoom::where('school_id', $schoolId)->orderBy('name')->get();
        $streams = ClassStream::whereIn('class_id', $classes->pluck('id'))->orderBy('name')->get();
        $subjects = Subject::forSchool($schoolId)->active()->orderBy('name')->get();
        $teachers = User::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where('user_type', 'teaching_staff')
            ->with('subjects')
            ->orderBy('name')
            ->get();

        // Group streams by class for JavaScript
        $streamsByClass = $streams->groupBy('class_id')->mapWithKeys(function ($items, $key) {
            return [(string)$key => $items->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })];
        })->toArray();

        return view('tenant.academics.timetable.create', compact(
            'classes',
            'streams',
            'subjects',
            'teachers',
            'streamsByClass'
        ));
    }

    /**
     * Store a newly created timetable entry
     */
    public function store(StoreTimetableEntryRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['school_id'] = auth()->user()->school_id;

            $entry = TimetableEntry::create($data);

            DB::commit();

            return redirect()
                ->route('tenant.academics.timetable.index')
                ->with('success', 'Timetable entry created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to create timetable entry: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing a timetable entry
     */
    public function edit($id)
    {
        $schoolId = auth()->user()->school_id;

        $entry = TimetableEntry::forSchool($schoolId)->findOrFail($id);

        $classes = ClassRoom::where('school_id', $schoolId)->orderBy('name')->get();
        $streams = ClassStream::whereIn('class_id', $classes->pluck('id'))->orderBy('name')->get();
        $subjects = Subject::forSchool($schoolId)->active()->orderBy('name')->get();
        $teachers = User::where('school_id', $schoolId)
            ->where('is_active', true)
            ->where('user_type', 'teaching_staff')
            ->with('subjects')
            ->orderBy('name')
            ->get();

        // Group streams by class for JavaScript
        $streamsByClass = $streams->groupBy('class_id')->map(function ($items) {
            return $items->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            });
        })->toArray();

        return view('tenant.academics.timetable.edit', compact(
            'entry',
            'classes',
            'streams',
            'subjects',
            'teachers',
            'streamsByClass'
        ));
    }

    /**
     * Update the specified timetable entry
     */
    public function update(UpdateTimetableEntryRequest $request, $id)
    {
        $schoolId = auth()->user()->school_id;

        DB::beginTransaction();
        try {
            $entry = TimetableEntry::forSchool($schoolId)->findOrFail($id);

            $data = $request->validated();
            $entry->update($data);

            DB::commit();

            return redirect()
                ->route('tenant.academics.timetable.index')
                ->with('success', 'Timetable entry updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update timetable entry: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified timetable entry
     */
    public function destroy($id)
    {
        $schoolId = auth()->user()->school_id;

        try {
            $entry = TimetableEntry::forSchool($schoolId)->findOrFail($id);
            $entry->delete();

            return redirect()
                ->route('tenant.academics.timetable.index')
                ->with('success', 'Timetable entry deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete timetable entry: ' . $e->getMessage());
        }
    }

    /**
     * Show the timetable generation form
     */
    // Generation methods removed - implement custom generation logic as needed

    /**
     * Bulk delete timetable entries
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*' => 'integer|exists:timetable_entries,id',
        ]);

        $schoolId = auth()->user()->school_id;

        try {
            $deleted = TimetableEntry::forSchool($schoolId)
                ->whereIn('id', $request->input('entries'))
                ->delete();

            return redirect()
                ->route('tenant.academics.timetable.index')
                ->with('success', "Successfully deleted {$deleted} timetable entries.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete entries: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update timetable entries
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'entries' => 'required|array|min:1',
            'entries.*' => 'integer|exists:timetable_entries,id',
            'action' => 'required|in:update_room,update_teacher,clear_room,clear_teacher',
            'room' => 'nullable|string|max:50',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $schoolId = auth()->user()->school_id;

        DB::beginTransaction();
        try {
            $action = $request->input('action');
            $entries = TimetableEntry::forSchool($schoolId)
                ->whereIn('id', $request->input('entries'));

            $updated = 0;

            switch ($action) {
                case 'update_room':
                    $updated = $entries->update(['room' => $request->input('room')]);
                    break;

                case 'update_teacher':
                    // Verify teacher belongs to school
                    if ($request->filled('teacher_id')) {
                        $teacher = User::where('school_id', $schoolId)
                            ->where('id', $request->input('teacher_id'))
                            ->where('user_type', 'teaching_staff')
                            ->firstOrFail();
                    }
                    $updated = $entries->update(['teacher_id' => $request->input('teacher_id')]);
                    break;

                case 'clear_room':
                    $updated = $entries->update(['room' => null]);
                    break;

                case 'clear_teacher':
                    $updated = $entries->update(['teacher_id' => null]);
                    break;
            }

            DB::commit();

            return redirect()
                ->route('tenant.academics.timetable.index')
                ->with('success', "Successfully updated {$updated} timetable entries.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update entries: ' . $e->getMessage());
        }
    }



    /**
     * Display weekly timetable for a class (optionally including streams summary)
     */
    public function showClass(ClassRoom $class)
    {
        $schoolId = auth()->user()->school_id;
        if ($class->school_id !== $schoolId) {
            abort(404);
        }

        $schedule = TimetableEntry::getWeeklyScheduleForClass($schoolId, $class->id, null);
        $streams = ClassStream::where('class_id', $class->id)->orderBy('name')->get();

        // Preload per-stream schedules counts (lazy, not full schedule per stream)
        $streamCounts = TimetableEntry::forSchool($schoolId)
            ->forClass($class->id)
            ->select('class_stream_id', DB::raw('count(*) as total'))
            ->groupBy('class_stream_id')
            ->pluck('total', 'class_stream_id');

        return view('tenant.academics.timetable.class', compact('class', 'schedule', 'streams', 'streamCounts'));
    }

    /**
     * Display weekly timetable for a specific stream.
     */
    public function showStream(ClassStream $stream)
    {
        $schoolId = auth()->user()->school_id;
        if ($stream->class->school_id !== $schoolId) {
            abort(404);
        }

        $class = $stream->class;
        $schedule = TimetableEntry::getWeeklyScheduleForClass($schoolId, $class->id, $stream->id);

        return view('tenant.academics.timetable.stream', compact('class', 'stream', 'schedule'));
    }

    /**
     * Display weekly timetable for a teacher.
     */
    public function showTeacher(User $teacher)
    {
        $schoolId = auth()->user()->school_id;
        if ($teacher->school_id !== $schoolId || !$teacher->hasUserType('teaching_staff')) {
            abort(404);
        }

        $schedule = TimetableEntry::getWeeklyScheduleForTeacher($schoolId, $teacher->id);

        return view('tenant.academics.timetable.teacher', compact('teacher', 'schedule'));
    }

    /**
     * Display weekly timetable for a student (currently shows class timetable).
     * Placeholder for future personalization (e.g., electives filtering).
     */
    public function showStudent(ClassRoom $class)
    {
        $schoolId = auth()->user()->school_id;
        if ($class->school_id !== $schoolId) {
            abort(404);
        }

        $schedule = TimetableEntry::getWeeklyScheduleForClass($schoolId, $class->id, null);
        $isStudentView = true;

        return view('tenant.academics.timetable.class', compact('class', 'schedule', 'isStudentView'));
    }

    /**
     * Show the timetable generation form.
     */
    public function generate()
    {
        $schoolId = auth()->user()->school_id;

        // Get data for generation form
        $classes = ClassRoom::forSchool($schoolId)->orderBy('name')->get();
        $streams = ClassStream::whereIn('class_id', $classes->pluck('id'))->orderBy('name')->get();

        // Group streams by class for JavaScript
        $streamsByClass = $streams->groupBy('class_id')->mapWithKeys(function ($items, $key) {
            return [(string)$key => $items->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            })];
        })->toArray();

        $subjects = Subject::forSchool($schoolId)->active()->orderBy('name')->get();
        $teachers = User::where('school_id', $schoolId)
            ->where('user_type', 'teaching_staff')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get counts for statistics
        $subjectsCount = $subjects->count();
        $teachersCount = $teachers->count();
        $entriesCount = TimetableEntry::forSchool($schoolId)->count();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('admin.timetable.generate', compact('classes', 'streams', 'streamsByClass', 'subjects', 'teachers', 'days', 'subjectsCount', 'teachersCount', 'entriesCount'));
    }

    /**
     * Store generated timetable entries.
     */
    public function storeGenerated(Request $request)
    {
        // If 'entries' is present, it's a manual save or result of a frontend generator
        if ($request->has('entries')) {
            $request->validate([
                'entries' => 'required|array',
                'entries.*.class_id' => 'required|exists:classes,id',
                'entries.*.subject_id' => 'required|exists:subjects,id',
                'entries.*.teacher_id' => 'nullable|exists:users,id',
                'entries.*.day_of_week' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
                'entries.*.starts_at' => 'required|date_format:H:i',
                'entries.*.ends_at' => 'required|date_format:H:i|after:entries.*.starts_at',
            ]);

            $schoolId = auth()->user()->school_id;

            DB::beginTransaction();
            try {
                // Optionally clear existing timetable
                if ($request->input('clear_existing')) {
                    TimetableEntry::forSchool($schoolId)->delete();
                }

                // Create entries
                foreach ($request->input('entries') as $entryData) {
                    TimetableEntry::create([
                        'school_id' => $schoolId,
                        'class_id' => $entryData['class_id'],
                        'class_stream_id' => $entryData['class_stream_id'] ?? null,
                        'subject_id' => $entryData['subject_id'],
                        'teacher_id' => $entryData['teacher_id'] ?? null,
                        'day_of_week' => $entryData['day_of_week'],
                        'starts_at' => $entryData['starts_at'],
                        'ends_at' => $entryData['ends_at'],
                    ]);
                }

                DB::commit();

                return redirect()->route('tenant.academics.timetable.index')
                    ->with('success', 'Timetable generated successfully!');
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Failed to generate timetable: ' . $e->getMessage());
            }
        }

        // Otherwise, it's a request to generate the timetable on the server
        $request->validate([
            'scope' => 'nullable|in:single,all',
            'class_id' => 'required_if:scope,single|nullable|exists:classes,id',
            'class_stream_id' => 'nullable|exists:class_streams,id',
            'max_periods_per_day' => 'required|integer|min:1|max:12',
            'working_days' => 'required|array|min:1',
            'working_days.*' => 'integer|between:1,7',
            'start_time' => 'nullable|date_format:H:i',
            'period_duration' => 'nullable|integer|min:30|max:90',
            'break_duration' => 'nullable|integer|min:5|max:60',
            'overwrite_existing' => 'nullable|boolean',
        ]);

        $schoolId = auth()->user()->school_id;
        $scope = $request->input('scope', 'single');
        $maxPeriods = $request->input('max_periods_per_day');
        $workingDays = $request->input('working_days'); // Array of day numbers (1=Mon, 7=Sun)
        $overwrite = $request->boolean('overwrite_existing');
        $startTime = $request->input('start_time', '08:00');
        $periodDuration = $request->input('period_duration', 40);
        $breakDuration = $request->input('break_duration', 15);

        DB::beginTransaction();
        try {
            $targets = [];

            if ($scope === 'all') {
                // Get all active classes
                $classes = ClassRoom::forSchool($schoolId)->get();
                foreach ($classes as $class) {
                    $streams = ClassStream::where('class_id', $class->id)->get();
                    if ($streams->count() > 0) {
                        foreach ($streams as $stream) {
                            $targets[] = ['class_id' => $class->id, 'stream_id' => $stream->id];
                        }
                    } else {
                        $targets[] = ['class_id' => $class->id, 'stream_id' => null];
                    }
                }
            } else {
                // Single class scope
                $classId = $request->input('class_id');
                $streamId = $request->input('class_stream_id');

                if ($streamId) {
                    // Specific stream selected
                    $targets[] = ['class_id' => $classId, 'stream_id' => $streamId];
                } else {
                    // No stream selected, check if class has streams
                    $streams = ClassStream::where('class_id', $classId)->get();
                    if ($streams->count() > 0) {
                        // Generate for ALL streams of this class
                        foreach ($streams as $stream) {
                            $targets[] = ['class_id' => $classId, 'stream_id' => $stream->id];
                        }
                    } else {
                        // Class has no streams
                        $targets[] = ['class_id' => $classId, 'stream_id' => null];
                    }
                }
            }

            $totalGenerated = 0;
            $totalSkipped = 0;

            foreach ($targets as $target) {
                $classId = $target['class_id'];
                $streamId = $target['stream_id'];

                // Clear existing if requested
                if ($overwrite) {
                    $query = TimetableEntry::forSchool($schoolId)->forClass($classId);
                    if ($streamId) {
                        $query->forStream($streamId);
                    } else {
                        $query->whereNull('class_stream_id');
                    }
                    $query->delete();
                }

                $class = ClassRoom::with(['subjects' => function ($query) {
                    $query->withPivot('teacher_id');
                }])->findOrFail($classId);

                $subjects = $class->subjects;
                if ($subjects->isEmpty()) {
                    continue; // Skip classes with no subjects
                }

                // Create slots (Interleaved: P1-AllDays, P2-AllDays...)
                $slots = [];
                for ($period = 1; $period <= $maxPeriods; $period++) {
                    foreach ($workingDays as $dayNum) {
                        if ($dayNum < 1 || $dayNum > 7) continue;
                        $slots[] = ['day' => $dayNum, 'period' => $period];
                    }
                }

                // Prepare lessons to schedule
                $lessons = [];
                foreach ($subjects as $subject) {
                    $frequency = isset($subject->required_periods_per_week) && $subject->required_periods_per_week > 0
                        ? $subject->required_periods_per_week
                        : 4; // Default frequency

                    for ($i = 0; $i < $frequency; $i++) {
                        $lessons[] = [
                            'subject_id' => $subject->id,
                            'subject_name' => $subject->name,
                            'teacher_id' => $subject->pivot->teacher_id ?? null,
                        ];
                    }
                }

                // Shuffle lessons for better distribution
                shuffle($lessons);

                foreach ($lessons as $lesson) {
                    if (empty($slots)) {
                        $totalSkipped++;
                        continue;
                    }

                    // Find a valid slot
                    $assignedSlotIndex = null;
                    foreach ($slots as $index => $slot) {
                        $slotStartTime = $this->getStartTime($slot['period'], $startTime, $periodDuration, $breakDuration);

                        // Check teacher availability
                        if ($lesson['teacher_id']) {
                            $isTeacherBusy = TimetableEntry::where('school_id', $schoolId)
                                ->where('teacher_id', $lesson['teacher_id'])
                                ->where('day_of_week', $slot['day'])
                                ->where('starts_at', $slotStartTime)
                                ->exists();

                            if ($isTeacherBusy) continue;
                        }

                        $assignedSlotIndex = $index;
                        break;
                    }

                    if ($assignedSlotIndex !== null) {
                        $slot = $slots[$assignedSlotIndex];

                        TimetableEntry::create([
                            'school_id' => $schoolId,
                            'class_id' => $classId,
                            'class_stream_id' => $streamId,
                            'subject_id' => $lesson['subject_id'],
                            'teacher_id' => $lesson['teacher_id'],
                            'day_of_week' => $slot['day'],
                            'starts_at' => $this->getStartTime($slot['period'], $startTime, $periodDuration, $breakDuration),
                            'ends_at' => $this->getEndTime($slot['period'], $startTime, $periodDuration, $breakDuration),
                        ]);

                        unset($slots[$assignedSlotIndex]);
                        $slots = array_values($slots);
                        $totalGenerated++;
                    } else {
                        $totalSkipped++;
                    }
                }
            }

            DB::commit();

            $message = "Timetable generated successfully! Created {$totalGenerated} entries.";
            if ($totalSkipped > 0) {
                $message .= " {$totalSkipped} lessons could not be scheduled.";
            }

            return redirect()->route('tenant.academics.timetable.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Helper to get start time for a period
     */
    private function getStartTime($period, $startTime = '08:00', $periodDuration = 40, $breakDuration = 15)
    {
        list($startHour, $startMinute) = explode(':', $startTime);
        $startHour = (int)$startHour;
        $startMinute = (int)$startMinute;

        // Calculate minutes from start for this period
        $minutesFromStart = ($period - 1) * ($periodDuration + $breakDuration);

        $totalMinutes = ($startHour * 60 + $startMinute) + $minutesFromStart;
        $hour = floor($totalMinutes / 60);
        $minute = $totalMinutes % 60;

        return sprintf('%02d:%02d', $hour, $minute);
    }

    /**
     * Helper to get end time for a period
     */
    private function getEndTime($period, $startTime = '08:00', $periodDuration = 40, $breakDuration = 15)
    {
        list($startHour, $startMinute) = explode(':', $startTime);
        $startHour = (int)$startHour;
        $startMinute = (int)$startMinute;

        // Calculate minutes from start for end of this period (before break)
        $minutesFromStart = ($period - 1) * ($periodDuration + $breakDuration) + $periodDuration;

        $totalMinutes = ($startHour * 60 + $startMinute) + $minutesFromStart;
        $hour = floor($totalMinutes / 60);
        $minute = $totalMinutes % 60;

        return sprintf('%02d:%02d', $hour, $minute);
    }
}


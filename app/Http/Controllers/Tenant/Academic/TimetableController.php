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
        $streamsByClass = $streams->groupBy('class_id')->map(function ($items) {
            return $items->map(function ($item) {
                return ['id' => $item->id, 'name' => $item->name];
            });
        });

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
        });

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

        return view('admin.timetable.generate', compact('classes', 'subjects', 'teachers', 'days', 'subjectsCount', 'teachersCount', 'entriesCount'));
    }

    /**
     * Store generated timetable entries.
     */
    public function storeGenerated(Request $request)
    {
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
}

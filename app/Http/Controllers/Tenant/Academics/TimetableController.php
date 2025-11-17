<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Models\TimetableEntry;
use App\Models\SchoolClass;
use App\Models\ClassStream;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\Validator;
use App\Services\TimetableGenerator;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->only(['class_id','class_stream_id','day_of_week']);
        $query = TimetableEntry::query()->with(['class','stream','subject','teacher'])
            ->when($filters['class_id'] ?? null, fn($q, $v) => $q->where('class_id', $v))
            ->when($filters['class_stream_id'] ?? null, fn($q, $v) => $q->where('class_stream_id', $v))
            ->when($filters['day_of_week'] ?? null, fn($q, $v) => $q->where('day_of_week', $v))
            ->orderBy('day_of_week')
            ->orderBy('starts_at');

        $entries = $query->paginate(25)->withQueryString();

        return view('tenant.academics.timetable.index', [
            'entries' => $entries,
            'classes' => SchoolClass::orderBy('name')->get(),
            'streams' => ClassStream::orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'teachers' => Teacher::orderBy('name')->get(),
            'filters' => $filters,
        ]);
    }

    public function create()
    {
        $this->authorize('manage', TimetableEntry::class);

        // For the create form, only load streams for the pre-selected class (if any)
        $preselectedClassId = old('class_id');
        $streams = collect();
        if ($preselectedClassId) {
            $streams = ClassStream::where('class_id', $preselectedClassId)
                ->orderBy('name')
                ->get();
        }
        // Provide a grouped map of streams by class for instant client-side filtering as a fallback to AJAX
        $streamsByClass = ClassStream::orderBy('name')
            ->get(['id','name','class_id'])
            ->groupBy('class_id')
            ->map(function ($group) {
                return $group->map(fn($s) => ['id' => $s->id, 'name' => $s->name])->values();
            });

        return view('tenant.academics.timetable.create', [
            'classes' => SchoolClass::orderBy('name')->get(),
            'streams' => $streams, // initially empty until a class is chosen; JS will populate via options route
            'subjects' => Subject::with('educationLevel')->orderBy('name')->get(),
            'streamsByClass' => $streamsByClass,
            'teachers' => Teacher::with(['subjects' => fn($q) => $q->select('subjects.id','subjects.name','subjects.code','education_level_id')->with('educationLevel')])
                ->whereHas('employeeRecord.user', function ($q) {
                    $q->where('approval_status', 'approved');
                })
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('manage', TimetableEntry::class);

        $data = $this->validateData($request);
        TimetableEntry::create($data);
        return redirect()->route('tenant.academics.timetable.index')->with('success', 'Timetable entry created.');
    }

    public function edit(TimetableEntry $timetable)
    {
        $this->authorize('manage', $timetable);

        $timetable->loadMissing('subject.educationLevel', 'class', 'stream');

        // Load streams for the current timetable entry's class
        $streams = ClassStream::query()
            ->when($timetable->class_id, fn($q) => $q->where('class_id', $timetable->class_id))
            ->orderBy('name')
            ->get();

        return view('tenant.academics.timetable.edit', [
            'entry' => $timetable,
            'classes' => SchoolClass::orderBy('name')->get(),
            'streams' => $streams, // Only streams for the selected class
            'subjects' => Subject::with('educationLevel')->orderBy('name')->get(),
            'teachers' => Teacher::with(['subjects' => fn($q) => $q->select('subjects.id','subjects.name','subjects.code','education_level_id')->with('educationLevel')])
                ->whereHas('employeeRecord.user', function ($q) {
                    $q->where('approval_status', 'approved');
                })
                ->orderBy('first_name')
                ->orderBy('last_name')
                ->get(),
        ]);
    }

    public function update(Request $request, TimetableEntry $timetable)
    {
        $this->authorize('manage', $timetable);

        $data = $this->validateData($request, $timetable->id);
        $timetable->update($data);
        return redirect()->route('tenant.academics.timetable.index')->with('success', 'Timetable entry updated.');
    }

    public function destroy(TimetableEntry $timetable)
    {
        $this->authorize('manage', $timetable);

        $timetable->delete();
        return redirect()->route('tenant.academics.timetable.index')->with('success', 'Timetable entry deleted.');
    }

    /**
     * Show timetable generation form
     */
    public function generate()
    {
        $this->authorize('manage', TimetableEntry::class);

        return view('tenant.academics.timetable.generate', [
            'classes' => SchoolClass::orderBy('name')->get(),
            'subjectsCount' => Subject::count(),
            'teachersCount' => Teacher::count(),
            'entriesCount' => TimetableEntry::count(),
        ]);
    }

    /**
     * Generate timetable for a class
     */
    public function storeGenerated(Request $request)
    {
        $this->authorize('manage', TimetableEntry::class);

        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'max_periods_per_day' => 'nullable|integer|min:1|max:12',
            'max_periods_per_week' => 'nullable|integer|min:1|max:60',
            'break_after_periods' => 'nullable|integer|min:1|max:10',
            'lunch_break_slot' => 'nullable|integer|min:1|max:10',
            'working_days' => 'nullable|array',
            'working_days.*' => 'integer|between:1,7',
            'overwrite_existing' => 'nullable|boolean',
        ]);

        $class = SchoolClass::findOrFail($request->class_id);
        $generator = new TimetableGenerator();

        $options = [
            'max_periods_per_day' => $request->max_periods_per_day ?? 8,
            'max_periods_per_week' => $request->max_periods_per_week ?? 40,
            'break_after_periods' => $request->break_after_periods ?? 4,
            'lunch_break_slot' => $request->lunch_break_slot ?? 4,
            'working_days' => $request->working_days ?? [1, 2, 3, 4, 5],
        ];

        try {
            $generatedTimetable = $generator->generateForClass($class, $options);

            // Optionally clear existing timetable
            if ($request->overwrite_existing) {
                TimetableEntry::where('class_id', $class->id)->delete();
            }

            // Save generated timetable
            $savedCount = 0;
            foreach ($generatedTimetable as $entry) {
                TimetableEntry::create($entry);
                $savedCount++;
            }

            return redirect()->route('tenant.academics.timetable.index')
                ->with('success', "Timetable generated successfully! {$savedCount} entries created for {$class->name}.");

        } catch (\Exception $e) {
            return back()->withErrors(['generation' => 'Failed to generate timetable: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk delete timetable entries
     */
    public function bulkDelete(Request $request)
    {
        $this->authorize('manage', TimetableEntry::class);

        $request->validate([
            'entries' => 'required|array',
            'entries.*' => 'integer|exists:timetable_entries,id',
        ]);

        $count = TimetableEntry::whereIn('id', $request->entries)->delete();

        return redirect()->route('tenant.academics.timetable.index')
            ->with('success', "Successfully deleted {$count} timetable entries.");
    }

    /**
     * Bulk update timetable entries
     */
    public function bulkUpdate(Request $request)
    {
        $this->authorize('manage', TimetableEntry::class);

        $request->validate([
            'entries' => 'required|array',
            'entries.*' => 'integer|exists:timetable_entries,id',
            'action' => 'required|in:update_room,update_teacher,clear_room,clear_teacher',
            'room' => 'nullable|string|max:50',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $entries = TimetableEntry::whereIn('id', $request->entries)->get();
        $count = 0;

        foreach ($entries as $entry) {
            switch ($request->action) {
                case 'update_room':
                    if ($request->filled('room')) {
                        $entry->update(['room' => $request->room]);
                        $count++;
                    }
                    break;
                case 'update_teacher':
                    if ($request->filled('teacher_id')) {
                        $entry->update(['teacher_id' => $request->teacher_id]);
                        $count++;
                    }
                    break;
                case 'clear_room':
                    $entry->update(['room' => null]);
                    $count++;
                    break;
                case 'clear_teacher':
                    $entry->update(['teacher_id' => null]);
                    $count++;
                    break;
            }
        }

        return redirect()->route('tenant.academics.timetable.index')
            ->with('success', "Successfully updated {$count} timetable entries.");
    }

    protected function validateData(Request $request, ?int $id = null): array
    {
        $id = $id ?? 'NULL';
        $validator = Validator::make($request->all(), [
            'day_of_week' => ['required','integer','between:1,7'],
            'starts_at' => ['required','date_format:H:i'],
            'ends_at' => ['required','date_format:H:i','after:starts_at'],
            'class_id' => ['required','exists:classes,id'],
            'class_stream_id' => ['nullable','exists:class_streams,id'],
            'subject_id' => ['required','exists:subjects,id'],
            'teacher_id' => ['nullable','exists:teachers,id'],
            'room' => ['nullable','string','max:50'],
            'notes' => ['nullable','string','max:500'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $teacherId = $request->input('teacher_id');
            $subjectId = $request->input('subject_id');

            if ($teacherId && $subjectId) {
                $teacher = Teacher::with(['subjects' => function ($query) use ($subjectId) {
                    $query->where('subjects.id', $subjectId);
                }])->find($teacherId);

                if (!$teacher || $teacher->subjects->isEmpty()) {
                    $validator->errors()->add('teacher_id', __('The selected teacher is not allocated to this subject. Please assign the subject to the teacher first.'));
                    return;
                }

                $classId = $request->input('class_id');
                $hasMatch = $teacher->subjects->contains(function ($subject) use ($classId) {
                    $pivotClass = $subject->pivot->class_id ?? null;
                    return $pivotClass === null || (string) $pivotClass === (string) $classId;
                });

                if (!$hasMatch) {
                    $validator->errors()->add('teacher_id', __('The selected teacher is not allocated to teach this subject for the chosen class. Update teacher allocations to continue.'));
                }
            }
        });

        return $validator->validate();
    }
}

<?php

namespace App\Http\Controllers\Tenant\Academics;

use App\Http\Controllers\Controller;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\Enrollment;
use App\Models\User;
use App\Models\Student;
use App\Models\Academic\ClassRoom as SchoolClass;
use App\Models\ClassStream;
use App\Models\Academic\AcademicYear;
use App\Notifications\StudentEnrolledToClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EnrollmentController extends Controller
{
    /**
     * Display a listing of enrollments
     */
    public function index(Request $request)
    {
        $query = Enrollment::with(['student', 'class']);

        // Filter by class
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by student name
        if ($request->filled('search')) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            });
        }

        $enrollments = $query->latest()->paginate(20);
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        
        return view('tenant.academics.enrollments.index', compact('enrollments', 'classes'));
    }

    /**
     * Show the form for creating a new enrollment
     */
    public function create()
    {
        $students = User::role('Student')
            ->whereDoesntHave('enrollments', function($q) {
                $q->where('status', 'active');
            })
            ->orderBy('name')
            ->get();
        
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        
        return view('tenant.academics.enrollments.create', compact('students', 'classes', 'academicYears'));
    }

    /**
     * Store a newly created enrollment
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'exists:users,id'],
            'class_id' => ['required', 'exists:classes,id'],
            'class_stream_id' => ['nullable', 'exists:class_streams,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'enrollment_date' => ['required', 'date'],
            'status' => ['required', 'in:active,dropped,transferred,completed'],
            'fees_total' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data) {
            $user = User::findOrFail($data['student_id']);

            $enrollment = Enrollment::create([
                'student_id' => $data['student_id'],
                'class_id' => $data['class_id'],
                'class_stream_id' => $data['class_stream_id'] ?? null,
                'academic_year_id' => $data['academic_year_id'],
                'enrollment_date' => $data['enrollment_date'],
                'status' => $data['status'],
                'fees_total' => $data['fees_total'] ?? 0,
                'fees_paid' => 0,
                'notes' => $data['notes'] ?? null,
                'enrolled_by' => auth()->id(),
            ]);

            $student = Student::where('email', $user->email)->first();
            $previousClassId = $student?->class_id;
            $previousStreamId = $student?->class_stream_id;
            $newClassId = $data['class_id'];
            $newStreamId = $data['class_stream_id'] ?? null;

            if ($student) {
                $student->update([
                    'class_id' => $newClassId,
                    'class_stream_id' => $newStreamId,
                ]);

                $newClassId = $student->class_id;
                $newStreamId = $student->class_stream_id;
            }

            $this->refreshEnrollmentCounters(
                $previousClassId,
                $newClassId,
                $previousStreamId,
                $newStreamId
            );

            try {
                $class = ClassRoom::find($data['class_id']);
                $stream = !empty($data['class_stream_id']) ? ClassStream::find($data['class_stream_id']) : null;

                $user->notify(
                    new StudentEnrolledToClass($class->name ?? '', $stream?->name, $data['status'])
                );
            } catch (\Throwable $e) {
                Log::warning('Failed to notify student about enrollment', [
                    'student_id' => $data['student_id'],
                    'error' => $e->getMessage(),
                ]);
            }
        });

        return redirect()
            ->route('tenant.academics.enrollments.index')
            ->with('success', __('Student enrolled successfully.'));
    }

    /**
     * Display the specified enrollment
     */
    public function show(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'class', 'academicYear']);
        return view('tenant.academics.enrollments.show', compact('enrollment'));
    }

    /**
     * Show the form for editing the specified enrollment
     */
    public function edit(Enrollment $enrollment)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->get();
        $streams = ClassStream::where('class_id', $enrollment->class_id)->orderBy('name')->get();
        $academicYears = AcademicYear::orderBy('start_date', 'desc')->get();
        
        return view('tenant.academics.enrollments.edit', compact('enrollment', 'classes', 'streams', 'academicYears'));
    }

    /**
     * Update the specified enrollment
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        $data = $request->validate([
            'class_id' => ['required', 'exists:classes,id'],
            'class_stream_id' => ['nullable', 'exists:class_streams,id'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'enrollment_date' => ['required', 'date'],
            'status' => ['required', 'in:active,dropped,transferred,completed'],
            'fees_total' => ['nullable', 'numeric', 'min:0'],
            'fees_paid' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($enrollment, $data) {
            $previousEnrollmentClassId = $enrollment->class_id;
            $previousEnrollmentStreamId = $enrollment->class_stream_id;

            $enrollment->update($data);

            $student = Student::where('email', $enrollment->student->email)->first();

            $previousClassId = $student?->class_id ?? $previousEnrollmentClassId;
            $previousStreamId = $student?->class_stream_id ?? $previousEnrollmentStreamId;

            if ($student) {
                $student->update([
                    'class_id' => $data['class_id'],
                    'class_stream_id' => $data['class_stream_id'] ?? null,
                ]);

                $newClassId = $student->class_id;
                $newStreamId = $student->class_stream_id;
            } else {
                $newClassId = $enrollment->class_id;
                $newStreamId = $enrollment->class_stream_id;
            }

            $this->refreshEnrollmentCounters(
                $previousClassId,
                $newClassId,
                $previousStreamId,
                $newStreamId
            );
        });

        return redirect()
            ->route('tenant.academics.enrollments.index')
            ->with('success', __('Enrollment updated successfully.'));
    }

    /**
     * Remove the specified enrollment
     */
    public function destroy(Enrollment $enrollment)
    {
        $classId = $enrollment->class_id;
        $streamId = $enrollment->class_stream_id;
        $enrollment->delete();

        $this->refreshEnrollmentCounters($classId, null, $streamId, null);

        return redirect()
            ->route('tenant.academics.enrollments.index')
            ->with('success', __('Enrollment deleted successfully.'));
    }

    /**
     * Get streams for a specific class (AJAX endpoint)
     */
    public function getClassStreams(Request $request)
    {
        $streams = ClassStream::where('class_id', $request->class_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'capacity', 'active_students_count']);

        return response()->json($streams);
    }

    private function refreshEnrollmentCounters(?int $previousClassId, ?int $currentClassId, ?int $previousStreamId, ?int $currentStreamId): void
    {
        if ($previousClassId && $previousClassId !== $currentClassId) {
            ClassRoom::find($previousClassId)?->updateEnrollmentCount();
        }

        if ($currentClassId) {
            ClassRoom::find($currentClassId)?->updateEnrollmentCount();
        }

        if ($previousStreamId && $previousStreamId !== $currentStreamId) {
            ClassStream::find($previousStreamId)?->updateEnrollmentCount();
        }

        if ($currentStreamId) {
            ClassStream::find($currentStreamId)?->updateEnrollmentCount();
        }
    }
}

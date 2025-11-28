<?php

namespace App\Http\Controllers\Tenant\Academic;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTeacherAllocationRequest;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\Subject;
use App\Models\User;
use App\Enums\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherAllocationController extends Controller
{
    /**
     * Display all teacher-class-subject allocations
     */
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Get filter parameters
        $teacherId = $request->input('teacher_id');
        $classId = $request->input('class_id');
        $subjectId = $request->input('subject_id');

        // Build query for allocations
        $query = DB::table('class_subject')
            ->join('classes', 'class_subject.class_id', '=', 'classes.id')
            ->join('subjects', 'class_subject.subject_id', '=', 'subjects.id')
            ->leftJoin('users', 'class_subject.teacher_id', '=', 'users.id')
            ->where('classes.school_id', $schoolId)
            ->select(
                'class_subject.id',
                'class_subject.class_id',
                'class_subject.subject_id',
                'class_subject.teacher_id',
                'class_subject.is_compulsory',
                'classes.name as class_name',
                'subjects.name as subject_name',
                'subjects.code as subject_code',
                'users.name as teacher_name',
                'users.email as teacher_email'
            );

        // Apply filters
        if ($teacherId) {
            $query->where('class_subject.teacher_id', $teacherId);
        }
        if ($classId) {
            $query->where('class_subject.class_id', $classId);
        }
        if ($subjectId) {
            $query->where('class_subject.subject_id', $subjectId);
        }

        $allocations = $query->orderBy('classes.name')
            ->orderBy('subjects.name')
            ->paginate(perPage());

        // Get dropdown data
        $teachers = User::where('school_id', $schoolId)
            ->where('user_type', UserType::TEACHING_STAFF)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $classes = ClassRoom::where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $subjects = Subject::where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return view('tenant.academics.teacher-allocations.index', compact(
            'allocations',
            'teachers',
            'classes',
            'subjects',
            'teacherId',
            'classId',
            'subjectId'
        ));
    }

    /**
     * Show form to create new allocation
     */
    public function create()
    {
        $schoolId = auth()->user()->school_id;

        $teachers = User::where('school_id', $schoolId)
            ->where('user_type', UserType::TEACHING_STAFF)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        $classes = ClassRoom::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('educationLevel')
            ->orderBy('name')
            ->get();

        $subjects = Subject::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('educationLevel')
            ->orderBy('name')
            ->get();

        return view('tenant.academics.teacher-allocations.create', compact('teachers', 'classes', 'subjects'));
    }

    /**
     * Store new teacher allocation
     */
    public function store(StoreTeacherAllocationRequest $request)
    {
        DB::beginTransaction();

        try {
            $teacherId = $request->input('teacher_id');
            $classId = $request->input('class_id');
            $subjectId = $request->input('subject_id');
            $isCompulsory = $request->input('is_compulsory', true);

            // Check if allocation already exists
            $exists = DB::table('class_subject')
                ->where('class_id', $classId)
                ->where('subject_id', $subjectId)
                ->exists();

            if ($exists) {
                // Update existing allocation
                DB::table('class_subject')
                    ->where('class_id', $classId)
                    ->where('subject_id', $subjectId)
                    ->update([
                        'teacher_id' => $teacherId,
                        'is_compulsory' => $isCompulsory,
                        'updated_at' => now()
                    ]);

                $message = 'Teacher allocation updated successfully!';
            } else {
                // Create new allocation
                DB::table('class_subject')->insert([
                    'class_id' => $classId,
                    'subject_id' => $subjectId,
                    'teacher_id' => $teacherId,
                    'is_compulsory' => $isCompulsory,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $message = 'Teacher allocated successfully!';
            }

            DB::commit();

            return redirect()->route('tenant.academics.teacher-allocations.index')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to allocate teacher: ' . $e->getMessage());
        }
    }

    /**
     * Remove teacher allocation
     */
    public function destroy($id)
    {
        try {
            $allocation = DB::table('class_subject')
                ->join('classes', 'class_subject.class_id', '=', 'classes.id')
                ->where('class_subject.id', $id)
                ->where('classes.school_id', auth()->user()->school_id)
                ->first();

            if (!$allocation) {
                return redirect()->back()
                    ->with('error', 'Allocation not found or access denied.');
            }

            // Remove teacher but keep the subject-class relationship
            DB::table('class_subject')
                ->where('id', $id)
                ->update([
                    'teacher_id' => null,
                    'updated_at' => now()
                ]);

            return redirect()->route('tenant.academics.teacher-allocations.index')
                ->with('success', 'Teacher unassigned successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to remove allocation: ' . $e->getMessage());
        }
    }

    /**
     * Show teacher's workload (all assigned subjects)
     */
    public function workload(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        // Fetch all active teachers once for dropdowns and fallbacks
        $teachers = User::where('school_id', $schoolId)
            ->where('user_type', UserType::TEACHING_STAFF)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        // Determine which teacher to display
        $teacherId = $request->input('teacher_id');

        if (! $teacherId) {
            if (auth()->user()->hasUserType(UserType::TEACHING_STAFF)) {
                $teacherId = auth()->id();
            } else {
                $teacherId = $teachers->first()?->id;
            }
        }

        $teacher = null;
        if ($teacherId) {
            $teacher = $teachers->firstWhere('id', (int) $teacherId)
                ?? User::where('id', $teacherId)
                    ->where('school_id', $schoolId)
                    ->where('user_type', UserType::TEACHING_STAFF)
                    ->first();
        }

        $allocations = collect();
        $stats = [
            'total_subjects' => 0,
            'total_classes' => 0,
            'core_subjects' => 0,
            'elective_subjects' => 0,
            'optional_subjects' => 0,
        ];

        if ($teacher) {
            $allocations = DB::table('class_subject')
                ->join('classes', 'class_subject.class_id', '=', 'classes.id')
                ->join('subjects', 'class_subject.subject_id', '=', 'subjects.id')
                ->leftJoin('education_levels', 'classes.education_level_id', '=', 'education_levels.id')
                ->where('class_subject.teacher_id', $teacher->id)
                ->where('classes.school_id', $schoolId)
                ->select(
                    'class_subject.id',
                    'classes.name as class_name',
                    'subjects.name as subject_name',
                    'subjects.code as subject_code',
                    'subjects.type as subject_type',
                    'class_subject.is_compulsory',
                    'education_levels.name as level_name'
                )
                ->orderBy('education_levels.name')
                ->orderBy('classes.name')
                ->get();

            $stats = [
                'total_subjects' => $allocations->count(),
                'total_classes' => $allocations->pluck('class_name')->unique()->count(),
                'core_subjects' => $allocations->where('subject_type', 'core')->count(),
                'elective_subjects' => $allocations->where('subject_type', 'elective')->count(),
                'optional_subjects' => $allocations->where('subject_type', 'optional')->count(),
            ];
        }

        return view('tenant.academics.teacher-allocations.workload', [
            'teacher' => $teacher,
            'allocations' => $allocations,
            'stats' => $stats,
            'teachers' => $teachers,
            'selectedTeacherId' => $teacher?->id,
        ]);
    }

    /**
     * Bulk assign multiple subjects to a teacher
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'allocations' => 'required|array|min:1',
            'allocations.*.class_id' => 'required|exists:classes,id',
            'allocations.*.subject_id' => 'required|exists:subjects,id',
        ]);

        DB::beginTransaction();

        try {
            $teacherId = $request->input('teacher_id');
            $allocations = $request->input('allocations');
            $count = 0;

            foreach ($allocations as $allocation) {
                $classId = $allocation['class_id'];
                $subjectId = $allocation['subject_id'];

                // Check if class-subject relationship exists
                $exists = DB::table('class_subject')
                    ->where('class_id', $classId)
                    ->where('subject_id', $subjectId)
                    ->exists();

                if ($exists) {
                    // Update teacher_id
                    DB::table('class_subject')
                        ->where('class_id', $classId)
                        ->where('subject_id', $subjectId)
                        ->update([
                            'teacher_id' => $teacherId,
                            'updated_at' => now()
                        ]);
                    $count++;
                } else {
                    // Create new class-subject-teacher allocation
                    DB::table('class_subject')->insert([
                        'class_id' => $classId,
                        'subject_id' => $subjectId,
                        'teacher_id' => $teacherId,
                        'is_compulsory' => true,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                    $count++;
                }
            }

            DB::commit();

            return redirect()->route('tenant.academics.teacher-allocations.index')
                ->with('success', "Successfully allocated {$count} subject(s) to teacher!");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withInput()
                ->with('error', 'Bulk allocation failed: ' . $e->getMessage());
        }
    }

    /**
     * Get subjects available for a specific class (AJAX)
     */
    public function getClassSubjects(Request $request, $classId)
    {
        $schoolId = auth()->user()->school_id;

        // Verify class belongs to school
        $class = ClassRoom::where('id', $classId)
            ->where('school_id', $schoolId)
            ->first();

        if (!$class) {
            return response()->json(['error' => 'Class not found'], 404);
        }

        // Get subjects assigned to this class
        $subjects = DB::table('class_subject')
            ->join('subjects', 'class_subject.subject_id', '=', 'subjects.id')
            ->where('class_subject.class_id', $classId)
            ->select('subjects.id', 'subjects.name', 'subjects.code', 'class_subject.teacher_id')
            ->get();

        return response()->json($subjects);
    }
}

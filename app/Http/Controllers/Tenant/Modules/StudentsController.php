<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\Academic\AcademicYear;
use App\Models\Academic\ClassRoom;
use App\Models\Academic\ClassStream;
use App\Models\Academic\Enrollment;
use App\Models\Student;
use App\Models\User;
use App\Notifications\StudentEnrolledToClass;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StudentsController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authorize('viewAny', Student::class);
            return $next($request);
        })->only(['index','create']);

        $this->middleware(function ($request, $next) {
            $this->authorize('create', Student::class);
            return $next($request);
        })->only(['store']);
    }
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $students = Student::query()
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where('name', 'like', "%$q%")
                   ->orWhere('admission_no', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%");
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
        return view('tenant.modules.students.index', compact('students','q'));
    }
    public function create(): View
    {
        $classes = ClassRoom::where('is_active', true)->orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('is_current')
            ->orderByDesc('start_date')
            ->get();

        $defaultAcademicYearId = $academicYears->firstWhere('is_current', true)?->id
            ?? $academicYears->first()?->id;

        $streamsByClass = ClassStream::select('id', 'class_id', 'name', 'capacity', 'active_students_count')
            ->orderBy('name')
            ->get()
            ->groupBy('class_id')
            ->map(fn ($streams) => $streams->map(fn ($stream) => [
                'id' => $stream->id,
                'name' => $stream->name,
                'capacity' => $stream->capacity,
                'active_students_count' => $stream->active_students_count,
            ])->values())
            ->toArray();

        return view('tenant.modules.students.create', compact(
            'classes',
            'academicYears',
            'defaultAcademicYearId',
            'streamsByClass'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $school = $request->attributes->get('currentSchool') ?? auth()->user()->school;

        $validated = $request->validate([
            // Personal Information
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'national_id' => ['nullable', 'string', 'max:50', 'unique:students,national_id'],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'phone' => ['nullable', 'string', 'max:20'],

            // Academic Information
            'admission_no' => ['required', 'string', 'max:100', 'unique:students,admission_no'],
            'class_id' => ['required', 'exists:classes,id'],
            'class_stream_id' => ['nullable', 'exists:class_streams,id'],
            'roll_number' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:10'],
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'admission_date' => ['required', 'date'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('students', 'email'),
                Rule::unique('users', 'email'),
            ],
            'password' => ['nullable', 'string', 'min:8'],
            'status' => ['required', 'in:active,inactive,transferred,graduated,suspended'],

            // Contact Information
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],

            // Father Information
            'father_name' => ['nullable', 'string', 'max:200'],
            'father_phone' => ['nullable', 'string', 'max:20'],
            'father_occupation' => ['nullable', 'string', 'max:100'],
            'father_email' => ['nullable', 'email', 'max:255'],

            // Mother Information
            'mother_name' => ['nullable', 'string', 'max:200'],
            'mother_phone' => ['nullable', 'string', 'max:20'],
            'mother_occupation' => ['nullable', 'string', 'max:100'],
            'mother_email' => ['nullable', 'email', 'max:255'],

            // Guardian Information
            'guardian_name' => ['nullable', 'string', 'max:200'],
            'guardian_phone' => ['nullable', 'string', 'max:20'],
            'guardian_relation' => ['nullable', 'string', 'max:100'],
            'guardian_email' => ['nullable', 'email', 'max:255'],

            // Emergency Contact
            'emergency_contact_name' => ['nullable', 'string', 'max:200'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],

            // Medical Information
            'medical_conditions' => ['nullable', 'string', 'max:1000'],
            'allergies' => ['nullable', 'string', 'max:1000'],
            'medications' => ['nullable', 'string', 'max:1000'],

            // Previous School
            'previous_school' => ['nullable', 'string', 'max:255'],
            'previous_class' => ['nullable', 'string', 'max:100'],
            'transfer_reason' => ['nullable', 'string', 'max:1000'],

            // Special Needs
            'has_special_needs' => ['nullable', 'boolean'],
            'special_needs_description' => ['nullable', 'string', 'max:1000'],

            // Additional
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        foreach (['address','city','state','postal_code','country'] as $nullableAddressField) {
            if (! array_key_exists($nullableAddressField, $validated) || $validated[$nullableAddressField] === null) {
                $validated[$nullableAddressField] = '';
            }
        }

        $streamId = $validated['class_stream_id'] ?? null;
        if ($streamId) {
            $stream = ClassStream::find($streamId);
            if (! $stream || (int) $stream->class_id !== (int) $validated['class_id']) {
                throw ValidationException::withMessages([
                    'class_stream_id' => __('Selected stream does not belong to the chosen class.'),
                ]);
            }
        }

        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('students/profiles', 'public');
        }

        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $validated['dob'] = $validated['date_of_birth'];

        $academicYearId = $validated['academic_year_id'];
        unset($validated['academic_year_id']);

        $student = null;

        DB::transaction(function () use (&$student, $validated, $school, $academicYearId, $streamId) {
            [$user, $autoApproved] = $this->provisionStudentUser($validated, $school, [
                'academic_year_id' => $academicYearId,
                'class_stream_id' => $streamId,
            ]);

            if (isset($validated['password'])) {
                unset($validated['password']);
            }

            $student = Student::create($validated);

            $enrollmentStatus = $autoApproved ? 'active' : 'pending';

            Enrollment::create([
                'student_id' => $user->id,
                'class_id' => $validated['class_id'],
                'class_stream_id' => $streamId,
                'academic_year_id' => $academicYearId,
                'enrollment_date' => $validated['admission_date'],
                'status' => $enrollmentStatus,
                'fees_total' => 0,
                'fees_paid' => 0,
                'notes' => __('Auto-created from student onboarding.'),
                'enrolled_by' => auth()->id(),
            ]);

            $this->recalculateEnrollmentCounters($validated['class_id'], $streamId);
            $this->notifyStudentEnrollment($user, $validated['class_id'], $streamId, $enrollmentStatus);
        });

        return redirect()
            ->route('tenant.modules.students.show', $student)
            ->with('status', __('Student profile, user account, and enrollment were created successfully.'));
    }

    public function show(Student $student): View
    {
        $account = $student->account()->with('roles')->first();
        $latestEnrollment = $account
            ? $account->enrollments()->with(['class', 'stream', 'academicYear'])->latest()->first()
            : null;

        return view('tenant.modules.students.show', compact('student', 'account', 'latestEnrollment'));
    }

    public function edit(Student $student): View
    {
        $classes = ClassRoom::where('is_active', true)->orderBy('name')->get();
        return view('tenant.modules.students.edit', compact('student', 'classes'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            // Personal Information
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'national_id' => ['nullable', 'string', 'max:50', 'unique:students,national_id,' . $student->id],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'phone' => ['nullable', 'string', 'max:20'],

            // Academic Information
            'admission_no' => ['required', 'string', 'max:100', 'unique:students,admission_no,' . $student->id],
            'class_id' => ['required', 'exists:classes,id'],
            'roll_number' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:10'],
            'admission_date' => ['required', 'date'],
            'email' => ['nullable', 'email', 'max:255', 'unique:students,email,' . $student->id],
            'status' => ['required', 'in:active,inactive,transferred,graduated,suspended'],

            // Contact Information
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],

            // Father Information
            'father_name' => ['nullable', 'string', 'max:200'],
            'father_phone' => ['nullable', 'string', 'max:20'],
            'father_occupation' => ['nullable', 'string', 'max:100'],
            'father_email' => ['nullable', 'email', 'max:255'],

            // Mother Information
            'mother_name' => ['nullable', 'string', 'max:200'],
            'mother_phone' => ['nullable', 'string', 'max:20'],
            'mother_occupation' => ['nullable', 'string', 'max:100'],
            'mother_email' => ['nullable', 'email', 'max:255'],

            // Guardian Information
            'guardian_name' => ['nullable', 'string', 'max:200'],
            'guardian_phone' => ['nullable', 'string', 'max:20'],
            'guardian_relation' => ['nullable', 'string', 'max:100'],
            'guardian_email' => ['nullable', 'email', 'max:255'],

            // Emergency Contact
            'emergency_contact_name' => ['nullable', 'string', 'max:200'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],

            // Medical Information
            'medical_conditions' => ['nullable', 'string', 'max:1000'],
            'allergies' => ['nullable', 'string', 'max:1000'],
            'medications' => ['nullable', 'string', 'max:1000'],

            // Previous School
            'previous_school' => ['nullable', 'string', 'max:255'],
            'previous_class' => ['nullable', 'string', 'max:100'],
            'transfer_reason' => ['nullable', 'string', 'max:1000'],

            // Special Needs
            'has_special_needs' => ['nullable', 'boolean'],
            'special_needs_description' => ['nullable', 'string', 'max:1000'],

            // Additional
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('students/profiles', 'public');
        }

        // Update name field for backward compatibility
        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $validated['dob'] = $validated['date_of_birth'];

        $student->update($validated);
        return redirect()->route('tenant.modules.students.show', $student)->with('status', __('Student updated successfully.'));
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();
        return redirect()->route('tenant.modules.students.index')->with('status', __('Student deleted.'));
    }

    /**
     * Create or update the tenant user account tied to this student.
     *
     * @return array{0: User, 1: bool} [user, autoApproved]
     */
    private function provisionStudentUser(array $studentData, $school, array $context = []): array
    {
        $approvalMode = function_exists('setting') ? setting('user_approval_mode', 'manual') : 'manual';
        $autoApprove = $approvalMode === 'automatic';

        $user = User::firstOrNew(['email' => $studentData['email']]);
        $displayName = trim($studentData['first_name'] . ' ' . $studentData['last_name']);

        if (! $user->exists) {
            $user->name = $displayName;
            $password = !empty($studentData['password']) ? $studentData['password'] : 'Student@123';
            $user->password = Hash::make($password);
            $user->school_id = $school->id ?? null;
            $user->user_type = UserType::STUDENT;
            $user->approval_status = $autoApprove ? 'approved' : 'pending';
            $user->is_active = $autoApprove;
            $user->approved_by = $autoApprove ? optional(auth()->user())->id : null;
            $user->approved_at = $autoApprove ? now() : null;
        } else {
            $user->name = $displayName;
            $user->school_id = $user->school_id ?: ($school->id ?? null);

            if (! $user->hasUserType(UserType::STUDENT)) {
                $user->user_type = UserType::STUDENT;
            }

            if ($autoApprove && $user->approval_status === 'pending') {
                $user->approval_status = 'approved';
                $user->approved_by = optional(auth()->user())->id;
                $user->approved_at = now();
                $user->is_active = true;
            } elseif (! $user->approval_status) {
                $user->approval_status = $autoApprove ? 'approved' : 'pending';
            }
        }

        $profileSnapshot = array_filter([
            'first_name' => $studentData['first_name'] ?? null,
            'last_name' => $studentData['last_name'] ?? null,
            'gender' => $studentData['gender'] ?? null,
            'dob' => $studentData['date_of_birth'] ?? $studentData['dob'] ?? null,
            'admission_no' => $studentData['admission_no'] ?? null,
            'class_id' => $studentData['class_id'] ?? null,
            'class_stream_id' => $context['class_stream_id'] ?? $studentData['class_stream_id'] ?? null,
            'academic_year_id' => $context['academic_year_id'] ?? $studentData['academic_year_id'] ?? null,
            'admission_date' => $studentData['admission_date'] ?? null,
            'status' => $studentData['status'] ?? null,
        ], static fn ($value) => ! is_null($value));

        $user->registration_data = array_replace_recursive(
            $user->registration_data ?? [],
            array_filter([
                'source' => 'tenant_student_module',
                'submitted_by' => optional(auth()->user())->id,
                'submitted_at' => now()->toIso8601String(),
                'student_profile' => $profileSnapshot,
            ])
        );

        $user->save();

        if (! $user->hasRole('Student')) {
            $user->assignRole('Student');
        }

        return [$user, $autoApprove];
    }

    private function recalculateEnrollmentCounters(int $classId, ?int $streamId = null): void
    {
        ClassRoom::find($classId)?->updateEnrollmentCount();

        if ($streamId) {
            ClassStream::find($streamId)?->updateEnrollmentCount();
        }
    }

    private function notifyStudentEnrollment(User $user, int $classId, ?int $streamId, string $status): void
    {
        try {
            $class = ClassRoom::find($classId);
            $stream = $streamId ? ClassStream::find($streamId) : null;

            $user->notify(new StudentEnrolledToClass($class->name ?? '', $stream?->name, $status));
        } catch (\Throwable $e) {
            Log::warning('Failed to notify student about auto enrollment', [
                'user_id' => $user->id,
                'class_id' => $classId,
                'stream_id' => $streamId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

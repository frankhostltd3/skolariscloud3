<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
        $classes = \App\Models\SchoolClass::where('is_active', true)->orderBy('name')->get();
        return view('tenant.modules.students.create', compact('classes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Personal Information
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'national_id' => ['nullable', 'string', 'max:50', 'unique:students,national_id'],
            'profile_photo' => ['nullable', 'image', 'max:2048'], // 2MB max
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'phone' => ['nullable', 'string', 'max:20'],
            
            // Academic Information
            'admission_no' => ['required', 'string', 'max:100', 'unique:students,admission_no'],
            'class_id' => ['required', 'exists:school_classes,id'],
            'roll_number' => ['nullable', 'string', 'max:50'],
            'section' => ['nullable', 'string', 'max:10'],
            'admission_date' => ['required', 'date'],
            'email' => ['nullable', 'email', 'max:255', 'unique:students,email'],
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

        // Set name as combination of first and last name for backward compatibility
        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];
        $validated['dob'] = $validated['date_of_birth']; // Alias for backward compatibility

        Student::create($validated);

        return redirect()->route('tenant.modules.students.index')->with('status', __('Student created successfully.'));
    }

    public function show(Student $student): View
    {
        return view('tenant.modules.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        return view('tenant.modules.students.edit', compact('student'));
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
            'class_id' => ['required', 'exists:school_classes,id'],
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
}

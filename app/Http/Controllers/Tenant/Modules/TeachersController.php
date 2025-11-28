<?php

namespace App\Http\Controllers\Tenant\Modules;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserType;

class TeachersController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->authorize('viewAny', Teacher::class);
            return $next($request);
        })->only(['index','create']);

        $this->middleware(function ($request, $next) {
            $this->authorize('create', Teacher::class);
            return $next($request);
        })->only(['store']);
    }
    public function index(Request $request): View
    {
        $q = trim((string) $request->get('q'));
        $teachers = Teacher::query()
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where('name', 'like', "%$q%")
                   ->orWhere('email', 'like', "%$q%");
            })
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();
        return view('tenant.modules.teachers.index', compact('teachers','q'));
    }
    public function create(): View
    {
        return view('tenant.modules.teachers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            // Personal Information
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date', 'before:-18 years'], // Must be 18+
            'national_id' => ['nullable', 'string', 'max:50', 'unique:teachers,national_id'],
            'profile_photo' => ['nullable', 'image', 'max:2048'], // 2MB max
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],

            // Contact Information
            'email' => ['required', 'email', 'max:255', 'unique:teachers,email'],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],

            // Professional Information
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:teachers,employee_id'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'joining_date' => ['required', 'date'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,visiting'],
            'status' => ['required', 'in:active,on_leave,resigned,terminated'],

            // Emergency Contact
            'emergency_contact_name' => ['nullable', 'string', 'max:200'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],

            // Additional Information
            'medical_conditions' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('teachers/profiles', 'public');
        }

        // Set name as combination of first and last name for backward compatibility
        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        DB::transaction(function () use ($validated) {
            // Create User Account
            $password = !empty($validated['password']) ? $validated['password'] : 'Teacher@123';

            $user = \App\Models\User::where('email', $validated['email'])->first();

            if (!$user) {
                $user = \App\Models\User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make($password),
                    'user_type' => UserType::TEACHING_STAFF,
                    'school_id' => auth()->user()->school_id ?? null,
                    'approval_status' => 'approved',
                    'is_active' => true,
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'email_verified_at' => now(),
                ]);
            }

            if (!$user->hasRole('Teacher')) {
                $user->assignRole('Teacher');
            }

            $validated['user_id'] = $user->id;
            if (isset($validated['password'])) {
                unset($validated['password']);
            }

            Teacher::create($validated);
        });

        return redirect()->route('tenant.modules.teachers.index')->with('status', __('Teacher created successfully.'));
    }

    public function show(Teacher $teacher): View
    {
        return view('tenant.modules.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher): View
    {
        return view('tenant.modules.teachers.edit', compact('teacher'));
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            // Personal Information
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'gender' => ['required', 'in:male,female,other'],
            'date_of_birth' => ['required', 'date', 'before:-18 years'],
            'national_id' => ['nullable', 'string', 'max:50', 'unique:teachers,national_id,' . $teacher->id],
            'profile_photo' => ['nullable', 'image', 'max:2048'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],

            // Contact Information
            'email' => ['required', 'email', 'max:255', 'unique:teachers,email,' . $teacher->id],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],

            // Professional Information
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:teachers,employee_id,' . $teacher->id],
            'qualification' => ['nullable', 'string', 'max:255'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'experience_years' => ['nullable', 'integer', 'min:0', 'max:50'],
            'joining_date' => ['required', 'date'],
            'employment_type' => ['required', 'in:full_time,part_time,contract,visiting'],
            'status' => ['required', 'in:active,on_leave,resigned,terminated'],

            // Emergency Contact
            'emergency_contact_name' => ['nullable', 'string', 'max:200'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],

            // Additional Information
            'medical_conditions' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')->store('teachers/profiles', 'public');
        }

        // Update name field for backward compatibility
        $validated['name'] = $validated['first_name'] . ' ' . $validated['last_name'];

        $teacher->update($validated);
        return redirect()->route('tenant.modules.teachers.show', $teacher)->with('status', __('Teacher updated successfully.'));
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->delete();
        return redirect()->route('tenant.modules.teachers.index')->with('status', __('Teacher deleted.'));
    }
}

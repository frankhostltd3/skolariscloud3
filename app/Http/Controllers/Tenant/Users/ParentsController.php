<?php

namespace App\Http\Controllers\Tenant\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ParentProfile;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ParentsController extends Controller
{
    private string $role = 'Parent';
    private string $title = 'Parents';
    private string $routePrefix = 'tenant.users.parents';

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $users = User::query()
            ->with('parentProfile.students')
            ->role($this->role)
            ->when($q !== '', fn($query) => $query->where(function($w) use ($q){
                $w->where('name','like',"%$q%");
                $w->orWhere('email','like',"%$q%");
                $w->orWhereHas('parentProfile', function($profile) use ($q) {
                    $profile->where('phone', 'like', "%$q%");
                    $profile->orWhere('first_name', 'like', "%$q%");
                    $profile->orWhere('last_name', 'like', "%$q%");
                });
            }))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('tenant.users.parents.index', [
            'users' => $users,
            'q' => $q,
        ]);
    }

    public function create(): View
    {
        return view('tenant.users.parents.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            // User Account
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['nullable','confirmed','min:8'],

            // Personal Information
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:-18 years'],
            'national_id' => ['nullable', 'string', 'max:50', 'unique:tenant.parents,national_id'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],

            // Contact Information
            'phone' => ['required', 'string', 'max:20'],
            'alternate_phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],

            // Occupation Information
            'occupation' => ['nullable', 'string', 'max:255'],
            'employer' => ['nullable', 'string', 'max:255'],
            'work_phone' => ['nullable', 'string', 'max:20'],
            'work_address' => ['nullable', 'string', 'max:500'],
            'annual_income' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],

            // Relationships
            'students' => ['nullable', 'array'],
            'students.*' => ['exists:students,id'],
            'relationships' => ['nullable', 'array'],
            'relationships.*' => ['in:father,mother,guardian,relative,other'],

            // Emergency Contact
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],

            // Additional Information
            'medical_conditions' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'in:active,inactive,deceased'],
        ]);

        // Create user account
        $user = User::create([
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['email'],
            'password' => !empty($data['password']) ? $data['password'] : 'Parent@123',
        ]);
        $user->assignRole($this->role);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('parents/profiles', 'public');
            $data['profile_photo'] = $path;
            $user->forceFill(['profile_photo' => $path])->save();
        }

        // Create parent profile
        $parentProfile = $user->parentProfile()->create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'profile_photo' => $data['profile_photo'] ?? null,
            'phone' => $data['phone'],
            'alternate_phone' => $data['alternate_phone'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'country' => $data['country'] ?? 'Kenya',
            'occupation' => $data['occupation'] ?? null,
            'employer' => $data['employer'] ?? null,
            'work_phone' => $data['work_phone'] ?? null,
            'work_address' => $data['work_address'] ?? null,
            'annual_income' => $data['annual_income'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'emergency_contact_relation' => $data['emergency_contact_relation'] ?? null,
            'medical_conditions' => $data['medical_conditions'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? 'active',
        ]);

        // Attach students with relationships
        if (!empty($data['students'])) {
            foreach ($data['students'] as $index => $studentId) {
                $relationship = $data['relationships'][$index] ?? 'parent';
                $parentProfile->students()->attach($studentId, [
                    'relationship' => $relationship,
                    'is_primary' => $index === 0, // First one is primary
                    'can_pickup' => true,
                    'financial_responsibility' => true,
                ]);
            }
        }

        return redirect()->route($this->routePrefix)->with('success', __(':title created.', ['title' => __($this->title)]));
    }

    public function show(User $user): View
    {
        abort_unless($user->hasRole($this->role), 404);

        // Load parent profile with students and their classes
        $user->load(['parentProfile.students.class']);
        $profile = $user->parentProfile;

        return view('tenant.users.parents.show', [
            'user' => $user,
            'profile' => $profile,
        ]);
    }

    public function edit(User $user): View
    {
        abort_unless($user->hasRole($this->role), 404);

        // Load parent profile with students
        $user->load(['parentProfile.students']);
        $profile = $user->parentProfile;

        if (!$profile) {
            $profile = new ParentProfile([
                'country' => 'Kenya',
            ]);
            $profile->setRelation('students', collect());
        }

        // Get all students for dropdown
        $students = Student::orderBy('first_name')->get();

        return view('tenant.users.parents.edit', [
            'user' => $user,
            'profile' => $profile,
            'students' => $students,
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->hasRole($this->role), 404);

        $data = $request->validate([
            // User Account
            'email' => ['required','email','max:255','unique:users,email,' . $user->id],
            'password' => ['nullable','confirmed','min:8'],

            // Personal Information
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', 'in:male,female,other'],
            'date_of_birth' => ['nullable', 'date', 'before:-18 years'],
            'national_id' => ['nullable', 'string', 'max:50', 'unique:tenant.parents,national_id,' . $user->parentProfile?->id],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,O+,O-,AB+,AB-'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],

            // Contact Information
            'phone' => ['required', 'string', 'max:20'],
            'alternate_phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],

            // Occupation Information
            'occupation' => ['nullable', 'string', 'max:255'],
            'employer' => ['nullable', 'string', 'max:255'],
            'work_phone' => ['nullable', 'string', 'max:20'],
            'work_address' => ['nullable', 'string', 'max:500'],
            'annual_income' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],

            // Relationships
            'students' => ['nullable', 'array'],
            'students.*' => ['exists:students,id'],
            'relationships' => ['nullable', 'array'],
            'relationships.*' => ['in:father,mother,guardian,relative,other'],

            // Emergency Contact
            'emergency_contact_name' => ['nullable', 'string', 'max:255'],
            'emergency_contact_phone' => ['nullable', 'string', 'max:20'],
            'emergency_contact_relation' => ['nullable', 'string', 'max:100'],

            // Additional Information
            'medical_conditions' => ['nullable', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'in:active,inactive,deceased'],
        ]);

        // Update user account
        $userData = [
            'name' => $data['first_name'] . ' ' . $data['last_name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $userData['password'] = $data['password'];
        }

        $user->update($userData);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('parents/profiles', 'public');
            $data['profile_photo'] = $path;
            $user->forceFill(['profile_photo' => $path])->save();
        }

        // Update or create parent profile
        $profileData = [
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'gender' => $data['gender'] ?? null,
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            'blood_group' => $data['blood_group'] ?? null,
            'phone' => $data['phone'],
            'alternate_phone' => $data['alternate_phone'] ?? null,
            'address' => $data['address'] ?? null,
            'city' => $data['city'] ?? null,
            'state' => $data['state'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'country' => $data['country'] ?? 'Kenya',
            'occupation' => $data['occupation'] ?? null,
            'employer' => $data['employer'] ?? null,
            'work_phone' => $data['work_phone'] ?? null,
            'work_address' => $data['work_address'] ?? null,
            'annual_income' => $data['annual_income'] ?? null,
            'emergency_contact_name' => $data['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            'emergency_contact_relation' => $data['emergency_contact_relation'] ?? null,
            'medical_conditions' => $data['medical_conditions'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $data['status'] ?? 'active',
        ];

        if (isset($data['profile_photo'])) {
            $profileData['profile_photo'] = $data['profile_photo'];
        }

        $profile = $user->parentProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        // Sync students with relationships
        if (isset($data['students'])) {
            $syncData = [];
            foreach ($data['students'] as $index => $studentId) {
                $relationship = $data['relationships'][$index] ?? 'parent';
                $syncData[$studentId] = [
                    'relationship' => $relationship,
                    'is_primary' => $index === 0,
                    'can_pickup' => true,
                    'financial_responsibility' => true,
                ];
            }
            $profile->students()->sync($syncData);
        }

        if (! $user->hasRole($this->role)) {
            $user->syncRoles([$this->role]);
        }

        return redirect()->route($this->routePrefix . '.show', $user)->with('success', __(':title updated.', ['title' => __($this->title)]));
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->hasRole($this->role), 404);
        $user->delete();
        return redirect()->route($this->routePrefix)->with('success', __(':title deleted.', ['title' => __($this->title)]));
    }

    public function activate(User $user): RedirectResponse
    {
        abort_unless($user->hasRole($this->role), 404);

        $user->activate();

        return redirect()->back()->with('success', __('User activated successfully.'));
    }

    public function deactivate(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->hasRole($this->role), 404);

        $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $user->deactivate($request->input('reason'));

        return redirect()->back()->with('success', __('User deactivated successfully.'));
    }
}

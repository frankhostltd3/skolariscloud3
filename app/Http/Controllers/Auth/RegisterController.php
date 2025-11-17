<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\SchoolUserInvitation;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\TenantDatabaseManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Support\CentralDomain;

class RegisterController extends Controller
{
    public function __construct(private TenantDatabaseManager $tenants)
    {
    }

    public function create(Request $request): View
    {
        // Logout any existing session to clear tenant connection issues
        if (auth()->check()) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        $school = $request->attributes->get('currentSchool');
        $host = $request->getHost();

        // Force central registration form for localhost/127.0.0.1
        if (in_array($host, ['localhost', '127.0.0.1'], true)) {
            return view('auth.register', [
                'baseDomain' => CentralDomain::base($request),
            ]);
        }

        if ($school instanceof School) {
            return view('auth.register-tenant', [
                'school' => $school,
            ]);
        }

        return view('auth.register', [
            'baseDomain' => CentralDomain::base($request),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $school = $request->attributes->get('currentSchool');
        $host = $request->getHost();

        // Force school registration for localhost/127.0.0.1
        if (in_array($host, ['localhost', '127.0.0.1'], true)) {
            return $this->registerSchool($request);
        }

        if ($school instanceof School) {
            return $this->registerTenantUser($request, $school);
        }

        return $this->registerSchool($request);
    }

    private function registerTenantUser(Request $request, School $school): RedirectResponse
    {
        $minLength = (int) setting('password_min_length', 8);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:' . $minLength, 'confirmed'],
        ]);

        $invitation = SchoolUserInvitation::query()
            ->where('school_id', $school->id)
            ->where('email', $validated['email'])
            ->first();

        if (! $invitation) {
            throw ValidationException::withMessages([
                'email' => 'We could not find an invitation for this email. Please contact your school administrator.',
            ]);
        }

        if ($invitation->isExpired()) {
            throw ValidationException::withMessages([
                'email' => 'This invitation has expired. Please request a new invitation.',
            ]);
        }

        if ($invitation->isAccepted()) {
            throw ValidationException::withMessages([
                'email' => 'This invitation has already been used. Try signing in instead.',
            ]);
        }

        $user = DB::transaction(function () use ($invitation, $validated, $school) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'user_type' => $invitation->user_type,
                'school_id' => $school->id,
            ]);

            $invitation->markAccepted();

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    private function registerSchool(Request $request): RedirectResponse
    {
        // For central registration, use default 8 (no tenant context yet)
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:50'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'You must agree to the terms of service to continue.',
        ]);

        $subdomain = $this->normalizeSubdomain($validated['subdomain']);

        if (School::query()->where('subdomain', $subdomain)->exists()) {
            throw ValidationException::withMessages([
                'subdomain' => 'That subdomain is already taken. Please choose another address.',
            ]);
        }

            $domain = CentralDomain::tenantDomain($subdomain, $request);

        $school = DB::transaction(function () use ($validated, $subdomain, $domain) {
            return School::create([
                'name' => $validated['school_name'],
                'code' => Str::upper(Str::random(8)),
                'subdomain' => $subdomain,
                'domain' => $domain,
            ]);
        });

        try {
            $this->tenants->runFor(
                $school,
                function () use ($validated, $school) {
                    User::create([
                        'name' => $validated['admin_name'],
                        'email' => $validated['admin_email'],
                        'password' => Hash::make($validated['password']),
                        'user_type' => UserType::ADMIN,
                        'school_id' => $school->id,
                        'approval_status' => 'approved',
                    ]);
                },
                runMigrations: true
            );
        } catch (\Throwable $exception) {
            $school->delete();

            throw $exception;
        }

        if ($school->url) {
            $request->session()->flash('workspace_url', $school->url);
        }

        return redirect()
            ->route('home')
            ->with('status', 'Your workspace is ready. Sign in using your new school address.');
    }

    private function normalizeSubdomain(string $value): string
    {
        $normalized = Str::lower($value);
        $normalized = preg_replace('/[^a-z0-9-]/', '-', $normalized);
        $normalized = trim((string) $normalized, '-');
        $normalized = preg_replace('/-+/', '-', $normalized ?? '');

        if ($normalized === '') {
            throw ValidationException::withMessages([
                'subdomain' => 'Enter a valid subdomain using letters, numbers, and hyphens only.',
            ]);
        }

        return $normalized;
    }
}

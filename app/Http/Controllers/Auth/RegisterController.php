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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(Request $request): View
    {
        $school = $request->attributes->get('currentSchool');

        if ($school instanceof School) {
            return view('auth.register-tenant', [
                'school' => $school,
            ]);
        }

        return view('auth.register', [
            'baseDomain' => $this->baseDomain(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $school = $request->attributes->get('currentSchool');

        if ($school instanceof School) {
            return $this->registerTenantUser($request, $school);
        }

        return $this->registerSchool($request);
    }

    private function registerTenantUser(Request $request, School $school): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
        $baseDomain = $this->baseDomain();

        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:50'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
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

        [$school, $user] = DB::transaction(function () use ($validated, $subdomain, $baseDomain) {
            $domain = $baseDomain ? $subdomain . '.' . $baseDomain : null;

            $school = School::create([
                'name' => $validated['school_name'],
                'code' => Str::upper(Str::random(8)),
                'subdomain' => $subdomain,
                'domain' => $domain,
            ]);

            $user = User::create([
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'password' => Hash::make($validated['password']),
                'user_type' => UserType::ADMIN,
                'school_id' => $school->id,
            ]);

            return [$school, $user];
        });

        Auth::login($user);
        $request->session()->regenerate();

        if ($school->url) {
            $request->session()->flash('workspace_url', $school->url);
        }

        return redirect()->route('dashboard')->with('status', 'Welcome aboard! Your workspace is ready.');
    }

    private function baseDomain(): ?string
    {
        $domain = config('tenancy.central_domain');

        if (! $domain) {
            return null;
        }

        if (str_contains($domain, '://')) {
            $parsed = parse_url($domain, PHP_URL_HOST);
            return $parsed ?: null;
        }

        return ltrim($domain, '.');
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

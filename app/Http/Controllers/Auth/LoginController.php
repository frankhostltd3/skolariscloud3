<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\User;
use App\Services\TenantDatabaseManager;
use App\Support\CentralDomain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    use ThrottlesLogins;

    public function __construct(private TenantDatabaseManager $tenants)
    {
    }

    public function create(Request $request): View
    {
        return view('auth.login', [
            'school' => $request->attributes->get('currentSchool'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Check if user is locked out
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);
            $this->sendLockoutResponse($request);
        }

        $remember = $request->boolean('remember');

        $school = $request->attributes->get('currentSchool');

        if (! $school && app()->environment('local')) {
            $resolved = $this->resolveSchoolForEmail($credentials['email']);

            if ($resolved instanceof School) {
                $school = $resolved;
                $request->attributes->set('currentSchool', $school);
                app()->instance('currentSchool', $school);

                $this->tenants->connect($school);
            }
        }

        if (! $school && ! app()->environment('local')) {
            throw ValidationException::withMessages([
                'email' => 'Please sign in from your school\'s unique address.',
            ]);
        }

        if (! $school) {
            throw ValidationException::withMessages([
                'email' => 'We could not determine which school to sign you into. Open your workspace URL and try again.',
            ]);
        }

        $attemptCredentials = [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ];

        $attemptCredentials['school_id'] = $school->id;

        if (! Auth::attempt($attemptCredentials, $remember)) {
            // Increment failed login attempts
            $this->incrementLoginAttempts($request);

            return back()
                ->withErrors(['email' => 'These credentials do not match our records for that school.'])
                ->onlyInput('email');
        }

        // Clear login attempts on successful login
        $this->clearLoginAttempts($request);

        $request->session()->regenerate();

        if ($school instanceof School) {
            $request->session()->put('tenant_school_id', $school->id);

            $domain = $school->domain ?? CentralDomain::tenantDomain($school->subdomain, $request);
            if ($domain) {
                $request->session()->put('tenant_school_domain', $domain);
            }
        } else {
            $request->session()->forget('tenant_school_id');
            $request->session()->forget('tenant_school_domain');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->forget('tenant_school_id');
        $request->session()->forget('tenant_school_domain');
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    private function resolveSchoolForEmail(string $email): ?School
    {
        $candidates = School::query()
            ->orderBy('id')
            ->get();

        foreach ($candidates as $candidate) {
            if (empty($candidate->database) && empty($candidate->domain) && empty($candidate->subdomain)) {
                continue;
            }

            try {
                $userExists = $this->tenants->runFor($candidate, function () use ($email) {
                    return User::query()->where('email', $email)->exists();
                });
            } catch (\Throwable $exception) {
                continue;
            }

            if ($userExists) {
                return $candidate->fresh();
            }
        }

        return null;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\User;
use App\Support\TenantAccessConfigurator;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Models\Domain;

class RegistrationController extends Controller
{
    public function create(Request $request): View
    {
        $baseDomain = $this->baseDomain($request);

        return view('register', [
            'baseDomain' => $baseDomain,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $baseDomain = $this->baseDomain($request);

        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/i'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'string', 'lowercase', 'email:rfc', 'max:255'],
            'password' => ['required', 'confirmed', new \App\Rules\SecurePassword()],
        ], [
            'subdomain.regex' => __('The subdomain may only contain letters, numbers, and hyphens (no leading or trailing hyphen).'),
        ]);

        $subdomain = strtolower($validated['subdomain']);
        $domain = $subdomain . '.' . $baseDomain;

        if (\in_array($domain, config('tenancy.central_domains', []), true)) {
            return back()->withInput()->withErrors([
                'subdomain' => __('This subdomain is reserved. Please choose another.'),
            ]);
        }

        if (Domain::query()->where('domain', $domain)->exists()) {
            return back()->withInput()->withErrors([
                'subdomain' => __('This school address is already taken. Please choose another.'),
            ]);
        }

        /** @var TenantWithDatabase|null $tenant */
        $tenant = null;

        try {
            /** @var TenantWithDatabase $tenant */
            $tenant = Tenant::create([
                'name' => $validated['school_name'],
                'plan' => 'trial',
                'contact_email' => $validated['admin_email'],
            ]);

            try {
                $tenant->domains()->create(['domain' => $domain]);
            } catch (\Throwable $domainException) {
                $tenant->delete();

                throw $domainException;
            }

            $tenant->run(function () use ($validated) {
                app(TenantAccessConfigurator::class)->seed(includeSampleUsers: false);

                $admin = User::create([
                    'name' => $validated['admin_name'],
                    'email' => $validated['admin_email'],
                    'password' => Hash::make($validated['password']),
                ]);

                if (method_exists($admin, 'assignRole')) {
                    $admin->assignRole('Admin');
                }
            });
        } catch (\Throwable $e) {
            if ($tenant !== null) {
                try {
                    $tenant->delete();
                } catch (\Throwable $cleanupException) {
                    Log::warning('Failed to clean up tenant after registration failure', [
                        'tenant_id' => $tenant->getKey(),
                        'message' => $cleanupException->getMessage(),
                    ]);
                }
            }

            Log::error('Registration failed', [
                'message' => $e->getMessage(),
                'tenant_id' => $tenant?->getKey(),
                'domain' => $domain,
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->withInput()->withErrors([
                'general' => __('We could not complete your registration. Please try again or contact support.'),
            ]);
        }

        $configuredUrl = (string) config('app.url');
        $scheme = parse_url($configuredUrl, PHP_URL_SCHEME) ?: $request->getScheme();
        $scheme = $scheme ?: 'http';

        return redirect()->route('landing', ['locale' => $request->route('locale')])
            ->with('status', __('Your school is now registered! Sign in at :url', ['url' => $scheme . '://' . $domain]));
    }

    private function baseDomain(Request $request): string
    {
        $configured = parse_url((string) config('app.url'), PHP_URL_HOST);
        $host = $configured ?: $request->getHost();

        return (string) preg_replace('/^www\./', '', $host);
    }
}

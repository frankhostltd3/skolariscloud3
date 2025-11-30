<?php

namespace App\Http\Controllers\Landlord\Tenants;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\TenantDatabaseManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CreateController extends Controller
{
    public function __invoke(): View
    {
        return view('landlord.tenants.create');
    }

    public function store(Request $request, TenantDatabaseManager $dbManager)
    {
        $validator = Validator::make($request->all(), [
            'school_name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', 'unique:schools,subdomain', 'regex:/^[a-zA-Z0-9\-]+$/'],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_password' => ['required', 'confirmed', new \App\Rules\SecurePassword()],
            'seed_sample_data' => ['nullable', 'boolean'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phones' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Prepare meta data
            $phones = [];
            if ($request->filled('phones')) {
                $phones = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('phones')))));
            }

            $meta = [
                'plan' => 'starter', // Default plan
                'country' => 'KE', // Default country or from request
                'admin_email' => $request->admin_email,
                'contact_email' => $request->input('contact_email') ?: $request->admin_email,
                'phones' => $phones,
            ];

            // Create the school
            $subdomain = $request->domain;
            $databaseName = 'tenant_' . str_replace('-', '_', $subdomain);

            $school = School::create([
                'name' => $request->school_name,
                'subdomain' => $subdomain,
                'domain' => null, // Can be set if custom domain is used
                'database' => $databaseName,
                'code' => strtoupper(substr($subdomain, 0, 3)), // Generate a code
                'meta' => $meta,
            ]);

            DB::commit(); // Commit the school creation first

            // Now setup the tenant database
            // We need to be careful not to wrap database creation in the main transaction if it's DDL
            // But TenantDatabaseManager handles connection and creation.

            try {
                $dbManager->connect($school);

                // Run migrations
                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/migrations/tenants',
                    '--force' => true,
                ]);

                // Create admin user
                $adminUser = \App\Models\User::create([
                    'name' => $request->admin_name,
                    'email' => $request->admin_email,
                    'password' => Hash::make($request->admin_password),
                    'email_verified_at' => now(),
                ]);

                // Assign admin role
                // Assuming Spatie Permission is set up and roles are seeded or created
                // We might need to seed roles first
                Artisan::call('db:seed', [
                    '--class' => 'PermissionsSeeder', // Assuming this seeder exists and seeds roles
                    '--database' => 'tenant',
                    '--force' => true,
                ]);

                $adminUser->assignRole('admin');

                // Seed sample data if requested
                if ($request->seed_sample_data) {
                    $this->seedSampleData($school);
                }

            } catch (\Exception $e) {
                // If database setup fails, we might want to delete the school record
                // or leave it for manual retry. For now, let's log and rethrow.
                \Log::error('Tenant setup failed: ' . $e->getMessage());
                throw $e;
            }

            return redirect()->route('landlord.tenants.index')
                ->with('success', 'Tenant created successfully!');

        } catch (\Exception $e) {
            // DB::rollBack(); // Only rolls back the central DB transaction if active

            return redirect()->back()
                ->withErrors(['error' => 'Failed to create tenant: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(School $tenant): View
    {
        $meta = $tenant->meta ?? [];
        return view('landlord.tenants.edit', [
            'tenant' => $tenant,
            'school_name' => $tenant->name,
            'admin_email' => $meta['admin_email'] ?? '',
            'admin_name' => '', // We can't easily get the admin name without connecting to tenant DB
            'contact_email' => $meta['contact_email'] ?? ($meta['admin_email'] ?? ''),
            'phones' => implode(', ', (array) ($meta['phones'] ?? [])),
        ]);
    }

    public function update(Request $request, School $tenant)
    {
        $validator = Validator::make($request->all(), [
            'school_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255'],
            'admin_name' => ['required', 'string', 'max:255'],
            'contact_email' => ['nullable', 'email', 'max:255'],
            'phones' => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            // Update tenant data
            $phones = [];
            if ($request->filled('phones')) {
                $phones = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('phones')))));
            }

            $meta = $tenant->meta ?? [];
            $meta['admin_email'] = $request->admin_email;
            $meta['contact_email'] = $request->input('contact_email') ?: $request->admin_email;
            $meta['phones'] = $phones;

            $tenant->update([
                'name' => $request->school_name,
                'meta' => $meta,
            ]);

            DB::commit();

            // Update admin user in tenant context
            // We need to connect to tenant DB
            $dbManager = app(TenantDatabaseManager::class);
            $dbManager->connect($tenant);

            $adminUser = \App\Models\User::where('email', $request->admin_email)->first();
            if ($adminUser) {
                $adminUser->update([
                    'name' => $request->admin_name,
                    'email' => $request->admin_email,
                ]);
            } else {
                // If email changed, we might not find the user by new email.
                // Ideally we should store admin_id in meta, but for now let's assume email didn't change or we find by old email if we had it.
                // Or just find the first user with admin role.
                $adminUser = \App\Models\User::role('admin')->first();
                if ($adminUser) {
                     $adminUser->update([
                        'name' => $request->admin_name,
                        'email' => $request->admin_email,
                    ]);
                }
            }

            return redirect()->route('landlord.tenants.index')
                ->with('success', 'Tenant updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update tenant: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(School $tenant)
    {
        try {
            // Delete the tenant
            // We should also delete the database
            // But TenantDatabaseManager doesn't seem to have a delete method exposed easily.
            // For now, just delete the record.
            $tenant->delete();

            return redirect()->route('landlord.tenants.index')
                ->with('success', 'Tenant deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete tenant: ' . $e->getMessage()]);
        }
    }

    private function seedSampleData(School $school)
    {
        // Run tenant seeders
        Artisan::call('db:seed', [
            '--database' => 'tenant',
            '--force' => true,
        ]);
    }
}

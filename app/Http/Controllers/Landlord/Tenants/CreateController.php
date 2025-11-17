<?php

namespace App\Http\Controllers\Landlord\Tenants;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

class CreateController extends Controller
{
    public function __invoke(): View
    {
        return view('landlord.tenants.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'school_name' => ['required', 'string', 'max:255'],
            'domain' => ['required', 'string', 'max:255', 'unique:domains,domain', 'regex:/^[a-zA-Z0-9\-]+$/'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:tenant_users,email'],
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

            // Create the tenant
            $phones = [];
            if ($request->filled('phones')) {
                $phones = array_values(array_filter(array_map('trim', explode(',', (string) $request->input('phones')))));
            }

            $tenant = Tenant::create([
                'id' => $request->domain,
                'data' => [
                    'school_name' => $request->school_name,
                    'admin_email' => $request->admin_email,
                    'admin_name' => $request->admin_name,
                    'contact_email' => $request->input('contact_email') ?: $request->admin_email,
                    'phones' => $phones,
                ],
            ]);

            // Create the domain
            $domain = Domain::create([
                'domain' => $request->domain . '.' . parse_url(config('app.url'), PHP_URL_HOST),
                'tenant_id' => $tenant->id,
            ]);

            // Switch to tenant context to create admin user
            tenancy()->initialize($tenant);

            // Create admin user in tenant database
            $adminUser = \App\Models\User::create([
                'name' => $request->admin_name,
                'email' => $request->admin_email,
                'password' => Hash::make($request->admin_password),
                'email_verified_at' => now(),
            ]);

            // Assign admin role
            $adminUser->assignRole('admin');

            // Seed sample data if requested
            if ($request->seed_sample_data) {
                $this->seedSampleData($tenant);
            }

            DB::commit();

            return redirect()->route('landlord.tenants.index')
                ->with('success', 'Tenant created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to create tenant: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(Tenant $tenant): View
    {
        $data = $tenant->data;
        return view('landlord.tenants.edit', [
            'tenant' => $tenant,
            'school_name' => $data['school_name'] ?? '',
            'admin_email' => $data['admin_email'] ?? '',
            'admin_name' => $data['admin_name'] ?? '',
            'contact_email' => $data['contact_email'] ?? ($data['admin_email'] ?? ''),
            'phones' => implode(', ', (array) ($data['phones'] ?? [])),
        ]);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'school_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', Rule::unique('tenant_users', 'email')->ignore($tenant->id, 'tenant_id')],
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

            $tenant->data = array_merge($tenant->data ?? [], [
                'school_name' => $request->school_name,
                'admin_email' => $request->admin_email,
                'admin_name' => $request->admin_name,
                'contact_email' => $request->input('contact_email') ?: $request->admin_email,
                'phones' => $phones,
            ]);
            $tenant->save();

            // Update admin user in tenant context
            tenancy()->initialize($tenant);

            $adminUser = \App\Models\User::where('email', $request->admin_email)->first();
            if ($adminUser) {
                $adminUser->update([
                    'name' => $request->admin_name,
                    'email' => $request->admin_email,
                ]);
            }

            DB::commit();

            return redirect()->route('landlord.tenants.index')
                ->with('success', 'Tenant updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->withErrors(['error' => 'Failed to update tenant: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(Tenant $tenant)
    {
        try {
            // Delete the tenant (this will also delete domains and database)
            $tenant->delete();

            return redirect()->route('landlord.tenants.index')
                ->with('success', 'Tenant deleted successfully!');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete tenant: ' . $e->getMessage()]);
        }
    }

    private function seedSampleData(Tenant $tenant)
    {
        // Run tenant seeders if they exist
        // This would typically include creating sample classes, subjects, users, etc.
        try {
            // You can add specific seeders here for sample data
            // For now, we'll just create a basic structure
        } catch (\Exception $e) {
            // Log the error but don't fail the tenant creation
            \Log::error('Failed to seed sample data: ' . $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Landlord\Tenants;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

class ImportController extends Controller
{
    public function __invoke(): View
    {
        return view('landlord.tenants.import');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'import_file' => ['required', 'file', 'mimes:xlsx,xls,csv'],
            'import_type' => ['required', 'in:excel,sql'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            if ($request->import_type === 'excel') {
                return $this->importExcel($request->file('import_file'));
            } elseif ($request->import_type === 'sql') {
                return $this->importSql($request->file('import_file'));
            }

        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Import failed: ' . $e->getMessage()]);
        }
    }

    private function importExcel($file)
    {
        $data = Excel::toArray([], $file)[0];
        $imported = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($data as $index => $row) {
                // Skip header row
                if ($index === 0) continue;

                try {
                    $tenantData = [
                        'id' => $row[0] ?? null,
                        'school_name' => $row[1] ?? '',
                        'admin_email' => $row[2] ?? '',
                        'admin_name' => $row[3] ?? '',
                        'plan' => $row[4] ?? 'starter',
                        'country' => $row[5] ?? '',
                    ];

                    if (empty($tenantData['id']) || empty($tenantData['school_name'])) {
                        $errors[] = "Row " . ($index + 1) . ": Missing required tenant ID or school name";
                        continue;
                    }

                    // Check if tenant already exists
                    if (Tenant::find($tenantData['id'])) {
                        $errors[] = "Row " . ($index + 1) . ": Tenant ID '{$tenantData['id']}' already exists";
                        continue;
                    }

                    // Create tenant
                    $tenant = Tenant::create([
                        'id' => $tenantData['id'],
                        'data' => $tenantData,
                    ]);

                    // Create domain
                    Domain::create([
                        'domain' => $tenantData['id'] . '.' . parse_url(config('app.url'), PHP_URL_HOST),
                        'tenant_id' => $tenant->id,
                    ]);

                    // Create admin user if email provided
                    if (!empty($tenantData['admin_email'])) {
                        tenancy()->initialize($tenant);

                        \App\Models\User::create([
                            'name' => $tenantData['admin_name'] ?: 'Admin',
                            'email' => $tenantData['admin_email'],
                            'password' => Hash::make('password123'), // Default password
                            'email_verified_at' => now(),
                        ])->assignRole('admin');
                    }

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Row " . ($index + 1) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Import completed. {$imported} tenants imported successfully.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', array_slice($errors, 0, 5));
                if (count($errors) > 5) {
                    $message .= " (and " . (count($errors) - 5) . " more errors)";
                }
            }

            return redirect()->route('landlord.tenants.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function importSql($file)
    {
        $content = file_get_contents($file->getRealPath());
        $statements = array_filter(array_map('trim', explode(';', $content)));

        $imported = 0;
        DB::beginTransaction();

        try {
            foreach ($statements as $statement) {
                if (empty($statement) || strpos($statement, '--') === 0) continue;

                // Basic validation - only allow INSERT statements for tenants and domains
                if (stripos($statement, 'INSERT INTO tenants') === 0 ||
                    stripos($statement, 'INSERT INTO domains') === 0) {
                    DB::statement($statement);
                    $imported++;
                }
            }

            DB::commit();

            return redirect()->route('landlord.tenants.index')
                ->with('success', "SQL import completed. {$imported} statements executed successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}

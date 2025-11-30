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
use Maatwebsite\Excel\Facades\Excel;

class ImportController extends Controller
{
    public function __invoke(): View
    {
        return view('landlord.tenants.import');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'import_file' => ['required', 'file', 'mimes:xlsx,xls,csv,sql'],
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
                    // Mapping based on TenantsExport columns:
                    // 0: ID, 1: Name, 2: Code, 3: Subdomain, 4: Domain, 5: Database, 6: Plan, 7: Country, 8: Admin Email

                    $schoolData = [
                        'name' => $row[1] ?? '',
                        'code' => $row[2] ?? '',
                        'subdomain' => $row[3] ?? '',
                        'domain' => $row[4] ?? null,
                        'database' => $row[5] ?? '',
                        'meta' => [
                            'plan' => $row[6] ?? 'starter',
                            'country' => $row[7] ?? '',
                            'admin_email' => $row[8] ?? '',
                        ]
                    ];

                    if (empty($schoolData['subdomain']) || empty($schoolData['name'])) {
                        // Fallback to old format if subdomain is missing (maybe row[0] was ID/subdomain)
                        if (!empty($row[0]) && !empty($row[1])) {
                             $schoolData['subdomain'] = $row[0];
                             $schoolData['name'] = $row[1];
                             $schoolData['meta']['admin_email'] = $row[2] ?? '';
                             // row[3] was admin name, we don't have a place for it in meta usually, but let's ignore
                             $schoolData['meta']['plan'] = $row[4] ?? 'starter';
                             $schoolData['meta']['country'] = $row[5] ?? '';
                             $schoolData['database'] = 'tenant_' . str_replace('-', '_', $schoolData['subdomain']);
                             $schoolData['code'] = strtoupper(substr($schoolData['subdomain'], 0, 3));
                        } else {
                            $errors[] = "Row " . ($index + 1) . ": Missing required subdomain or school name";
                            continue;
                        }
                    }

                    // Check if school already exists
                    if (School::where('subdomain', $schoolData['subdomain'])->exists()) {
                        $errors[] = "Row " . ($index + 1) . ": Subdomain '{$schoolData['subdomain']}' already exists";
                        continue;
                    }

                    // Create school
                    $school = School::create($schoolData);

                    // Setup database and admin user
                    // Note: This is a heavy operation to do in a loop.
                    // Ideally we should queue this. For now, we'll do it synchronously but catch errors.

                    try {
                        $dbManager = app(TenantDatabaseManager::class);
                        $dbManager->connect($school);

                        // Run migrations
                        Artisan::call('migrate', [
                            '--database' => 'tenant',
                            '--path' => 'database/migrations/tenants',
                            '--force' => true,
                        ]);

                        // Create admin user if email provided
                        if (!empty($schoolData['meta']['admin_email'])) {
                            \App\Models\User::create([
                                'name' => 'Admin', // We don't have name in export, default to Admin
                                'email' => $schoolData['meta']['admin_email'],
                                'password' => Hash::make('password123'), // Default password
                                'email_verified_at' => now(),
                            ])->assignRole('admin');
                        }
                    } catch (\Exception $e) {
                        $errors[] = "Row " . ($index + 1) . " (DB Setup): " . $e->getMessage();
                        // We don't rollback the school creation here to allow manual retry/fix?
                        // Or maybe we should. Let's just log error.
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

                // Basic validation - only allow INSERT statements for schools
                if (stripos($statement, 'INSERT INTO schools') === 0) {
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

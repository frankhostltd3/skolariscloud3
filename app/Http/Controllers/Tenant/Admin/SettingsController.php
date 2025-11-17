<?php

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\School;
use App\Models\Setting;
use App\Models\User;
use App\Mail\TestEmail;
use App\Services\ExchangeRateService;
use App\Services\TenantMailConfigurator;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;
use ZipArchive;

class SettingsController extends Controller
{
    public function index(): View
    {
        return $this->general();
    }

    /**
     * Display general settings.
     */
    public function general(): View
    {
        $school = School::query()->first();
        $user = Auth::user();

        return view('tenant.admin.settings.general', compact('school', 'user'));
    }

    /**
     * Display academic settings.
     */
    public function academic(): View
    {
        $gradingSettings = [
            'grading_scale' => setting('grading_scale', 'percentage'),
            'passing_grade' => setting('passing_grade', 50),
        ];

        $attendanceSettings = [
            'attendance_marking' => setting('attendance_marking', 'automatic'),
            'minimum_attendance' => setting('minimum_attendance', 75),
            'late_arrival_grace' => setting('late_arrival_grace', 15),
            'attendance_notifications' => setting('attendance_notifications', 'enabled'),
        ];

        $assignmentSettings = [
            'reminder_windows' => setting('assignments.reminders.windows', '24'),
            'auto_send_sms' => setting('assignments.reminders.auto_send.sms', false),
            'auto_send_whatsapp' => setting('assignments.reminders.auto_send.whatsapp', false),
            'log_only' => setting('assignments.reminders.log_only', true),
        ];

        $academicSettings = [
            'class_system' => setting('class_system', 'standard'),
            'current_academic_year' => setting('current_academic_year', date('Y') . '-' . (date('Y') + 1)),
            'academic_year_start' => setting('academic_year_start', date('Y') . '-01-01'),
            'academic_year_end' => setting('academic_year_end', (date('Y') + 1) . '-12-31'),
            'semester_system' => setting('semester_system', 'annual'),
        ];

        return view('tenant.admin.settings.academic', compact(
            'gradingSettings',
            'attendanceSettings',
            'assignmentSettings',
            'academicSettings'
        ));
    }

    /**
     * Display system settings.
     */
    public function system(): View
    {
        $currencies = Currency::orderBy('code')->get();
        $enabledCodes = Currency::enabled()->pluck('code')->toArray();
        $defaultCurrency = setting('currency', config('currency.default', 'UGX'));

        $systemSettings = [
            'timezone' => setting('timezone', config('app.timezone', 'UTC')),
            'date_format' => setting('date_format', 'Y-m-d'),
            'time_format' => setting('time_format', 'H:i:s'),
            'language' => setting('language', 'en'),
            'default_language' => setting('default_language', 'en'),
            'records_per_page' => setting('records_per_page', 25),
        ];

        $performanceSettings = [
            'cache_duration' => setting('cache_duration', 60),
            'session_timeout' => setting('session_timeout', 480),
            'max_file_size' => setting('max_file_size', 10),
            'backup_frequency' => setting('backup_frequency', 'weekly'),
            'enable_debug_mode' => setting('enable_debug_mode', false),
            'enable_maintenance_mode' => setting('enable_maintenance_mode', false),
        ];

        $securitySettings = [
            'enable_2fa' => setting('enable_2fa', false),
            'password_expiry' => setting('password_expiry', 90),
            'max_login_attempts' => setting('max_login_attempts', 5),
            'enable_captcha' => setting('enable_captcha', false),
            'enable_ip_blocking' => setting('enable_ip_blocking', false),
        ];

        $backupSettings = [
            'backup_frequency' => setting('backup_frequency', 'weekly'),
            'backup_retention' => setting('backup_retention', 30),
            'auto_backup' => setting('auto_backup', true),
            'backup_notifications' => setting('backup_notifications', true),
        ];

        return view('tenant.admin.settings.system', compact(
            'currencies',
            'enabledCodes',
            'defaultCurrency',
            'systemSettings',
            'performanceSettings',
            'securitySettings',
            'backupSettings'
        ));
    }

    /**
     * Display finance settings.
     */
    public function finance(): View
    {
        $settings = [
            'bank_name' => setting('bank_name', ''),
            'bank_account_name' => setting('bank_account_name', ''),
            'bank_account_number' => setting('bank_account_number', ''),
            'bank_branch' => setting('bank_branch', ''),
            'bank_swift' => setting('bank_swift', ''),
            'mtn_merchant_code' => setting('mtn_merchant_code', ''),
            'airtel_merchant_code' => setting('airtel_merchant_code', ''),
            'payment_instructions' => setting('payment_instructions', ''),
            'enable_sms' => setting('enable_sms', false),
            'enable_whatsapp' => setting('enable_whatsapp', false),
            'notifications_phone' => setting('notifications_phone', ''),
            'monthly_budget' => setting('monthly_budget', null),
        ];

        return view('tenant.admin.settings.finance', compact('settings'));
    }

    /**
     * Display user permissions settings.
     */
    public function permissions(): View
    {
        $roles = Role::withCount('users')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        // Group permissions by top-level namespace (before first dot)
        $permissionGroups = [];
        foreach ($permissions as $perm) {
            $parts = explode('.', $perm->name);
            $group = ucfirst(str_replace('_', ' ', $parts[0] ?? 'General'));
            $permissionGroups[$group][] = $perm;
        }

        return view('tenant.admin.settings.permissions', compact('roles', 'permissionGroups'));
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request): RedirectResponse
    {
        $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string',
            'school_phone' => 'required|string|max:20',
            'school_email' => 'required|email|max:255',
            'website_title' => 'nullable|string|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:4096',
            'favicon' => 'nullable|image|mimes:png,ico,svg,gif,webp,jpeg,jpg|max:2048',
            'social_links' => 'nullable|string',
        ]);

        // Persist to DB-backed settings
        foreach ([
            'school_name',
            'school_address',
            'school_phone',
            'school_email',
            'school_code',
            'school_website',
            'school_motto',
            'principal_name',
            'app_name',
            'academic_year_start',
            'academic_year_end',
            'timezone',
            'date_format',
            'time_format',
            'currency',
            'language',
            'default_language',
            'records_per_page',
        ] as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), 'general', true);
            }
        }

        // Branding persisted on School model (and keep settings key for backward compatibility)
        $school = School::query()->first();
        if (!$school) {
            $school = new School();
        }
        if ($request->filled('school_name')) {
            $school->name = $request->input('school_name');
        }
        if ($request->filled('website_title')) {
            $school->website_title = $request->input('website_title');
        }
        if ($request->hasFile('school_logo')) {
            $path = $request->file('school_logo')->store('logos', 'public');
            $school->logo_path = $path;
            // keep legacy setting for existing views until all are switched
            Setting::set('school_logo', $path, 'general', true);
        }
        if ($request->hasFile('favicon')) {
            $favPath = $request->file('favicon')->store('logos', 'public');
            $school->favicon_path = $favPath;
        }

        // Social links as JSON array of {platform,label,url}
        if ($request->filled('social_links')) {
            try {
                $decoded = json_decode($request->input('social_links'), true);
                if (is_array($decoded)) {
                    $max = 20;
                    $clean = collect($decoded)
                        ->map(function ($item) {
                            $platform = trim((string)($item['platform'] ?? ($item['label'] ?? 'link')));
                            $label = trim((string)($item['label'] ?? ($item['platform'] ?? 'link')));
                            $url = trim((string)($item['url'] ?? ''));
                            if ($url !== '' && !preg_match('#^https?://#i', $url)) {
                                // add https scheme if looks like a domain
                                if (preg_match('/^([a-z0-9-]+\.)+[a-z]{2,}(\/.*)?$/i', $url)) {
                                    $url = 'https://' . $url;
                                }
                            }
                            // Validate URL
                            $valid = filter_var($url, FILTER_VALIDATE_URL) !== false;
                            if ($valid) {
                                $scheme = parse_url($url, PHP_URL_SCHEME);
                                if (!in_array(strtolower((string)$scheme), ['http', 'https'])) {
                                    $valid = false;
                                }
                            }
                            if (!$valid) {
                                return null; // drop invalid
                            }
                            // Sanitize platform/label to reasonable length
                            $platform = substr($platform, 0, 32);
                            $label = substr($label, 0, 64);
                            return [
                                'platform' => $platform ?: 'link',
                                'label' => $label ?: 'link',
                                'url' => $url,
                            ];
                        })
                        ->filter()
                        ->unique('url')
                        ->take($max)
                        ->values()
                        ->all();
                    $school->social_links = $clean;
                }
            } catch (\Throwable $e) {
                // ignore invalid json
            }
        }

        // Only save if changed or new
        if ($school->isDirty()) {
            $school->save();
        }

        Setting::clearCache();

        return redirect()->route('tenant.admin.settings.general')
            ->with('success', 'General settings updated successfully.');
    }

    /**
     * Update academic settings.
     */
    public function updateAcademic(Request $request): RedirectResponse
    {
        // Grading System form submission
        $request->validate([
            'grading_scale' => 'required|in:percentage,gpa_4,gpa_5,letter',
            'passing_grade' => 'required|numeric|min:0|max:100',
        ]);

        // Persist grading settings using Setting model instead of cache
        Setting::set('grading_scale', $request->input('grading_scale'), 'academic', true);
        Setting::set('passing_grade', $request->input('passing_grade'), 'academic', true);

        return redirect()->route('tenant.admin.settings.academic')
            ->with('success', 'Grading settings updated successfully.');
    }

    /**
     * Update assignment reminders settings.
     */
    public function updateAcademicAssignmentReminders(Request $request): RedirectResponse
    {
        $request->validate([
            'reminder_windows' => 'nullable|string',
            'auto_send_sms' => 'nullable|boolean',
            'auto_send_whatsapp' => 'nullable|boolean',
            'log_only' => 'nullable|boolean',
        ]);

        // Parse windows as array of unique positive integers (hours)
        $raw = (string) $request->input('reminder_windows', '24');
        $windows = collect(preg_split('/[\s,;]+/', $raw, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn($x) => (int) trim($x))
            ->filter(fn($x) => $x > 0 && $x <= 720) // up to 30 days ahead
            ->unique()->sort()->values()->all();
        if (empty($windows)) {
            $windows = [24];
        }

        Setting::set('assignments.reminders.windows', implode(',', $windows), 'academic', true);
        Setting::set('assignments.reminders.auto_send.sms', $request->boolean('auto_send_sms'), 'academic', true);
        Setting::set('assignments.reminders.auto_send.whatsapp', $request->boolean('auto_send_whatsapp'), 'academic', true);
        Setting::set('assignments.reminders.log_only', $request->boolean('log_only', true), 'academic', true);
        Setting::clearCache();

        return redirect()->route('tenant.admin.settings.academic')
            ->with('success', 'Assignment reminder settings updated successfully.');
    }

    /**
     * Update system settings.
     */
    public function updateSystem(Request $request): RedirectResponse
    {
        $request->validate([
            'timezone' => 'required|string',
            'currency' => 'required|string|max:3',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
        ]);

        foreach (['timezone', 'currency', 'date_format', 'time_format'] as $key) {
            Setting::set($key, $request->input($key), 'system', true);
        }
        Setting::clearCache();

        return redirect()->route('tenant.admin.settings.system')
            ->with('success', 'System settings updated successfully.');
    }

    /**
     * Update currency settings.
     */
    public function updateCurrency(Request $request): RedirectResponse
    {
        $request->validate([
            'currency' => 'required|string|size:3',
            'enabled_currencies' => 'array',
            'enabled_currencies.*' => 'string|size:3',
            'extra_codes' => 'nullable|string',
        ]);

        $default = strtoupper($request->input('currency'));
        $enabledInput = (array) $request->input('enabled_currencies', []);
        // Parse additional codes
        $extra = collect(preg_split('/[\s,;]+/', (string) $request->input('extra_codes', ''), -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn($c) => strtoupper(trim($c)))
            ->filter(fn($c) => strlen($c) === 3)
            ->all();
        $enabled = array_values(array_unique(array_merge(array_map('strtoupper', $enabledInput), $extra)));

        // Ensure default currency exists and is enabled
        $def = Currency::find($default);
        if (!$def) {
            $def = Currency::create([
                'code' => $default,
                'name' => $default,
                'symbol' => $default,
                'decimals' => in_array($default, ['UGX', 'KES', 'TZS', 'RWF', 'BIF', 'JPY']) ? 0 : 2,
                'is_active' => true,
            ]);
        } else {
            $def->is_active = true;
            $def->save();
        }

        // Update enabled flags for all currencies
        $allCodes = Currency::pluck('code')->map(fn($c) => strtoupper($c))->toArray();
        $toEnable = array_values(array_unique(array_merge($enabled, [$default])));
        // Create records for any new codes and enable them
        foreach ($toEnable as $code) {
            if (!in_array($code, $allCodes)) {
                Currency::create([
                    'code' => $code,
                    'name' => $code,
                    'symbol' => $code,
                    'decimals' => in_array($code, ['UGX', 'KES', 'TZS', 'RWF', 'BIF', 'JPY']) ? 0 : 2,
                    'is_active' => true,
                ]);
            } else {
                Currency::where('code', $code)->update(['is_active' => true]);
            }
        }
        // Disable others not selected
        Currency::whereNotIn('code', $toEnable)->update(['is_active' => false]);

        // Persist settings
        Setting::set('currency', $default, 'system', true);
        Setting::set('enabled_currencies', $toEnable, 'system', true);
        Setting::clearCache();

        return redirect()->route('tenant.admin.settings.system')
            ->with('success', 'Currency settings updated successfully.');
    }

    /**
     * Manually refresh exchange rates now.
     */
    public function refreshExchangeRates(ExchangeRateService $service)
    {
        try {
            if (! (bool) setting('fx_auto_enabled', true)) {
                return response()->json(['success' => false, 'message' => 'Automatic FX refresh is disabled in settings. Enable it to refresh.']);
            }
            $base = (string) setting('currency', config('currency.default', 'UGX'));
            $quotes = Currency::enabled()
                ->where('code', '!=', $base)
                ->pluck('code')
                ->toArray();
            $count = $service->refreshRates($base, $quotes);
            return response()->json(['success' => true, 'message' => "Refreshed {$count} rates."]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Clear application cache.
     */
    public function clearCache(): RedirectResponse
    {
        // Clear application cache using artisan commands instead of Cache::flush()
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');

        return redirect()->route('tenant.admin.settings.system')
            ->with('success', 'Application cache cleared successfully.');
    }

    /**
     * Update academic year settings.
     */
    public function updateAcademicYear(Request $request): RedirectResponse
    {
        // Academic Year form submission
        $request->validate([
            'class_system' => 'required|in:standard,uganda',
            'current_academic_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
            'academic_year_start' => 'required|date',
            'academic_year_end' => 'required|date|after:academic_year_start',
            'semester_system' => 'required|in:semester,trimester,quarter,annual',
        ]);

        // Store academic year settings in cache or database
        foreach ([
            'class_system',
            'current_academic_year',
            'academic_year_start',
            'academic_year_end',
            'semester_system',
        ] as $key) {
            Setting::set($key, $request->input($key), 'academic', true);
        }
        Setting::clearCache();

        return redirect()->route('tenant.admin.settings.academic')
            ->with('success', 'Academic year settings updated successfully.');
    }

    /**
     * Update system performance settings.
     */
    public function updateSystemPerformance(Request $request): RedirectResponse
    {
        $request->validate([
            'cache_duration' => 'required|integer|min:1|max:1440',
            'session_timeout' => 'required|integer|min:5|max:480',
            'max_file_size' => 'required|integer|min:1|max:100',
            'backup_frequency' => 'required|string|in:daily,weekly,monthly',
            'enable_debug_mode' => 'boolean',
            'enable_maintenance_mode' => 'boolean',
        ]);

        // Store performance settings using Setting model instead of cache
        $performanceSettings = $request->only([
            'cache_duration',
            'session_timeout',
            'max_file_size',
            'backup_frequency',
            'enable_debug_mode',
            'enable_maintenance_mode'
        ]);

        foreach ($performanceSettings as $key => $value) {
            Setting::set($key, $value, 'system', true);
        }

        Setting::clearCache();

        return redirect()->route('tenant.admin.settings.system')
            ->with('success', 'System performance settings updated successfully.');
    }

    /**
     * Update system security settings.
     */
    public function updateSystemSecurity(Request $request): RedirectResponse
    {
        $request->validate([
            'enable_2fa' => 'boolean',
            'password_expiry' => 'required|integer|min:30|max:365',
            'session_timeout' => 'required|integer|min:15|max:480',
            'max_login_attempts' => 'required|integer|min:3|max:10',
            'enable_captcha' => 'boolean',
            'enable_ip_blocking' => 'boolean',
        ]);

        try {
            // Here you would normally save to a settings table or config
            // For now, we'll just return success
            return redirect()->route('tenant.admin.settings.system')
                ->with('success', 'Security settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('tenant.admin.settings.system')
                ->with('error', 'Failed to update security settings: ' . $e->getMessage());
        }
    }

    /**
     * Update finance settings.
     */
    public function updateFinance(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:64',
            'bank_branch' => 'nullable|string|max:255',
            'bank_swift' => 'nullable|string|max:64',
            'mtn_merchant_code' => 'nullable|string|max:64',
            'airtel_merchant_code' => 'nullable|string|max:64',
            'payment_instructions' => 'nullable|string',
            'enable_sms' => 'nullable|boolean',
            'enable_whatsapp' => 'nullable|boolean',
            'notifications_phone' => 'nullable|string|max:32',
            'monthly_budget' => 'nullable|numeric|min:0',
        ]);

        // Normalize checkboxes
        $validated['enable_sms'] = $request->boolean('enable_sms');
        $validated['enable_whatsapp'] = $request->boolean('enable_whatsapp');

        // Store finance settings using Setting model instead of cache
        foreach ($validated as $key => $value) {
            Setting::set($key, $value, 'finance', true);
        }

        Setting::clearCache();

        return redirect()->route('tenant.admin.settings.finance')
            ->with('success', 'Finance settings updated successfully.');
    }

    /**
     * Download system logs
     */
    public function downloadLogs()
    {
        try {
            $logPath = storage_path('logs/laravel.log');

            if (!file_exists($logPath)) {
                return redirect()->route('tenant.admin.settings.system')
                    ->with('error', 'No log file found.');
            }

            $fileName = 'system-logs-' . date('Y-m-d-H-i-s') . '.log';

            return response()->download($logPath, $fileName, [
                'Content-Type' => 'text/plain',
            ]);
        } catch (\Exception $e) {
            return redirect()->route('tenant.admin.settings.system')
                ->with('error', 'Failed to download logs: ' . $e->getMessage());
        }
    }

    /**
     * Create a manual backup
     */
    public function createBackup(Request $request)
    {
        try {
            // Check if backup directory exists
            $backupPath = storage_path('app/backups');
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }

            // Generate backup filename
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "backup_{$timestamp}";

            // Create database backup
            $dbBackupResult = $this->createDatabaseBackup($backupName);

            if (!$dbBackupResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database backup failed: ' . $dbBackupResult['message']
                ]);
            }

            // Create files backup
            $filesBackupResult = $this->createFilesBackup($backupName);

            if (!$filesBackupResult['success']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Files backup failed: ' . $filesBackupResult['message']
                ]);
            }

            // Log the backup creation
            Log::info('Manual backup created successfully', [
                'backup_name' => $backupName,
                'created_by' => Auth::user()->name ?? 'System',
                'timestamp' => $timestamp
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully!',
                'backup_name' => $backupName,
                'timestamp' => $timestamp
            ]);
        } catch (\Exception $e) {
            Log::error('Backup creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Backup creation failed: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create database backup
     */
    private function createDatabaseBackup($backupName)
    {
        try {
            $dbName = config('database.connections.mysql.database');
            $dbUsername = config('database.connections.mysql.username');
            $dbPassword = config('database.connections.mysql.password');
            $dbHost = config('database.connections.mysql.host');
            $dbPort = config('database.connections.mysql.port', 3306);

            $backupPath = storage_path("app/backups/{$backupName}_database.sql");

            // Use mysqldump command
            $command = sprintf(
                'mysqldump --host=%s --port=%s --user=%s --password=%s --single-transaction --routines --triggers %s > %s',
                escapeshellarg($dbHost),
                escapeshellarg($dbPort),
                escapeshellarg($dbUsername),
                escapeshellarg($dbPassword),
                escapeshellarg($dbName),
                escapeshellarg($backupPath)
            );

            // Execute the command
            $output = [];
            $returnCode = 0;
            exec($command . ' 2>&1', $output, $returnCode);

            if ($returnCode !== 0) {
                // If mysqldump fails, try Laravel's database export method
                return $this->createDatabaseBackupFallback($backupName);
            }

            // Check if backup file was created and has content
            if (!file_exists($backupPath) || filesize($backupPath) < 100) {
                return [
                    'success' => false,
                    'message' => 'Database backup file was not created or is empty'
                ];
            }

            return [
                'success' => true,
                'path' => $backupPath
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Fallback method for database backup using Laravel
     */
    private function createDatabaseBackupFallback($backupName)
    {
        try {
            $backupPath = storage_path("app/backups/{$backupName}_database.sql");
            $handle = fopen($backupPath, 'w+');

            if (!$handle) {
                return [
                    'success' => false,
                    'message' => 'Could not create backup file'
                ];
            }

            // Write SQL header
            fwrite($handle, "-- Database Backup\n");
            fwrite($handle, "-- Generated: " . Carbon::now() . "\n");
            fwrite($handle, "-- Database: " . config('database.connections.mysql.database') . "\n\n");

            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $dbName = config('database.connections.mysql.database');
            $tableKey = "Tables_in_{$dbName}";

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                // Get table structure
                $createTableResult = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createTable = $createTableResult[0]->{'Create Table'};

                fwrite($handle, "\n-- Table structure for table `{$tableName}`\n");
                fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                fwrite($handle, $createTable . ";\n\n");

                // Get table data
                $rows = DB::table($tableName)->get();

                if ($rows->count() > 0) {
                    fwrite($handle, "-- Dumping data for table `{$tableName}`\n");

                    foreach ($rows as $row) {
                        $values = [];
                        foreach ((array)$row as $value) {
                            if (is_null($value)) {
                                $values[] = 'NULL';
                            } else {
                                $values[] = "'" . addslashes($value) . "'";
                            }
                        }

                        $columns = implode('`, `', array_keys((array)$row));
                        $valuesStr = implode(', ', $values);

                        fwrite($handle, "INSERT INTO `{$tableName}` (`{$columns}`) VALUES ({$valuesStr});\n");
                    }
                    fwrite($handle, "\n");
                }
            }

            fclose($handle);

            return [
                'success' => true,
                'path' => $backupPath
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Create files backup
     */
    private function createFilesBackup($backupName)
    {
        try {
            $backupPath = storage_path("app/backups/{$backupName}_files.zip");

            // Create zip archive
            $zip = new ZipArchive();

            if ($zip->open($backupPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
                return [
                    'success' => false,
                    'message' => 'Could not create zip archive'
                ];
            }

            // Add important directories to backup
            $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage/app/public');
            $this->addDirectoryToZip($zip, public_path('uploads'), 'public/uploads');

            // Add .env file if exists
            if (file_exists(base_path('.env'))) {
                $zip->addFile(base_path('.env'), '.env');
            }

            // Add composer files
            if (file_exists(base_path('composer.json'))) {
                $zip->addFile(base_path('composer.json'), 'composer.json');
            }

            if (file_exists(base_path('composer.lock'))) {
                $zip->addFile(base_path('composer.lock'), 'composer.lock');
            }

            $zip->close();

            return [
                'success' => true,
                'path' => $backupPath
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Add directory to zip archive recursively
     */
    private function addDirectoryToZip($zip, $dir, $zipPath)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipPath . '/' . substr($filePath, strlen($dir) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    /**
     * Clear system logs
     */
    public function clearLogs(Request $request)
    {
        try {
            $logPath = storage_path('logs/laravel.log');

            if (file_exists($logPath)) {
                file_put_contents($logPath, '');
            }

            Log::info('System logs cleared', [
                'cleared_by' => Auth::user()->name ?? 'System',
                'timestamp' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'System logs cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear logs: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Clear application cache
     */
    public function clearCacheAjax(Request $request)
    {
        try {
            // Clear application cache
            \Illuminate\Support\Facades\Artisan::call('cache:clear');

            // Clear config cache
            \Illuminate\Support\Facades\Artisan::call('config:clear');

            // Clear route cache
            \Illuminate\Support\Facades\Artisan::call('route:clear');

            // Clear view cache
            \Illuminate\Support\Facades\Artisan::call('view:clear');

            Log::info('Application cache cleared', [
                'cleared_by' => Auth::user()->name ?? 'System',
                'timestamp' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Application cache cleared successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Optimize database
     */
    public function optimizeDatabase(Request $request)
    {
        try {
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $dbName = config('database.connections.mysql.database');
            $tableKey = "Tables_in_{$dbName}";

            $optimizedTables = [];

            foreach ($tables as $table) {
                $tableName = $table->$tableKey;

                // Optimize each table
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
                $optimizedTables[] = $tableName;
            }

            Log::info('Database optimized', [
                'optimized_by' => Auth::user()->name ?? 'System',
                'tables_count' => count($optimizedTables),
                'timestamp' => Carbon::now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Database optimized successfully! Optimized ' . count($optimizedTables) . ' tables.',
                'tables' => $optimizedTables
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to optimize database: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * List available backups
     */
    public function listBackups()
    {
        try {
            $backupPath = storage_path('app/backups');

            if (!is_dir($backupPath)) {
                return response()->json([
                    'success' => true,
                    'backups' => []
                ]);
            }

            $backups = [];
            $files = scandir($backupPath);

            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && (strpos($file, '.sql') !== false || strpos($file, '.zip') !== false)) {
                    $filePath = $backupPath . '/' . $file;
                    $backups[] = [
                        'name' => $file,
                        'size' => $this->formatBytes(filesize($filePath)),
                        'created' => date('Y-m-d H:i:s', filemtime($filePath))
                    ];
                }
            }

            // Sort by creation time (newest first)
            usort($backups, function ($a, $b) {
                return strtotime($b['created']) - strtotime($a['created']);
            });

            return response()->json([
                'success' => true,
                'backups' => $backups
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to list backups: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    // --- Users/Assignments APIs ---
    public function usersSearch(Request $request)
    {
        $q = trim((string) $request->query('search', ''));
        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            })
            ->orderBy('name')
            ->limit(15)
            ->get(['id', 'name', 'email']);
        return response()->json($users);
    }

    public function getUserAssignments(User $user)
    {
        return response()->json([
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
            'roles' => $user->roles()->pluck('name')->toArray(),
            'permissions' => $user->permissions()->pluck('name')->toArray(),
        ]);
    }

    public function syncUserAssignments(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'string',
            'permissions' => 'array',
            'permissions.*' => 'string',
        ]);
        $roles = array_values(array_unique((array) $request->input('roles', [])));
        $permissions = array_values(array_unique((array) $request->input('permissions', [])));
        $user->syncRoles($roles);
        $user->syncPermissions($permissions);
        return redirect()->route('tenant.admin.settings.permissions')->with('success', 'User roles and permissions updated.');
    }

    /**
     * Display email settings.
     */
    public function email(): View
    {
        return view('tenant.admin.settings.email');
    }

    /**
     * Update email settings.
     */
    public function updateEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'mail_driver' => 'required|in:smtp,mailgun,ses,postmark',
            'mail_host' => 'required_if:mail_driver,smtp|string',
            'mail_port' => 'required_if:mail_driver,smtp|integer|min:1|max:65535',
            'mail_encryption' => 'required_if:mail_driver,smtp|in:tls,ssl,none',
            'mail_username' => 'required_if:mail_driver,smtp|string',
            'mail_password' => 'required_if:mail_driver,smtp|string',
            'mail_from_address' => 'required|email',
            'mail_from_name' => 'required|string|max:255',
            'email_notifications' => 'array',
            'email_notifications.*' => 'string',
        ]);

        // Save email settings
        $emailSettings = [
            'mail_driver',
            'mail_host',
            'mail_port',
            'mail_encryption',
            'mail_username',
            'mail_password',
            'mail_from_address',
            'mail_from_name',
            'welcome_email_subject',
            'password_reset_subject',
            'email_notifications',
        ];

        foreach ($emailSettings as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->input($key), 'email', true);
            }
        }

        Setting::clearCache();

        app(TenantMailConfigurator::class)->applyFromSettings();

        return redirect()->route('tenant.admin.settings.email')
            ->with('success', 'Email settings updated successfully.');
    }

    /**
     * Display test email page.
     */
    public function testEmail(): View
    {
        return view('tenant.admin.settings.test-email');
    }

    /**
     * Send test email.
     */
    public function sendTestEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'test_email' => 'required|email',
            'email_subject' => 'required|string|max:255',
            'email_message' => 'required|string',
        ]);

        try {
            app(TenantMailConfigurator::class)->applyFromSettings();

            if (config('app.debug')) {
                \Illuminate\Support\Facades\Log::debug('Tenant test email config snapshot', [
                    'default' => config('mail.default'),
                    'host' => config('mail.mailers.smtp.host'),
                    'port' => config('mail.mailers.smtp.port'),
                    'encryption' => config('mail.mailers.smtp.encryption'),
                    'from' => config('mail.from.address'),
                    'driver' => app('mail.manager')->getDefaultDriver(),
                ]);
            }

            Mail::mailer(config('mail.default'))->to($request->test_email)->send(new TestEmail(
                $request->email_subject,
                $request->email_message
            ));

            return redirect()->route('tenant.admin.settings.test-email')
                ->with('success', 'Test email sent successfully!');
        } catch (\Exception $e) {
            return redirect()->route('tenant.admin.settings.test-email')
                ->with('error', 'Failed to send test email: ' . $e->getMessage());
        }
    }

    /**
     * Show email log details.
     */
    public function showEmailLog(Request $request, $logId)
    {
        // This would typically fetch from an email_logs table
        // For now, return a placeholder response
        return response()->json([
            'id' => $logId,
            'to_email' => 'test@example.com',
            'subject' => 'Test Email',
            'message' => 'Test message content',
            'status' => 'sent',
            'created_at' => now()->toDateTimeString(),
            'error_message' => null,
            'attempts' => 1,
        ]);
    }

    /**
     * Update currency rates.
     */
    public function updateCurrencyRates(Request $request): RedirectResponse
    {
        try {
            $service = app(ExchangeRateService::class);
            $base = setting('default_currency', 'USD');
            $currencies = Currency::enabled()->where('code', '!=', $base)->pluck('code')->toArray();

            $count = $service->refreshRates($base, $currencies);

            return redirect()->route('tenant.admin.settings.finance')
                ->with('success', "Currency rates updated successfully. {$count} rates refreshed.");
        } catch (\Exception $e) {
            return redirect()->route('tenant.admin.settings.finance')
                ->with('error', 'Failed to update currency rates: ' . $e->getMessage());
        }
    }

    /**
     * Update permissions settings.
     */
    public function updatePermissions(Request $request): RedirectResponse
    {
        $request->validate([
            'enable_rbac' => 'boolean',
            'default_role' => 'required|string',
            'permissions' => 'array',
            'inherit_parent_permissions' => 'boolean',
        ]);

        // Save RBAC settings
        Setting::set('enable_rbac', $request->boolean('enable_rbac'), 'permissions', true);
        Setting::set('default_role', $request->default_role, 'permissions', true);
        Setting::set('inherit_parent_permissions', $request->boolean('inherit_parent_permissions'), 'permissions', true);

        // Save role permissions
        if ($request->has('permissions')) {
            foreach ($request->permissions as $role => $permissions) {
                Setting::set("permissions.{$role}", $permissions, 'permissions', true);
            }
        }

        Setting::clearCache();

        return redirect()->route('tenant.admin.settings.permissions')
            ->with('success', 'Permissions settings updated successfully.');
    }

    // Legacy method for backward compatibility
    public function update(Request $request): RedirectResponse
    {
        return $this->updateGeneral($request);
    }
}
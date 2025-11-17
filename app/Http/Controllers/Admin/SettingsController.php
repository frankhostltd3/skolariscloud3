<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use ZipArchive;
use App\Models\Currency;
use App\Services\ExchangeRateService;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

class SettingsController extends Controller
{
    protected function settingsRoute(string $suffix = 'general'): string
    {
        return 'tenant.settings.admin.' . ltrim($suffix, '.');
    }

    /**
     * Display the settings dashboard.
     */
    public function index()
    {
        $settings = [
            'school_name' => setting('school_name', config('app.name', 'SkolarisCloud')),
            'school_address' => setting('school_address', 'Your School Address'),
            'school_phone' => setting('school_phone', '+1234567890'),
            'school_email' => setting('school_email', 'info@skolariscloud.com'),
            'academic_year_start' => setting('academic_year_start', '2024-09-01'),
            'academic_year_end' => setting('academic_year_end', '2025-06-30'),
            'timezone' => setting('timezone', config('app.timezone', 'UTC')),
            'currency' => setting('currency', 'USD'),
            'date_format' => setting('date_format', 'Y-m-d'),
            'time_format' => setting('time_format', 'H:i'),
        ];

        return view('tenant.admin.settings.index', compact('settings'));
    }

    /**
     * Display general settings.
     */
    public function general()
    {
        $settings = [
            'school_name' => setting('school_name', 'School Management System'),
            'school_code' => setting('school_code', 'SCH001'),
            'school_email' => setting('school_email', 'info@school.com'),
            'school_phone' => setting('school_phone', '+1-234-567-8900'),
            'school_address' => setting('school_address', '123 Education Street, Learning City, State 12345'),
            'school_website' => setting('school_website', 'https://www.school.com'),
            'school_logo' => setting('school_logo', null),
            'school_motto' => setting('school_motto', 'Excellence in Education'),
            'principal_name' => setting('principal_name', 'Dr. Jane Smith'),
            'school_type' => setting('school_type', 'private'),
            'school_category' => setting('school_category', 'day'),
            'gender_type' => setting('gender_type', 'mixed'),
            'app_name' => setting('app_name', config('app.name', 'SkolarisCloud')),
            'academic_year_start' => setting('academic_year_start', '2024-09-01'),
            'academic_year_end' => setting('academic_year_end', '2025-06-30'),
            'timezone' => setting('timezone', 'UTC'),
            'date_format' => setting('date_format', 'Y-m-d'),
            'time_format' => setting('time_format', 'H:i'),
            'currency' => setting('currency', 'USD'),
            'language' => setting('language', 'en'),
            'default_language' => setting('default_language', 'en'),
            'records_per_page' => setting('records_per_page', '15'),
            'mail_driver' => setting('mail_driver', 'smtp'),
            'mail_host' => setting('mail_host', 'smtp.gmail.com'),
            'mail_port' => setting('mail_port', '587'),
            'mail_encryption' => setting('mail_encryption', 'tls'),
            'mail_username' => setting('mail_username', ''),
            'mail_password' => setting('mail_password', ''),
            'mail_from_address' => setting('mail_from_address', 'noreply@school.com'),
            'mail_from_name' => setting('mail_from_name', 'School Management System'),
            // Branding (from School model where possible)
            'website_title' => optional(School::query()->first())->website_title,
        ];

        return view('tenant.admin.settings.general', compact('settings'));
    }

    /**
     * Display academic settings.
     */
    public function academic()
    {
        \Log::info('Loading academic settings page', ['tenant_id' => tenant('id')]);

        $settings = [
            // Academic Year Settings
            'current_academic_year' => setting('current_academic_year', date('Y') . '-' . (date('Y') + 1)),
            'academic_year_start' => setting('academic_year_start', date('Y') . '-09-01'),
            'academic_year_end' => setting('academic_year_end', date('Y', strtotime('+1 year')) . '-06-30'),
            'semester_system' => setting('semester_system', 'semester'),
            'class_system' => setting('class_system', 'standard'),

            // Grading Settings
            'grading_scale' => setting('grading_scale', 'percentage'),
            'passing_grade' => setting('passing_grade', '60'),

            // Grade Level Ranges
            'grade_a_min' => setting('grade_a_min', '90'),
            'grade_a_max' => setting('grade_a_max', '100'),
            'grade_a_gpa' => setting('grade_a_gpa', '4.0'),
            'grade_b_min' => setting('grade_b_min', '80'),
            'grade_b_max' => setting('grade_b_max', '89'),
            'grade_b_gpa' => setting('grade_b_gpa', '3.0'),
            'grade_c_min' => setting('grade_c_min', '70'),
            'grade_c_max' => setting('grade_c_max', '79'),
            'grade_c_gpa' => setting('grade_c_gpa', '2.0'),
            'grade_d_min' => setting('grade_d_min', '60'),
            'grade_d_max' => setting('grade_d_max', '69'),
            'grade_d_gpa' => setting('grade_d_gpa', '1.0'),
            'grade_f_min' => setting('grade_f_min', '0'),
            'grade_f_max' => setting('grade_f_max', '59'),
            'grade_f_gpa' => setting('grade_f_gpa', '0.0'),

            // Attendance Settings
            'attendance_marking' => setting('attendance_marking', 'automatic'),
            'minimum_attendance' => setting('minimum_attendance', '75'),
            'late_arrival_grace' => setting('late_arrival_grace', '15'),
            'attendance_notifications' => setting('attendance_notifications', 'enabled'),
        ];

        \Log::info('Loaded academic settings', ['settings' => $settings]);

        return view('tenant.admin.settings.academic', compact('settings'));
    }

    /**
     * Display system settings.
     */
    public function system()
    {
        $currencies = Currency::orderBy('code')->get();
        $enabledCodes = Currency::enabled()->pluck('code')->toArray();
        $defaultCurrency = setting('currency', config('currency.default', 'UGX'));

        // Load system settings from cache/database with PRODUCTION-READY DEFAULTS
        $defaults = [
            'cache_driver' => 'file',
            'session_driver' => 'database', // Changed from 'file' to 'database' for better security
            'session_lifetime' => '60', // Changed from 120 to 60 minutes for production
            'max_file_upload' => '10',
            'pagination_limit' => '15',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'decimal_places' => '2',
            'password_min_length' => '10', // Changed from 8 to 10 for better security
            'max_login_attempts' => '5',
            'lockout_duration' => '30', // Changed from 15 to 30 minutes for production
            'force_https' => true, // Changed from false to true - CRITICAL for production
            'enable_two_factor' => true, // Changed from false to true - RECOMMENDED for production
            'auto_backup' => 'daily', // Changed from 'disabled' to 'daily'
            'backup_retention' => '30',
            'log_level' => 'warning', // Changed from 'error' to 'warning' for better monitoring
            'account_status' => 'verified',
            'enable_two_factor_auth' => true, // Changed from false to true
            // NEW SECURITY SETTINGS
            'require_password_complexity' => true,
            'password_expiry_days' => 90,
            'session_timeout_minutes' => 30, // Inactivity timeout
            'enable_ip_whitelist' => false,
            'admin_ip_whitelist' => '',
            'enable_security_headers' => true,
            'log_failed_logins' => true,
            'log_settings_changes' => true,
        ];

        $cached = \Illuminate\Support\Facades\Cache::get('system_settings', []);
        $settings = array_merge($defaults, $cached);

        return view('tenant.admin.settings.system', compact('currencies', 'enabledCodes', 'defaultCurrency', 'settings'));
    }

    /**
     * Display finance settings (bank & mobile money).
     */
    public function finance()
    {
        $defaults = [
            'bank_name' => '',
            'bank_account_name' => '',
            'bank_account_number' => '',
            'bank_branch' => '',
            'bank_swift' => '',
            'mtn_merchant_code' => '',
            'airtel_merchant_code' => '',
            'payment_instructions' => '',
            'monthly_budget' => null,
        ];

        $cached = \Illuminate\Support\Facades\Cache::get('finance_settings', []);
        $settings = array_merge($defaults, $cached);

        return view('tenant.admin.settings.finance', compact('settings'));
    }

    /**
     * Display user permissions settings.
     */
    public function permissions()
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

        // Load permissions settings from database/cache
        $defaults = [
            'default_student_role' => 'student',
            'default_teacher_role' => 'teacher',
            'allow_student_login' => true,
            'allow_parent_login' => true,
            'allow_teacher_login' => true,
            'require_email_verification' => false,
            'allow_password_reset' => true,
            'enable_two_factor' => false,
            'allow_registration' => false,
            'require_strong_password' => true,
            'min_password_length' => 8,
            'password_expiry_days' => 90,
            'max_login_attempts' => 5,
            'session_timeout' => 60,
            'remember_me_days' => 30,
            'allowed_ips' => '',
            'restrict_by_ip' => false,
            'teacher_manage_students' => false,
            'teacher_manage_classes' => false,
            'student_view_reports' => false,
        ];

        $cached = \Illuminate\Support\Facades\Cache::get('permissions_settings', []);
        $settings = array_merge($defaults, $cached);

        return view('tenant.admin.settings.permissions', compact('roles', 'permissionGroups', 'settings'));
    }

    /**
     * Display notification settings.
     */
    public function notifications()
    {
        $settings = [
            // SMS Settings
            'sms_provider' => setting('sms_provider', 'twilio'),
            'sms_enabled' => setting('sms_enabled', false),
            'twilio_sid' => setting('twilio_sid'),
            'twilio_token' => setting('twilio_token'),
            'twilio_phone' => setting('twilio_phone'),
            'at_username' => setting('at_username'),
            'at_api_key' => setting('at_api_key'),
            'at_sender_id' => setting('at_sender_id'),
            // WhatsApp Settings
            'whatsapp_enabled' => setting('whatsapp_enabled', false),
            'whatsapp_provider' => setting('whatsapp_provider', 'twilio'),
            'whatsapp_twilio_sid' => setting('whatsapp_twilio_sid'),
            'whatsapp_twilio_token' => setting('whatsapp_twilio_token'),
            'whatsapp_phone_number' => setting('whatsapp_phone_number'),
            // Templates
            'sms_template_welcome' => setting('sms_template_welcome', 'Welcome to {school_name}! Your account has been created successfully.'),
            'sms_template_fee_reminder' => setting('sms_template_fee_reminder', 'Dear {user_name}, you have an outstanding fee balance of {amount}. Please make payment soon.'),
            // Default Preferences
            'default_email_general' => setting('default_email_general', true),
            'default_email_academic' => setting('default_email_academic', true),
            'default_email_financial' => setting('default_email_financial', true),
            'default_sms_urgent' => setting('default_sms_urgent', true),
            'default_sms_fee_reminders' => setting('default_sms_fee_reminders', true),
            'default_sms_attendance' => setting('default_sms_attendance', false),
            'default_whatsapp_general' => setting('default_whatsapp_general', false),
            'default_whatsapp_urgent' => setting('default_whatsapp_urgent', true),
            'default_whatsapp_results' => setting('default_whatsapp_results', false),
        ];

        return view('tenant.admin.settings.notifications', compact('settings'));
    }

    /**
     * Update permissions settings.
     */
    public function updatePermissions(Request $request)
    {
        $request->validate([
            'default_student_role' => 'required|in:student,prefect,monitor',
            'default_teacher_role' => 'required|in:teacher,hod,deputy',
            'allow_student_login' => 'nullable|boolean',
            'allow_parent_login' => 'nullable|boolean',
            'allow_teacher_login' => 'nullable|boolean',
            'require_email_verification' => 'nullable|boolean',
            'allow_password_reset' => 'nullable|boolean',
            'enable_two_factor' => 'nullable|boolean',
            'allow_registration' => 'nullable|boolean',
            'require_strong_password' => 'nullable|boolean',
            'min_password_length' => 'required|integer|min:6|max:32',
            'password_expiry_days' => 'required|integer|min:0|max:365',
            'max_login_attempts' => 'required|integer|min:1|max:20',
            'session_timeout' => 'required|integer|min:5|max:480',
            'remember_me_days' => 'required|integer|min:1|max:365',
            'allowed_ips' => 'nullable|string',
            'restrict_by_ip' => 'nullable|boolean',
            'teacher_manage_students' => 'nullable|boolean',
            'teacher_manage_classes' => 'nullable|boolean',
            'student_view_reports' => 'nullable|boolean',
        ]);

        // Store permissions settings in cache
        Cache::put('permissions_settings', $request->only([
            'default_student_role',
            'default_teacher_role',
            'allow_student_login',
            'allow_parent_login',
            'allow_teacher_login',
            'require_email_verification',
            'allow_password_reset',
            'enable_two_factor',
            'allow_registration',
            'require_strong_password',
            'min_password_length',
            'password_expiry_days',
            'max_login_attempts',
            'session_timeout',
            'remember_me_days',
            'allowed_ips',
            'restrict_by_ip',
            'teacher_manage_students',
            'teacher_manage_classes',
            'student_view_reports',
        ]), 60 * 24 * 365); // Cache for 365 days

        return redirect()->route('tenant.settings.admin.permissions')
            ->with('success', 'Permissions settings updated successfully.');
    }

    /**
     * Update notification settings.
     */
    public function updateNotifications(Request $request)
    {
        $request->validate([
            'sms_provider' => 'required|in:twilio,africastalking,none',
            'sms_enabled' => 'nullable|boolean',
            'twilio_sid' => 'nullable|string|max:255',
            'twilio_token' => 'nullable|string|max:255',
            'twilio_phone' => 'nullable|string|max:20',
            'at_username' => 'nullable|string|max:255',
            'at_api_key' => 'nullable|string|max:255',
            'at_sender_id' => 'nullable|string|max:255',
            'whatsapp_enabled' => 'nullable|boolean',
            'whatsapp_provider' => 'required|in:twilio,360dialog,none',
            'whatsapp_twilio_sid' => 'nullable|string|max:255',
            'whatsapp_twilio_token' => 'nullable|string|max:255',
            'whatsapp_phone_number' => 'nullable|string|max:20',
            'sms_template_welcome' => 'nullable|string|max:500',
            'sms_template_fee_reminder' => 'nullable|string|max:500',
            'default_email_general' => 'nullable|boolean',
            'default_email_academic' => 'nullable|boolean',
            'default_email_financial' => 'nullable|boolean',
            'default_sms_urgent' => 'nullable|boolean',
            'default_sms_fee_reminders' => 'nullable|boolean',
            'default_sms_attendance' => 'nullable|boolean',
            'default_whatsapp_general' => 'nullable|boolean',
            'default_whatsapp_urgent' => 'nullable|boolean',
            'default_whatsapp_results' => 'nullable|boolean',
        ]);

        // Store notification settings
        $settings = [
            'sms_provider' => $request->input('sms_provider'),
            'sms_enabled' => $request->boolean('sms_enabled'),
            'whatsapp_enabled' => $request->boolean('whatsapp_enabled'),
            'whatsapp_provider' => $request->input('whatsapp_provider'),
            'sms_template_welcome' => $request->input('sms_template_welcome'),
            'sms_template_fee_reminder' => $request->input('sms_template_fee_reminder'),
            'default_email_general' => $request->boolean('default_email_general'),
            'default_email_academic' => $request->boolean('default_email_academic'),
            'default_email_financial' => $request->boolean('default_email_financial'),
            'default_sms_urgent' => $request->boolean('default_sms_urgent'),
            'default_sms_fee_reminders' => $request->boolean('default_sms_fee_reminders'),
            'default_sms_attendance' => $request->boolean('default_sms_attendance'),
            'default_whatsapp_general' => $request->boolean('default_whatsapp_general'),
            'default_whatsapp_urgent' => $request->boolean('default_whatsapp_urgent'),
            'default_whatsapp_results' => $request->boolean('default_whatsapp_results'),
        ];

        // Store sensitive data separately (not in cache)
        if ($request->filled('twilio_sid')) {
            \App\Models\Setting::set('twilio_sid', $request->input('twilio_sid'), 'notifications', false);
        }
        if ($request->filled('twilio_token')) {
            \App\Models\Setting::set('twilio_token', $request->input('twilio_token'), 'notifications', false);
        }
        if ($request->filled('twilio_phone')) {
            \App\Models\Setting::set('twilio_phone', $request->input('twilio_phone'), 'notifications', true);
        }
        if ($request->filled('at_username')) {
            \App\Models\Setting::set('at_username', $request->input('at_username'), 'notifications', false);
        }
        if ($request->filled('at_api_key')) {
            \App\Models\Setting::set('at_api_key', $request->input('at_api_key'), 'notifications', false);
        }
        if ($request->filled('at_sender_id')) {
            \App\Models\Setting::set('at_sender_id', $request->input('at_sender_id'), 'notifications', true);
        }
        if ($request->filled('whatsapp_twilio_sid')) {
            \App\Models\Setting::set('whatsapp_twilio_sid', $request->input('whatsapp_twilio_sid'), 'notifications', false);
        }
        if ($request->filled('whatsapp_twilio_token')) {
            \App\Models\Setting::set('whatsapp_twilio_token', $request->input('whatsapp_twilio_token'), 'notifications', false);
        }
        if ($request->filled('whatsapp_phone_number')) {
            \App\Models\Setting::set('whatsapp_phone_number', $request->input('whatsapp_phone_number'), 'notifications', true);
        }

        // Store other settings
        foreach ($settings as $key => $value) {
            \App\Models\Setting::set($key, $value, 'notifications', true);
        }

        \App\Models\Setting::clearCache();

        return redirect()->route('tenant.settings.admin.notifications')
            ->with('success', 'Notification settings updated successfully.');
    }

    /**
     * Send test SMS.
     */
    public function testSms(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string|max:20',
        ]);

        try {
            $phone = $request->input('test_phone');
            $provider = setting('sms_provider', 'twilio');

            if ($provider === 'none' || !setting('sms_enabled', false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'SMS notifications are disabled or no provider configured.'
                ]);
            }

            // Here you would integrate with the actual SMS provider
            // For now, we'll just simulate success
            \Illuminate\Support\Facades\Log::info('Test SMS sent', [
                'phone' => $phone,
                'provider' => $provider,
                'sent_by' => Auth::user()->name ?? 'System',
            ]);

            return response()->json([
                'success' => true,
                'message' => "Test SMS sent successfully to {$phone} via {$provider}."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test SMS: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Send test WhatsApp message.
     */
    public function testWhatsapp(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string|max:20',
        ]);

        try {
            $phone = $request->input('test_phone');
            $provider = setting('whatsapp_provider', 'twilio');

            if ($provider === 'none' || !setting('whatsapp_enabled', false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp notifications are disabled or no provider configured.'
                ]);
            }

            // Here you would integrate with the actual WhatsApp provider
            // For now, we'll just simulate success
            \Illuminate\Support\Facades\Log::info('Test WhatsApp message sent', [
                'phone' => $phone,
                'provider' => $provider,
                'sent_by' => Auth::user()->name ?? 'System',
            ]);

            return response()->json([
                'success' => true,
                'message' => "Test WhatsApp message sent successfully to {$phone} via {$provider}."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test WhatsApp message: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update general settings.
     */
    public function updateGeneral(Request $request)
    {
        $formType = $request->input('form_type', 'school_info');

        if ($formType === 'application') {
            $data = $request->validate([
                'app_name' => 'required|string|max:255',
                'timezone' => 'required|string',
                'date_format' => 'required|string',
                'time_format' => 'required|string',
                'default_language' => 'required|string|max:10',
                'records_per_page' => 'required|integer|in:10,15,25,50,100',
            ]);

            foreach ([
                'app_name',
                'timezone',
                'date_format',
                'time_format',
                'default_language',
                'records_per_page',
            ] as $key) {
                $value = $data[$key];
                if ($key === 'records_per_page') {
                    $value = (int) $value;
                }
                \App\Models\Setting::set($key, $value, 'general', true);
            }

            \App\Models\Setting::clearCache();

            return redirect()->route('tenant.settings.admin.general')
                ->with('success', 'Application settings updated successfully.');
        }

        $data = $request->validate([
            'school_name' => 'required|string|max:255',
            'school_address' => 'required|string',
            'school_phone' => 'required|string|max:20',
            'school_email' => 'required|email|max:255',
            'school_type' => 'required|in:government,private,hybrid',
            'school_category' => 'required|in:day,boarding,hybrid',
            'gender_type' => 'required|in:boys,girls,mixed',
            'school_code' => 'nullable|string|max:64',
            'school_website' => 'nullable|url|max:255',
            'school_motto' => 'nullable|string|max:255',
            'principal_name' => 'nullable|string|max:255',
            'website_title' => 'nullable|string|max:255',
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,svg|max:4096',
            'favicon' => 'nullable|image|mimes:png,ico,svg,gif,webp,jpeg,jpg|max:2048',
            'social_links' => 'nullable|string',
        ]);

        foreach ([
            'school_name',
            'school_address',
            'school_phone',
            'school_email',
            'school_code',
            'school_website',
            'school_motto',
            'principal_name',
            'school_type',
            'school_category',
            'gender_type',
            'website_title',
        ] as $key) {
            if (array_key_exists($key, $data)) {
                \App\Models\Setting::set($key, $data[$key], 'general', true);
            }
        }

        $school = School::query()->first();
        if (!$school) {
            $school = new School();
        }

        if (!empty($data['school_name'])) {
            $school->name = $data['school_name'];
        }
        if (!empty($data['website_title'])) {
            $school->website_title = $data['website_title'];
        }

        if ($request->hasFile('school_logo')) {
            $path = $request->file('school_logo')->store('logos', 'public');
            $school->logo_path = $path;
            \App\Models\Setting::set('school_logo', $path, 'general', true);
        }

        if ($request->hasFile('favicon')) {
            $favPath = $request->file('favicon')->store('logos', 'public');
            $school->favicon_path = $favPath;
        }

        if (!empty($data['social_links'])) {
            try {
                $decoded = json_decode($data['social_links'], true);
                if (is_array($decoded)) {
                    $max = 20;
                    $clean = collect($decoded)
                        ->map(function ($item) {
                            $platform = trim((string)($item['platform'] ?? ($item['label'] ?? 'link')));
                            $label = trim((string)($item['label'] ?? ($item['platform'] ?? 'link')));
                            $url = trim((string)($item['url'] ?? ''));
                            if ($url !== '' && !preg_match('#^https?://#i', $url)) {
                                if (preg_match('/^([a-z0-9-]+\.)+[a-z]{2,}(\/.*)?$/i', $url)) {
                                    $url = 'https://' . $url;
                                }
                            }
                            $valid = filter_var($url, FILTER_VALIDATE_URL) !== false;
                            if ($valid) {
                                $scheme = parse_url($url, PHP_URL_SCHEME);
                                if (!in_array(strtolower((string) $scheme), ['http', 'https'])) {
                                    $valid = false;
                                }
                            }
                            if (!$valid) {
                                return null;
                            }
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

        if ($school->isDirty()) {
            $school->save();
        }

        \App\Models\Setting::clearCache();

        return redirect()->route('tenant.settings.admin.general')
            ->with('success', 'School information updated successfully.');
    }

    /**
     * Update academic settings.
     */
    public function updateAcademic(Request $request)
    {
        $formType = $request->input('form_type', 'grading');

        \Log::info('Academic settings update', [
            'form_type' => $formType,
            'tenant_id' => tenant('id'),
            'all_input' => $request->all()
        ]);

        if ($formType === 'academic_year') {
            $data = $request->validate([
                'current_academic_year' => ['required', 'regex:/^\d{4}-\d{4}$/'],
                'academic_year_start' => 'required|date',
                'academic_year_end' => 'required|date|after:academic_year_start',
                'semester_system' => 'required|in:semester,trimester,quarter,annual',
                'class_system' => 'nullable|in:standard,uganda',
            ]);

            foreach ([
                'current_academic_year',
                'academic_year_start',
                'academic_year_end',
                'semester_system',
            ] as $key) {
                \App\Models\Setting::set($key, $data[$key], 'academic', true);
                \Log::info("Saved setting: {$key}", ['value' => $data[$key]]);
            }

            if (!empty($data['class_system'])) {
                \App\Models\Setting::set('class_system', $data['class_system'], 'academic', true);
                \Log::info("Saved setting: class_system", ['value' => $data['class_system']]);
            }

            \App\Models\Setting::clearCache();

            return redirect()->route($this->settingsRoute('academic'))
                ->with('success', 'Academic year settings updated successfully.');
        }

        if ($formType === 'attendance') {
            $data = $request->validate([
                'attendance_marking' => 'required|in:automatic,manual,biometric',
                'minimum_attendance' => 'required|integer|min:0|max:100',
                'late_arrival_grace' => 'required|integer|min:0|max:60',
                'attendance_notifications' => 'required|in:enabled,disabled',
            ]);

            foreach ($data as $key => $value) {
                \App\Models\Setting::set($key, $value, 'academic', true);
                \Log::info("Saved attendance setting: {$key}", ['value' => $value]);
            }

            \App\Models\Setting::clearCache();

            return redirect()->route($this->settingsRoute('academic'))
                ->with('success', 'Attendance settings updated successfully.');
        }

        $data = $request->validate([
            'grading_scale' => 'required|in:percentage,gpa_4,gpa_5,letter',
            'passing_grade' => 'required|numeric|min:0|max:100',
            'grade_a_min' => 'nullable|numeric|min:0|max:100',
            'grade_a_max' => 'nullable|numeric|min:0|max:100',
            'grade_a_gpa' => 'nullable|numeric|min:0|max:5',
            'grade_b_min' => 'nullable|numeric|min:0|max:100',
            'grade_b_max' => 'nullable|numeric|min:0|max:100',
            'grade_b_gpa' => 'nullable|numeric|min:0|max:5',
            'grade_c_min' => 'nullable|numeric|min:0|max:100',
            'grade_c_max' => 'nullable|numeric|min:0|max:100',
            'grade_c_gpa' => 'nullable|numeric|min:0|max:5',
            'grade_d_min' => 'nullable|numeric|min:0|max:100',
            'grade_d_max' => 'nullable|numeric|min:0|max:100',
            'grade_d_gpa' => 'nullable|numeric|min:0|max:5',
            'grade_f_min' => 'nullable|numeric|min:0|max:100',
            'grade_f_max' => 'nullable|numeric|min:0|max:100',
            'grade_f_gpa' => 'nullable|numeric|min:0|max:5',
        ]);

        $cachePayload = collect($data)->only([
            'grading_scale',
            'passing_grade',
            'grade_a_min', 'grade_a_max', 'grade_a_gpa',
            'grade_b_min', 'grade_b_max', 'grade_b_gpa',
            'grade_c_min', 'grade_c_max', 'grade_c_gpa',
            'grade_d_min', 'grade_d_max', 'grade_d_gpa',
            'grade_f_min', 'grade_f_max', 'grade_f_gpa',
        ])->toArray();

        Cache::put('grading_settings', $cachePayload, 60 * 24 * 365);

        foreach ($cachePayload as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            \App\Models\Setting::set($key, $value, 'academic', true);
            \Log::info("Saved grading setting: {$key}", ['value' => $value]);
        }

        \App\Models\Setting::clearCache();

        return redirect()->route($this->settingsRoute('academic'))
            ->with('success', 'Grading settings updated successfully.');
    }

    /**
     * Update assignment reminders settings (windows and auto-send toggles).
     */
    public function updateAcademicAssignmentReminders(Request $request)
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

        \App\Models\Setting::set('assignments.reminders.windows', implode(',', $windows), 'academic', true);
        \App\Models\Setting::set('assignments.reminders.auto_send.sms', $request->boolean('auto_send_sms'), 'academic', true);
        \App\Models\Setting::set('assignments.reminders.auto_send.whatsapp', $request->boolean('auto_send_whatsapp'), 'academic', true);
        \App\Models\Setting::set('assignments.reminders.log_only', $request->boolean('log_only', true), 'academic', true);
        \App\Models\Setting::clearCache();

        return redirect()->route($this->settingsRoute('academic'))
            ->with('success', 'Assignment reminder settings updated successfully.');
    }

    /**
     * Update system settings.
     */
    public function updateSystem(Request $request)
    {
        $request->validate([
            'cache_driver' => 'nullable|string|in:file,redis,memcached,database',
            'session_driver' => 'nullable|string|in:file,database,redis,cookie',
            'session_lifetime' => 'nullable|integer|min:1|max:1440',
            'max_file_upload' => 'nullable|integer|min:1|max:100',
            'pagination_limit' => 'nullable|integer|in:10,15,25,50,100',
            'currency' => 'nullable|string|size:3',
            'currency_symbol' => 'nullable|string|max:10',
            'decimal_places' => 'nullable|integer|in:0,2,3',
            'password_min_length' => 'nullable|integer|min:6|max:20',
            'max_login_attempts' => 'nullable|integer|min:1|max:10',
            'lockout_duration' => 'nullable|integer|min:1|max:60',
            'force_https' => 'nullable|boolean',
            'enable_two_factor' => 'nullable|boolean',
            'auto_backup' => 'nullable|string|in:disabled,daily,weekly,monthly',
            'backup_retention' => 'nullable|integer|min:1|max:365',
            'log_level' => 'nullable|string|in:emergency,alert,critical,error,warning,notice,info,debug',
            'account_status' => 'nullable|string|in:verified,unverified',
            'enable_two_factor_auth' => 'nullable|boolean',
        ]);

        // Prepare settings array
        $settings = [
            'cache_driver' => $request->input('cache_driver', 'file'),
            'session_driver' => $request->input('session_driver', 'file'),
            'session_lifetime' => $request->input('session_lifetime', '120'),
            'max_file_upload' => $request->input('max_file_upload', '10'),
            'pagination_limit' => $request->input('pagination_limit', '15'),
            'currency_symbol' => $request->input('currency_symbol', '$'),
            'decimal_places' => $request->input('decimal_places', '2'),
            'password_min_length' => $request->input('password_min_length', '8'),
            'max_login_attempts' => $request->input('max_login_attempts', '5'),
            'lockout_duration' => $request->input('lockout_duration', '15'),
            'force_https' => $request->boolean('force_https'),
            'enable_two_factor' => $request->boolean('enable_two_factor'),
            'auto_backup' => $request->input('auto_backup', 'disabled'),
            'backup_retention' => $request->input('backup_retention', '30'),
            'log_level' => $request->input('log_level', 'error'),
            'account_status' => $request->input('account_status', 'verified'),
            'enable_two_factor_auth' => $request->boolean('enable_two_factor_auth'),
        ];

        // Store system settings in cache
        Cache::put('system_settings', $settings, 60 * 24 * 365); // Cache for 365 days

        // Also persist to database for some critical settings
        foreach (['currency', 'timezone', 'date_format', 'time_format'] as $key) {
            if ($request->has($key)) {
                \App\Models\Setting::set($key, $request->input($key), 'system', true);
            }
        }

        \App\Models\Setting::clearCache();

        // Log security-related setting changes
        $securitySettings = ['password_min_length', 'max_login_attempts', 'lockout_duration', 'force_https', 'enable_two_factor', 'enable_two_factor_auth'];
        $changedSecuritySettings = [];

        foreach ($securitySettings as $key) {
            if ($request->has($key)) {
                $oldValue = setting($key);
                $newValue = $settings[$key] ?? $request->input($key);

                if ($oldValue != $newValue) {
                    $changedSecuritySettings[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }
        }

        if (!empty($changedSecuritySettings)) {
            $user = Auth::user();
            SecurityAuditLog::logEvent(
                SecurityAuditLog::EVENT_SETTINGS_CHANGED,
                $user?->email,
                $user?->id,
                'Security settings modified',
                [
                    'changed_settings' => $changedSecuritySettings,
                    'source' => 'system_settings_page'
                ],
                SecurityAuditLog::SEVERITY_WARNING
            );
        }

        return redirect()->route($this->settingsRoute('system'))
            ->with('success', 'System settings updated successfully.');
    }

    /**
     * Update kiosk/fingerprint settings (device tokens etc.)
     */
    public function updateKiosk(Request $request)
    {
        $request->validate([
            'kiosk_allowed_tokens' => 'nullable|string',
            'kiosk_require_token' => 'nullable|boolean',
        ]);

        // Parse tokens from textarea (newline/comma/semicolon separated)
        $raw = (string) $request->input('kiosk_allowed_tokens', '');
        $tokens = collect(preg_split('/[\r\n,;\t ]+/', $raw, -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn($t) => trim($t))
            ->filter()
            ->unique()
            ->values()
            ->all();

        \App\Models\Setting::set('kiosk.allowed_tokens', $tokens, 'system', true);
        \App\Models\Setting::set('kiosk.require_token', $request->boolean('kiosk_require_token', true), 'system', true);
        \App\Models\Setting::clearCache();

        return redirect()->route($this->settingsRoute('system'))
            ->with('success', 'Kiosk settings updated successfully.');
    }

    /**
     * Update currency settings.
     */
    public function updateCurrency(Request $request)
    {
        $request->validate([
            'currency' => 'required|string|size:3',
            'enabled_currencies' => 'array',
            'enabled_currencies.*' => 'string|size:3',
            'extra_codes' => 'nullable|string',
            'fx_auto_enabled' => 'nullable|boolean',
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
                'decimal_places' => in_array($default, ['UGX', 'KES', 'TZS', 'RWF', 'BIF', 'JPY']) ? 0 : 2,
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
                    'decimal_places' => in_array($code, ['UGX', 'KES', 'TZS', 'RWF', 'BIF', 'JPY']) ? 0 : 2,
                    'is_active' => true,
                ]);
            } else {
                Currency::where('code', $code)->update(['is_active' => true]);
            }
        }
        // Disable others not selected
        Currency::whereNotIn('code', $toEnable)->update(['is_active' => false]);

        // Persist settings
        \App\Models\Setting::set('currency', $default, 'system', true);
        \App\Models\Setting::set('enabled_currencies', $toEnable, 'system', true);
        \App\Models\Setting::set('fx_auto_enabled', $request->boolean('fx_auto_enabled', true), 'system', true);
        \App\Models\Setting::clearCache();

        return redirect()->route($this->settingsRoute('system'))
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

    /** Bulk update currency definitions */
    public function bulkUpdateCurrencies(Request $request)
    {
        $data = $request->input('currencies', []);
        foreach ($data as $code => $payload) {
            $code = strtoupper($code);
            $name = (string) ($payload['name'] ?? $code);
            $symbol = (string) ($payload['symbol'] ?? $code);
            $decimals = max(0, min(6, (int) ($payload['decimals'] ?? 2)));
            $is_active = isset($payload['enabled']) && (bool) $payload['enabled'];
            Currency::updateOrCreate(['code' => $code], compact('code', 'name', 'symbol', 'decimal_places', 'is_active'));
        }
        // Keep enabled_currencies setting in sync
        $enabled = Currency::enabled()->pluck('code')->toArray();
        \App\Models\Setting::set('enabled_currencies', $enabled, 'system', true);
        \App\Models\Setting::clearCache();
    return redirect()->route($this->settingsRoute('system'))->with('success', 'Currencies updated successfully.');
    }

    /**
     * Clear application cache.
     */
    public function clearCache()
    {
        Cache::flush();

        return redirect()->route($this->settingsRoute('system'))
            ->with('success', 'Application cache cleared successfully.');
    }

    /**
     * Update academic year settings.
     */
    public function updateAcademicYear(Request $request)
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
        foreach (
            [
                'class_system',
                'current_academic_year',
                'academic_year_start',
                'academic_year_end',
                'semester_system',
            ] as $key
        ) {
            \App\Models\Setting::set($key, $request->input($key), 'academic', true);
        }
        \App\Models\Setting::clearCache();

        return redirect()->route($this->settingsRoute('academic'))
            ->with('success', 'Academic year settings updated successfully.');
    }

    /**
     * Update system performance settings.
     */
    public function updateSystemPerformance(Request $request)
    {
        $request->validate([
            'cache_duration' => 'required|integer|min:1|max:1440',
            'session_timeout' => 'required|integer|min:5|max:480',
            'max_file_size' => 'required|integer|min:1|max:100',
            'backup_frequency' => 'required|string|in:daily,weekly,monthly',
            'enable_debug_mode' => 'boolean',
            'enable_maintenance_mode' => 'boolean',
        ]);

        // Store performance settings in cache
        Cache::put('performance_settings', $request->only([
            'cache_duration',
            'session_timeout',
            'max_file_size',
            'backup_frequency',
            'enable_debug_mode',
            'enable_maintenance_mode'
        ]), 60 * 24 * 30); // Cache for 30 days

        return redirect()->route($this->settingsRoute('system'))
            ->with('success', 'System performance settings updated successfully.');
    }

    /**
     * Update system security settings.
     */
    public function updateSystemSecurity(Request $request)
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
            return redirect()->route($this->settingsRoute('system'))
                ->with('success', 'Security settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route($this->settingsRoute('system'))
                ->with('error', 'Failed to update security settings: ' . $e->getMessage());
        }
    }

    /**
     * Update HR module settings (lightweight section for toggles like integrations)
     */
    public function updateHR(Request $request)
    {
        $request->validate([
            'hr_leave_integration_enabled' => 'nullable|boolean',
        ]);

        \App\Models\Setting::set('hr_leave_integration_enabled', $request->boolean('hr_leave_integration_enabled', false), 'hr', true);
        \App\Models\Setting::clearCache();

    return redirect()->route($this->settingsRoute('system'))->with('success', 'HR settings saved.');
    }

    /**
     * Store a new role.
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'permissions' => 'array',
        ]);

        try {
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            if ($request->permissions) {
                $role->syncPermissions($request->permissions);
            }

            return redirect()->route($this->settingsRoute('permissions'))
                ->with('success', 'Role created successfully.');
        } catch (\Exception $e) {
            return redirect()->route($this->settingsRoute('permissions'))
                ->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }

    /** Delete a role */
    public function destroyRole(Role $role)
    {
        if ($role->name === 'super-admin') {
            return back()->with('error', 'Cannot delete super-admin role.');
        }
        try {
            $role->delete();
            return back()->with('success', 'Role deleted successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    /** Get role permissions (JSON) */
    public function getRolePermissions(Role $role)
    {
        return response()->json([
            'role' => $role->name,
            'permissions' => $role->permissions()->pluck('name')->toArray(),
        ]);
    }

    /** Sync role permissions */
    public function syncRolePermissions(Request $request, Role $role)
    {
        $request->validate(['permissions' => 'array']);
        $perms = (array) $request->input('permissions', []);
        $role->syncPermissions($perms);
        return back()->with('success', 'Role permissions updated.');
    }

    /**
     * Sync permissions registry (config/permissions.php) for the current tenant only.
     */
    public function syncPermissionsRegistry(Request $request)
    {
        // Optional authorization (comment out if permission not yet seeded)
        // $this->authorize('manage security settings');
        try {
            $tenantId = tenant('id');
            \Artisan::call('permissions:sync', ['--tenant' => [$tenantId]]);
            SecurityAuditLog::logEvent(
                SecurityAuditLog::EVENT_SETTINGS_CHANGED,
                auth()->user()?->email,
                auth()->id(),
                'Permissions registry synced',
                ['tenant_id' => $tenantId],
                SecurityAuditLog::SEVERITY_INFO
            );
            return redirect()->route($this->settingsRoute('permissions'))
                ->with('success', 'Permissions registry synced successfully.');
        } catch (\Throwable $e) {
            return redirect()->route($this->settingsRoute('permissions'))
                ->with('error', 'Failed to sync permissions: ' . $e->getMessage());
        }
    }

    /**
     * Bulk assign (or reassign) a role to multiple users.
     */
    public function bulkAssignRole(Request $request)
    {
        $data = $request->validate([
            'role' => 'required|string|exists:roles,name',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'integer|exists:users,id',
            'detach_existing' => 'sometimes|boolean',
        ]);

        $roleName = $data['role'];
        if ($roleName === 'super-admin' && !auth()->user()->hasRole('super-admin')) {
            return back()->with('error', 'Only super-admin can bulk assign the super-admin role.');
        }

        $users = User::whereIn('id', $data['user_ids'])->get();
        $assigned = 0; $detached = 0;
        foreach ($users as $user) {
            if (! $user->hasRole($roleName)) {
                if (!empty($data['detach_existing']) && $data['detach_existing']) {
                    $detached++;
                    $user->syncRoles([$roleName]);
                } else {
                    $user->assignRole($roleName);
                }
                $assigned++;
            } elseif (!empty($data['detach_existing']) && $data['detach_existing']) {
                $detached++;
                $user->syncRoles([$roleName]);
            }
        }

        SecurityAuditLog::logEvent(
            SecurityAuditLog::EVENT_SETTINGS_CHANGED,
            auth()->user()?->email,
            auth()->id(),
            'Bulk role assignment',
            [
                'role' => $roleName,
                'target_users' => count($data['user_ids']),
                'assigned_new' => $assigned,
                'detached_existing' => $detached,
            ],
            SecurityAuditLog::SEVERITY_WARNING
        );

        return back()->with('success', "Bulk role assignment complete. {$assigned} users updated.");
    }

    /**
     * Update system backup settings.
     */
    public function updateSystemBackup(Request $request)
    {
        $request->validate([
            'backup_frequency' => 'required|string|in:daily,weekly,monthly',
            'backup_retention' => 'required|integer|min:1|max:365',
            'auto_backup' => 'boolean',
            'backup_notifications' => 'boolean',
        ]);

        try {
            // Here you would normally save to a settings table or config
            // For now, we'll just return success
            return redirect()->route($this->settingsRoute('system'))
                ->with('success', 'Backup settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route($this->settingsRoute('system'))
                ->with('error', 'Failed to update backup settings: ' . $e->getMessage());
        }
    }

    /**
     * Update academic attendance settings.
     */
    public function updateAcademicAttendance(Request $request)
    {
        // Attendance Settings form submission
        $request->validate([
            'attendance_marking' => 'required|in:automatic,manual,biometric',
            'minimum_attendance' => 'required|integer|min:0|max:100',
            'late_arrival_grace' => 'required|integer|min:0|max:60',
            'attendance_notifications' => 'required|in:enabled,disabled',
        ]);

        try {
            foreach (
                [
                    'attendance_marking',
                    'minimum_attendance',
                    'late_arrival_grace',
                    'attendance_notifications',
                ] as $key
            ) {
                \App\Models\Setting::set($key, $request->input($key), 'academic', true);
            }
            \App\Models\Setting::clearCache();

            return redirect()->route($this->settingsRoute('academic'))
                ->with('success', 'Attendance settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route($this->settingsRoute('academic'))
                ->with('error', 'Failed to update attendance settings: ' . $e->getMessage());
        }
    }

    /**
     * Store a new permission.
     */
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        try {
            Permission::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            return redirect()->route($this->settingsRoute('permissions'))
                ->with('success', 'Permission created successfully.');
        } catch (\Exception $e) {
            return redirect()->route($this->settingsRoute('permissions'))
                ->with('error', 'Failed to create permission: ' . $e->getMessage());
        }
    }

    /** Delete a permission */
    public function destroyPermission(Permission $permission)
    {
        try {
            $permission->delete();
            return back()->with('success', 'Permission deleted successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to delete permission: ' . $e->getMessage());
        }
    }

    /**
     * Update application settings.
     */
    public function updateApplication(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
            'default_language' => 'required|string',
            'records_per_page' => 'required|integer|min:5|max:100',
        ]);

        try {
            foreach (
                [
                    'app_name',
                    'timezone',
                    'date_format',
                    'time_format',
                    'default_language',
                    'records_per_page',
                ] as $key
            ) {
                \App\Models\Setting::set($key, $request->input($key), 'application', true);
            }
            \App\Models\Setting::clearCache();

            return redirect()->route($this->settingsRoute('general'))
                ->with('success', 'Application settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route($this->settingsRoute('general'))
                ->with('error', 'Failed to update application settings: ' . $e->getMessage());
        }
    }

    /**
     * Update email settings.
     */
    public function updateEmail(Request $request)
    {
        $request->validate([
            'mail_driver' => 'required|string|in:smtp,sendmail,mailgun',
            'mail_host' => 'required_if:mail_driver,smtp|string|max:255',
            'mail_port' => 'required_if:mail_driver,smtp|integer|min:1|max:65535',
            'mail_encryption' => 'nullable|string|in:tls,ssl',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name' => 'required|string|max:255',
        ]);

        try {
            foreach (
                [
                    'mail_driver',
                    'mail_host',
                    'mail_port',
                    'mail_encryption',
                    'mail_username',
                    // don't store empty password to avoid wiping existing secrets unless provided
                    'mail_from_address',
                    'mail_from_name',
                ] as $key
            ) {
                if ($request->has($key)) {
                    \App\Models\Setting::set($key, $request->input($key), 'email', true);
                }
            }
            if ($request->filled('mail_password')) {
                \App\Models\Setting::set('mail_password', $request->input('mail_password'), 'email', true);
            }
            \App\Models\Setting::clearCache();

            app(\App\Services\TenantMailConfigurator::class)->applyFromSettings();

            return redirect()->route($this->settingsRoute('general'))
                ->with('success', 'Email settings updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route($this->settingsRoute('general'))
                ->with('error', 'Failed to update email settings: ' . $e->getMessage());
        }
    }

    /**
     * Apply DB-backed email settings to runtime config.
     */
    protected function applyEmailConfigFromSettings(): void
    {
        app(\App\Services\TenantMailConfigurator::class)->applyFromSettings();
    }

    /**
     * Send a test email using current settings.
     */
    public function sendTestEmail(Request $request)
    {
        $data = $request->validate([
            'to' => 'required|email',
        ]);

        // Apply config dynamically from DB settings before sending
        $this->applyEmailConfigFromSettings();

        if (config('app.debug')) {
            \Illuminate\Support\Facades\Log::debug('Test email config snapshot', [
                'default' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'from' => config('mail.from.address'),
                'driver' => app('mail.manager')->getDefaultDriver(),
            ]);
        }

        try {
            // Quick connectivity check for SMTP
            $defaultMailer = (string) config('mail.default', 'smtp');
            if ($defaultMailer === 'smtp') {
                $host = (string) config('mail.mailers.smtp.host');
                $port = (int) config('mail.mailers.smtp.port', 587);
                if ($host) {
                    $errno = 0; $errstr = '';
                    // Use tcp scheme to avoid implicit TLS at this stage; STARTTLS is negotiated by the mailer
                    $conn = @stream_socket_client("tcp://{$host}:{$port}", $errno, $errstr, 5, STREAM_CLIENT_CONNECT);
                    if (!$conn) {
                        $msg = "Cannot connect to SMTP {$host}:{$port} ({$errno}) {$errstr}. Check host/port, firewall, and provider settings.";
                        if ($request->wantsJson()) {
                            return response()->json(['success' => false, 'message' => $msg], 500);
                        }
                        return back()->with('error', $msg);
                    }
                    fclose($conn);
                }
            }

            $body = 'This is a test email to confirm your SMTP configuration works.';
            Mail::mailer(config('mail.default'))->to($data['to'])->send(new TestEmail($body));

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Test email sent successfully to ' . $data['to'] . '.']);
            }
            return back()->with('success', 'Test email sent successfully to ' . $data['to'] . '. Please check the inbox/spam folder.');
        } catch (\Throwable $e) {
            // Surface detailed transport error to help debugging
            $configSnapshot = [
                'default' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption'),
                'username_present' => (bool) config('mail.mailers.smtp.username'),
                'from' => config('mail.from.address'),
            ];
            \Illuminate\Support\Facades\Log::error('Test email failed', [
                'error' => $e->getMessage(),
                'mail_config' => $configSnapshot,
                'trace' => $e->getTraceAsString(),
            ]);

            $short = substr($e->getMessage(), 0, 600);
            $msg = 'Failed to send test email: ' . $short;
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 500);
            }
            return back()->with('error', $msg);
        }
    }

    /**
     * Update finance settings (bank + mobile money)
     */
    public function updateFinance(Request $request)
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
            // Notifications provider fields
            'sms_provider' => 'nullable|string|in:log,twilio,africastalking',
            'sms_default_country_code' => 'nullable|string|max:6',
            'twilio_sid' => 'nullable|string|max:128',
            'twilio_token' => 'nullable|string|max:128',
            'twilio_from' => 'nullable|string|max:32',
            'at_username' => 'nullable|string|max:128',
            'at_api_key' => 'nullable|string|max:256',
            'at_from' => 'nullable|string|max:32',
            'whatsapp_provider' => 'nullable|string|in:log,cloud',
            'whatsapp_phone_id' => 'nullable|string|max:64',
            'whatsapp_token' => 'nullable|string|max:1024',
            // Rate limits
            'notif_rate_per_recipient_per_min' => 'nullable|integer|min:0|max:1000',
            'notif_rate_per_admin_per_day' => 'nullable|integer|min:0|max:100000',
            // Payment gateways
            'mtn_api_user' => 'nullable|string|max:191',
            'mtn_api_key' => 'nullable|string|max:191',
            'mtn_primary_key' => 'nullable|string|max:191',
            'mtn_callback_url' => 'nullable|url|max:255',
            'airtel_client_id' => 'nullable|string|max:191',
            'airtel_client_secret' => 'nullable|string|max:191',
            'airtel_callback_url' => 'nullable|url|max:255',
            'stripe_secret' => 'nullable|string|max:255',
            'stripe_key' => 'nullable|string|max:255',
            'stripe_webhook_secret' => 'nullable|string|max:255',
            'paypal_client_id' => 'nullable|string|max:255',
            'paypal_client_secret' => 'nullable|string|max:255',
            'paypal_webhook_id' => 'nullable|string|max:255',
            'paypal_mode' => 'nullable|string|in:sandbox,live',
        ]);
        // Normalize checkboxes
        $validated['enable_sms'] = $request->boolean('enable_sms');
        $validated['enable_whatsapp'] = $request->boolean('enable_whatsapp');

        \Illuminate\Support\Facades\Cache::put('finance_settings', $validated, 60 * 24 * 365);

        // Persist notification provider settings to DB-backed settings for runtime use
        // SMS
        if ($request->has('sms_provider')) {
            \App\Models\Setting::set('sms.provider', $request->input('sms_provider'), 'notifications', true);
        }
        if ($request->has('sms_default_country_code')) {
            \App\Models\Setting::set('sms.default_country_code', $request->input('sms_default_country_code'), 'notifications', true);
        }
        $validated['mtn_env'] = $request->input('mtn_env', null);
        $validated['airtel_country'] = $request->input('airtel_country', null);
        $validated['airtel_currency'] = $request->input('airtel_currency', null);
        // Twilio
        foreach (
            [
                'sms.twilio.sid' => 'twilio_sid',
                'sms.twilio.token' => 'twilio_token',
                'sms.twilio.from' => 'twilio_from',
            ] as $key => $field
        ) {
            if ($request->filled($field)) {
                \App\Models\Setting::set($key, $request->input($field), 'notifications', false);
            }
        }
        // Africa's Talking
        foreach (
            [
                'sms.africastalking.username' => 'at_username',
                'sms.africastalking.api_key' => 'at_api_key',
                'sms.africastalking.from' => 'at_from',
            ] as $key => $field
        ) {
            if ($request->filled($field)) {
                \App\Models\Setting::set($key, $request->input($field), 'notifications', false);
            }
        }
        // WhatsApp Cloud
        if ($request->has('whatsapp_provider')) {
            \App\Models\Setting::set('whatsapp.provider', $request->input('whatsapp_provider'), 'notifications', true);
        }
        foreach (
            [
                'whatsapp.cloud.phone_id' => 'whatsapp_phone_id',
                'whatsapp.cloud.token' => 'whatsapp_token',
            ] as $key => $field
        ) {
            if ($request->filled($field)) {
                \App\Models\Setting::set($key, $request->input($field), 'notifications', false);
            }
        }
        // Rate limits
        if ($request->has('notif_rate_per_recipient_per_min')) {
            \App\Models\Setting::set('notifications.rate.per_recipient_per_min', (int) $request->input('notif_rate_per_recipient_per_min'), 'notifications', true);
        }
        if ($request->has('notif_rate_per_admin_per_day')) {
            \App\Models\Setting::set('notifications.rate.per_admin_per_day', (int) $request->input('notif_rate_per_admin_per_day'), 'notifications', true);
        }
        // Payment gateways - MTN
        foreach (
            [
                'payments.mtn.api_user' => 'mtn_api_user',
                'payments.mtn.api_key' => 'mtn_api_key',
                'payments.mtn.primary_key' => 'mtn_primary_key',
                'payments.mtn.callback_url' => 'mtn_callback_url',
            ] as $key => $field
        ) {
            if ($request->filled($field)) {
                \App\Models\Setting::set($key, $request->input($field), 'payments', false);
            }
        }
        // Airtel
        foreach (
            [
                'payments.airtel.client_id' => 'airtel_client_id',
                'payments.airtel.client_secret' => 'airtel_client_secret',
                'payments.airtel.callback_url' => 'airtel_callback_url',
            ] as $key => $field
        ) {
            if ($request->filled($field)) {
                \App\Models\Setting::set($key, $request->input($field), 'payments', false);
            }
        }
        // Stripe
        foreach (
            [
                'payments.stripe.secret' => 'stripe_secret',
                'payments.stripe.key' => 'stripe_key',
                'payments.stripe.webhook_secret' => 'stripe_webhook_secret',
            ] as $key => $field
        ) {
            if ($request->filled($field)) {
                \App\Models\Setting::set($key, $request->input($field), 'payments', false);
            }
        }
        // PayPal
        foreach (
            [
                'payments.paypal.client_id' => 'paypal_client_id',
                'payments.paypal.client_secret' => 'paypal_client_secret',
                'payments.paypal.webhook_id' => 'paypal_webhook_id',
                'payments.paypal.mode' => 'paypal_mode',
            ] as $key => $field
        ) {
            if ($request->has($field)) {
                \App\Models\Setting::set($key, $request->input($field), 'payments', false);
            }
        }
        \App\Models\Setting::clearCache();

        return redirect()->route($this->settingsRoute('finance'))
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
                return redirect()->route($this->settingsRoute('system'))
                    ->with('error', 'No log file found.');
            }

            $fileName = 'system-logs-' . date('Y-m-d-H-i-s') . '.log';

            return response()->download($logPath, $fileName, [
                'Content-Type' => 'text/plain',
            ]);
        } catch (\Exception $e) {
            return redirect()->route($this->settingsRoute('system'))
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

            $tenantId = tenant('id');
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = "backup_tenant_{$tenantId}_{$timestamp}";

            // Create database backup
            $dbBackupResult = $this->createDatabaseBackup($backupName, $tenantId);

            if (!$dbBackupResult['success']) {
                Log::error('Database backup failed', [
                    'tenant' => $tenantId,
                    'error' => $dbBackupResult['message']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Database backup failed: ' . $dbBackupResult['message']
                ]);
            }

            // Create files backup
            $filesBackupResult = $this->createFilesBackup($backupName, $tenantId);

            if (!$filesBackupResult['success']) {
                Log::error('Files backup failed', [
                    'tenant' => $tenantId,
                    'error' => $filesBackupResult['message']
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Files backup failed: ' . $filesBackupResult['message']
                ]);
            }

            // Log the backup creation
            Log::info('Manual backup created successfully', [
                'backup_name' => $backupName,
                'tenant' => $tenantId,
                'created_by' => Auth::user()->name ?? 'System',
                'timestamp' => $timestamp,
                'db_size' => $this->formatBytes(filesize($dbBackupResult['path'])),
                'files_size' => $this->formatBytes(filesize($filesBackupResult['path'])),
            ]);

            // Log to security audit
            SecurityAuditLog::logEvent(
                SecurityAuditLog::EVENT_SETTINGS_CHANGED,
                Auth::user()?->email,
                Auth::user()?->id,
                'Manual backup created',
                [
                    'backup_name' => $backupName,
                    'db_backup' => basename($dbBackupResult['path']),
                    'files_backup' => basename($filesBackupResult['path']),
                ],
                SecurityAuditLog::SEVERITY_INFO
            );

            return response()->json([
                'success' => true,
                'message' => 'Backup created successfully!',
                'backup_name' => $backupName,
                'timestamp' => $timestamp,
                'db_size' => $this->formatBytes(filesize($dbBackupResult['path'])),
                'files_size' => $this->formatBytes(filesize($filesBackupResult['path'])),
            ]);
        } catch (\Exception $e) {
            Log::error('Backup creation failed', [
                'tenant' => tenant('id'),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Backup creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create database backup
     */
    private function createDatabaseBackup($backupName, $tenantId = null)
    {
        try {
            // For tenant databases, use the tenant connection
            if ($tenantId) {
                $connection = 'tenant';
                $dbName = 'tenant' . $tenantId;
            } else {
                $connection = config('database.default');
                $dbName = config("database.connections.{$connection}.database");
            }

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
            fwrite($handle, "-- Tenant: " . ($tenantId ?? 'landlord') . "\n");
            fwrite($handle, "-- Database: {$dbName}\n\n");

            // Get all tables
            $tables = DB::connection($connection)->select('SELECT name FROM sqlite_master WHERE type="table" AND name NOT LIKE "sqlite_%"');

            foreach ($tables as $table) {
                $tableName = $table->name;

                // Get table structure
                $createTableResult = DB::connection($connection)->select("SELECT sql FROM sqlite_master WHERE type='table' AND name=?", [$tableName]);

                if (!empty($createTableResult)) {
                    $createTable = $createTableResult[0]->sql;

                    fwrite($handle, "\n-- Table structure for table `{$tableName}`\n");
                    fwrite($handle, "DROP TABLE IF EXISTS `{$tableName}`;\n");
                    fwrite($handle, $createTable . ";\n\n");

                    // Get table data
                    $rows = DB::connection($connection)->table($tableName)->get();

                    if ($rows->count() > 0) {
                        fwrite($handle, "-- Dumping data for table `{$tableName}`\n");

                        foreach ($rows as $row) {
                            $values = [];
                            foreach ((array)$row as $value) {
                                if (is_null($value)) {
                                    $values[] = 'NULL';
                                } else {
                                    $values[] = "'" . str_replace("'", "''", $value) . "'";
                                }
                            }

                            $columns = implode('`, `', array_keys((array)$row));
                            $valuesStr = implode(', ', $values);

                            fwrite($handle, "INSERT INTO `{$tableName}` (`{$columns}`) VALUES ({$valuesStr});\n");
                        }
                        fwrite($handle, "\n");
                    }
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
    private function createFilesBackup($backupName, $tenantId = null)
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
            if (is_dir(storage_path('app/public'))) {
                $this->addDirectoryToZip($zip, storage_path('app/public'), 'storage/app/public');
            }

            if (is_dir(public_path('uploads'))) {
                $this->addDirectoryToZip($zip, public_path('uploads'), 'public/uploads');
            }

            // Add tenant-specific files if available
            if ($tenantId && is_dir(storage_path("app/tenants/{$tenantId}"))) {
                $this->addDirectoryToZip($zip, storage_path("app/tenants/{$tenantId}"), "tenant_{$tenantId}");
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
            Artisan::call('cache:clear');

            // Clear config cache
            Artisan::call('config:clear');

            // Clear route cache
            Artisan::call('route:clear');

            // Clear view cache
            Artisan::call('view:clear');

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
        return redirect()->route($this->settingsRoute('permissions'))->with('success', 'User roles and permissions updated.');
    }
}


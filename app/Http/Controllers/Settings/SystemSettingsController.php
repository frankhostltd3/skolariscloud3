<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class SystemSettingsController extends Controller
{
    public function edit()
    {
        $settings = [
            // System Information
            'account_status' => setting('account_status', 'verified'),
            'enable_two_factor_auth' => (bool) setting('enable_two_factor_auth', false),

            // System Performance
            'cache_driver' => setting('cache_driver', 'file'),
            'session_driver' => setting('session_driver', 'file'),
            'session_lifetime' => setting('session_lifetime', '120'),
            'max_file_upload' => setting('max_file_upload', '10'),
            'pagination_limit' => setting('pagination_limit', '15'),

            // Security Settings
            'password_min_length' => setting('password_min_length', '8'),
            'max_login_attempts' => setting('max_login_attempts', '5'),
            'lockout_duration' => setting('lockout_duration', '15'),
            'force_https' => (bool) setting('force_https', false),
            'enable_two_factor' => (bool) setting('enable_two_factor', false),

            // Backup & Maintenance
            'auto_backup' => setting('auto_backup', 'disabled'),
            'backup_retention' => setting('backup_retention', '30'),
            'log_level' => setting('log_level', 'error'),
        ];

        return view('settings.system', compact('settings'));
    }

    public function update(Request $request)
    {
        $formType = $request->input('form_type');

        switch ($formType) {
            case 'system_info':
                return $this->updateSystemInfo($request);
            case 'performance':
                return $this->updatePerformance($request);
            case 'security':
                return $this->updateSecurity($request);
            case 'maintenance':
                return $this->updateMaintenance($request);
            default:
                return redirect()->back()->with('error', 'Invalid form submission.');
        }
    }

    private function updateSystemInfo(Request $request)
    {
        $validated = $request->validate([
            'account_status' => 'nullable|boolean',
            'enable_two_factor_auth' => 'nullable|boolean',
        ]);

        // Handle account status
        $accountStatus = $request->has('account_status') && $request->account_status ? 'verified' : 'unverified';
        setting(['account_status' => $accountStatus]);

        // Handle two-factor auth
        $twoFactorAuth = $request->has('enable_two_factor_auth') && $request->enable_two_factor_auth;
        setting(['enable_two_factor_auth' => $twoFactorAuth]);

        Cache::forget('settings');

        return redirect()->route('settings.system.edit')
            ->with('status', 'System information updated successfully.');
    }

    private function updatePerformance(Request $request)
    {
        $validated = $request->validate([
            'cache_driver' => 'required|in:file,redis,memcached,database',
            'session_driver' => 'required|in:file,database,redis,cookie',
            'session_lifetime' => 'required|numeric|min:1|max:1440',
            'max_file_upload' => 'required|numeric|min:1|max:256',
            'pagination_limit' => 'required|in:10,15,25,50,100',
        ]);

        foreach ($validated as $key => $value) {
            setting([$key => $value]);
        }

        Cache::forget('settings');

        return redirect()->route('settings.system.edit')
            ->with('status', 'Performance settings updated successfully.');
    }

    private function updateSecurity(Request $request)
    {
        $validated = $request->validate([
            'password_min_length' => 'required|numeric|min:6|max:20',
            'max_login_attempts' => 'required|numeric|min:1|max:20',
            'lockout_duration' => 'required|in:1,5,10,15,30,45,60,forever',
            'force_https' => 'nullable|boolean',
            'enable_two_factor' => 'nullable|boolean',
        ]);

        // Handle boolean values
        $validated['force_https'] = $request->has('force_https') && $request->force_https;
        $validated['enable_two_factor'] = $request->has('enable_two_factor') && $request->enable_two_factor;

        foreach ($validated as $key => $value) {
            setting([$key => $value]);
        }

        Cache::forget('settings');

        return redirect()->route('settings.system.edit')
            ->with('status', 'Security settings updated successfully.');
    }

    private function updateMaintenance(Request $request)
    {
        $validated = $request->validate([
            'auto_backup' => 'required|in:disabled,daily,weekly,monthly',
            'backup_retention' => 'required|numeric|min:1|max:365',
            'log_level' => 'required|in:emergency,alert,critical,error,warning,notice,info,debug',
        ]);

        foreach ($validated as $key => $value) {
            setting([$key => $value]);
        }

        Cache::forget('settings');

        return redirect()->route('settings.system.edit')
            ->with('status', 'Maintenance settings updated successfully.');
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Cache::forget('settings');

            return response()->json([
                'success' => true,
                'message' => 'Cache cleared successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage(),
            ], 500);
        }
    }
}

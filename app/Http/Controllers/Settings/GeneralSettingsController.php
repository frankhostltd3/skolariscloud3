<?php

namespace App\Http\Controllers\Settings;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class GeneralSettingsController extends Controller
{
    public function edit(Request $request): View
    {
        $this->authorizeAdmin($request);

        // Get settings from database or use defaults
        $settings = [
            'school_name' => setting('school_name', 'School Management System'),
            'website_title' => setting('website_title', ''),
            'school_code' => setting('school_code', 'SCH001'),
            'school_email' => setting('school_email', 'info@school.com'),
            'school_phone' => setting('school_phone', '+1-234-567-8900'),
            'school_address' => setting('school_address', '123 Education Street, Learning City, State 12345'),
            'school_website' => setting('school_website', 'https://www.school.com'),
            'school_logo' => setting('school_logo'),
            'principal_name' => setting('principal_name', 'Dr. Jane Smith'),
            'school_type' => setting('school_type', 'private'),
            'school_category' => setting('school_category', 'day'),
            'gender_type' => setting('gender_type', 'mixed'),
            'app_name' => setting('app_name', config('app.name')),
            'timezone' => setting('timezone', 'UTC'),
            'date_format' => setting('date_format', 'Y-m-d'),
            'time_format' => setting('time_format', 'H:i'),
            'default_language' => setting('default_language', 'en'),
            'records_per_page' => setting('records_per_page', '15'),
        ];

        return view('settings.general', [
            'settings' => $settings,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $formType = $request->input('form_type', 'school_info');

        if ($formType === 'school_info') {
            return $this->updateSchoolInfo($request);
        }

        if ($formType === 'application') {
            return $this->updateApplicationSettings($request);
        }

        return redirect()->route('settings.general.edit')
            ->with('error', 'Invalid form type.');
    }

    public function clearCache(Request $request)
    {
        $this->authorizeAdmin($request);

        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            Artisan::call('route:clear');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Application cache cleared successfully!',
                ]);
            }

            return redirect()->route('settings.general.edit')
                ->with('status', 'Application cache cleared successfully!');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to clear cache: ' . $e->getMessage(),
                ], 500);
            }

            return redirect()->route('settings.general.edit')
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    private function updateSchoolInfo(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'website_title' => ['nullable', 'string', 'max:255'],
            'school_code' => ['required', 'string', 'max:50'],
            'school_email' => ['required', 'email', 'max:255'],
            'school_phone' => ['required', 'string', 'max:50'],
            'school_address' => ['required', 'string', 'max:500'],
            'school_website' => ['nullable', 'url', 'max:255'],
            'school_logo' => ['nullable', 'image', 'max:2048'],
            'principal_name' => ['required', 'string', 'max:255'],
            'school_type' => ['required', 'in:government,private,hybrid'],
            'school_category' => ['required', 'in:day,boarding,hybrid'],
            'gender_type' => ['required', 'in:boys,girls,mixed'],
        ]);

        // Handle logo upload
        if ($request->hasFile('school_logo')) {
            $logo = $request->file('school_logo');
            $path = $logo->store('logos', 'public');
            $validated['school_logo'] = $path;

            // Delete old logo if exists
            $oldLogo = setting('school_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
        }

        // Save each setting
        foreach ($validated as $key => $value) {
            setting([$key => $value]);
        }

        return redirect()->route('settings.general.edit')
            ->with('status', 'School information updated successfully.');
    }

    private function updateApplicationSettings(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'app_name' => ['required', 'string', 'max:255'],
            'timezone' => ['required', 'string', 'max:100'],
            'date_format' => ['required', 'string', 'max:50'],
            'time_format' => ['required', 'string', 'max:50'],
            'default_language' => ['required', 'string', 'max:10'],
            'records_per_page' => ['required', 'integer', 'min:5', 'max:200'],
        ]);

        // Save each setting
        foreach ($validated as $key => $value) {
            setting([$key => $value]);
        }

        return redirect()->route('settings.general.edit')
            ->with('status', 'Application settings updated successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        $user = $request->user();

        abort_if(!$user, 403);
        abort_if(!$user->hasUserType(UserType::ADMIN), 403);
    }
}

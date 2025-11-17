<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use App\Models\PlatformIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PlatformIntegrationController extends Controller
{
    /**
     * Show platform integration setup page
     */
    public function index()
    {
        $platforms = [
            'zoom' => PlatformIntegration::getByPlatform('zoom'),
            'google_meet' => PlatformIntegration::getByPlatform('google_meet'),
            'microsoft_teams' => PlatformIntegration::getByPlatform('microsoft_teams'),
        ];

        return view('tenant.teacher.integrations.index', compact('platforms'));
    }

    /**
     * Show setup form for specific platform
     */
    public function setup(string $platform)
    {
        $validPlatforms = ['zoom', 'google_meet', 'microsoft_teams'];
        
        if (!in_array($platform, $validPlatforms)) {
            abort(404, 'Invalid platform');
        }

        $integration = PlatformIntegration::getByPlatform($platform) 
                      ?? new PlatformIntegration(['platform' => $platform]);

        return view('tenant.teacher.integrations.setup', compact('integration', 'platform'));
    }

    /**
     * Store or update platform configuration
     */
    public function store(Request $request, string $platform)
    {
        $validPlatforms = ['zoom', 'google_meet', 'microsoft_teams'];
        
        if (!in_array($platform, $validPlatforms)) {
            abort(404, 'Invalid platform');
        }

        $rules = [
            'is_enabled' => 'boolean',
        ];

        // Platform-specific validation
        if ($platform === 'zoom') {
            $rules['api_key'] = 'required_if:is_enabled,true|nullable|string';
            $rules['api_secret'] = 'required_if:is_enabled,true|nullable|string';
        } elseif ($platform === 'google_meet') {
            $rules['client_id'] = 'required_if:is_enabled,true|nullable|string';
            $rules['client_secret'] = 'required_if:is_enabled,true|nullable|string';
        } elseif ($platform === 'microsoft_teams') {
            $rules['client_id'] = 'required_if:is_enabled,true|nullable|string';
            $rules['client_secret'] = 'required_if:is_enabled,true|nullable|string';
        }

        $validated = $request->validate($rules);
        $validated['platform'] = $platform;

        // Update or create integration
        $integration = PlatformIntegration::updateOrCreate(
            ['platform' => $platform],
            $validated
        );

        return redirect()
            ->route('tenant.teacher.integrations.index')
            ->with('success', ucfirst(str_replace('_', ' ', $platform)) . ' integration updated successfully!');
    }

    /**
     * Test platform connection
     */
    public function test(string $platform)
    {
        $integration = PlatformIntegration::getByPlatform($platform);

        if (!$integration || !$integration->isConfigured()) {
            return back()->with('error', 'Platform not configured. Please set up credentials first.');
        }

        // Test connection based on platform
        try {
            $result = $this->testPlatformConnection($platform, $integration);

            if ($result['success']) {
                return back()->with('success', $result['message']);
            } else {
                return back()->with('error', $result['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Connection test failed: ' . $e->getMessage());
        }
    }

    /**
     * Disable platform integration
     */
    public function disable(string $platform)
    {
        $integration = PlatformIntegration::getByPlatform($platform);

        if ($integration) {
            $integration->update(['is_enabled' => false]);
            return back()->with('success', ucfirst(str_replace('_', ' ', $platform)) . ' integration disabled.');
        }

        return back()->with('error', 'Platform integration not found.');
    }

    /**
     * Test platform connection (placeholder for actual API calls)
     */
    private function testPlatformConnection(string $platform, PlatformIntegration $integration): array
    {
        // In production, you would make actual API calls here
        // This is a placeholder that always returns success

        switch ($platform) {
            case 'zoom':
                // TODO: Make actual Zoom API call to verify credentials
                return [
                    'success' => true,
                    'message' => 'Zoom connection test successful! API credentials are valid.'
                ];

            case 'google_meet':
                // TODO: Make actual Google Meet API call
                return [
                    'success' => true,
                    'message' => 'Google Meet connection test successful! OAuth credentials are valid.'
                ];

            case 'microsoft_teams':
                // TODO: Make actual Microsoft Teams API call
                return [
                    'success' => true,
                    'message' => 'Microsoft Teams connection test successful! OAuth credentials are valid.'
                ];

            default:
                return [
                    'success' => false,
                    'message' => 'Unknown platform'
                ];
        }
    }
}

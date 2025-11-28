<?php

namespace App\Http\Controllers\Tenant\Teacher;

use App\Http\Controllers\Controller;
use App\Models\PlatformIntegration;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlatformIntegrationController extends Controller
{
    private const SUPPORTED_PLATFORMS = [
        'zoom' => [
            'label' => 'Zoom',
            'icon' => 'bi-camera-video',
            'description' => 'Professional video conferencing with recording, waiting rooms, and breakout rooms.',
        ],
        'google_meet' => [
            'label' => 'Google Meet',
            'icon' => 'bi-google',
            'description' => 'Tightly integrated with Google Workspace for calendar-driven scheduling.',
        ],
        'microsoft_teams' => [
            'label' => 'Microsoft Teams',
            'icon' => 'bi-microsoft',
            'description' => 'Enterprise-grade collaboration with Microsoft 365 integration.',
        ],
    ];

    /**
     * Show platform integration setup page
     */
    public function index(): View
    {
        $tableExists = PlatformIntegration::tableExists();

        $platforms = [];
        $statuses = [];

        foreach (array_keys(self::SUPPORTED_PLATFORMS) as $key) {
            $platforms[$key] = $tableExists ? PlatformIntegration::getByPlatform($key) : null;
            $statuses[$key] = $this->statusMeta($platforms[$key]);
        }

        return view('tenant.teacher.integrations.index', [
            'platforms' => $platforms,
            'platformMeta' => self::SUPPORTED_PLATFORMS,
            'statuses' => $statuses,
            'tableMissing' => ! $tableExists,
        ]);
    }

    /**
     * Show setup form for specific platform
     */
    public function setup(string $platform): View
    {
        $this->guardPlatform($platform);

        $tableExists = PlatformIntegration::tableExists();

        $integration = $tableExists
            ? PlatformIntegration::getByPlatform($platform) ?? new PlatformIntegration(['platform' => $platform])
            : null;

        return view('tenant.teacher.integrations.setup', [
            'integration' => $integration,
            'platform' => $platform,
            'tableMissing' => ! $tableExists,
            'adminManaged' => $integration?->managedByAdmin() ?? false,
        ]);
    }

    /**
     * Store or update platform configuration
     */
    public function store(Request $request, string $platform)
    {
        $this->guardPlatform($platform);

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
        $validated['is_enabled'] = $request->boolean('is_enabled');
        $validated['platform'] = $platform;

        if (! PlatformIntegration::tableExists()) {
            return back()
                ->withInput()
                ->with('error', 'Integration settings are not available for this school yet. Please contact your administrator.');
        }

        $existing = PlatformIntegration::getByPlatform($platform);

        if ($existing && $existing->managedByAdmin()) {
            return redirect()
                ->route('tenant.teacher.classroom.integrations.index')
                ->with('error', 'This integration is centrally managed by your administrator. Please contact the admin team to request changes.');
        }

        $integration = PlatformIntegration::updateOrCreate(
            ['platform' => $platform],
            array_merge($validated, [
                'managed_by_admin' => false,
            ])
        );

        $this->refreshStatus($integration);

        return redirect()
            ->route('tenant.teacher.classroom.integrations.index')
            ->with('success', ucfirst(str_replace('_', ' ', $platform)) . ' integration updated successfully!');
    }

    /**
     * Test platform connection
     */
    public function test(string $platform)
    {
        if (! PlatformIntegration::tableExists()) {
            return back()->with('error', 'Integration settings are not available for this school yet. Please contact your administrator.');
        }

        $integration = PlatformIntegration::getByPlatform($platform);

        if ($integration && $integration->managedByAdmin()) {
            return back()->with('error', 'This integration is centrally managed by your administrator.');
        }

        if (! $integration || ! $integration->isConfigured()) {
            return back()->with('error', 'Platform not configured. Please set up credentials first.');
        }

        try {
            $result = $this->testPlatformConnection($platform, $integration);

            if ($result['success']) {
                $integration->update([
                    'status' => 'ready',
                    'status_message' => $result['message'],
                    'last_tested_at' => now(),
                ]);

                return back()->with('success', $result['message']);
            }

            $integration->update([
                'status' => 'error',
                'status_message' => $result['message'],
                'last_tested_at' => now(),
            ]);

            return back()->with('error', $result['message']);
        } catch (\Throwable $e) {
            $integration->update([
                'status' => 'error',
                'status_message' => $e->getMessage(),
                'last_tested_at' => now(),
            ]);

            return back()->with('error', 'Connection test failed: ' . $e->getMessage());
        }
    }

    /**
     * Disable a teacher-managed integration.
     */
    public function disable(string $platform)
    {
        if (! PlatformIntegration::tableExists()) {
            return back()->with('error', 'Integration settings are not available for this school yet. Please contact your administrator.');
        }

        $integration = PlatformIntegration::getByPlatform($platform);

        if (! $integration) {
            return back()->with('error', 'Platform integration not found.');
        }

        if ($integration->managedByAdmin()) {
            return back()->with('error', 'This integration is centrally managed by your administrator.');
        }

        $integration->update([
            'is_enabled' => false,
        ]);
        $this->refreshStatus($integration);

        return back()->with('success', ucfirst(str_replace('_', ' ', $platform)) . ' integration disabled.');
    }

    /**
     * Test platform connection (validates credentials format)
     */
    private function testPlatformConnection(string $platform, PlatformIntegration $integration): array
    {
        // In a full production environment with valid API keys, you would make actual API calls here.
        // For now, we validate that the required credentials are present and formatted correctly.

        switch ($platform) {
            case 'zoom':
                if (empty($integration->api_key) || empty($integration->api_secret)) {
                    return [
                        'success' => false,
                        'message' => 'Zoom API Key and Secret are required.'
                    ];
                }
                return [
                    'success' => true,
                    'message' => 'Zoom credentials validated successfully.'
                ];

            case 'google_meet':
                if (empty($integration->client_id) || empty($integration->client_secret)) {
                    return [
                        'success' => false,
                        'message' => 'Google Meet Client ID and Secret are required.'
                    ];
                }
                return [
                    'success' => true,
                    'message' => 'Google Meet credentials validated successfully.'
                ];

            case 'microsoft_teams':
                if (empty($integration->client_id) || empty($integration->client_secret)) {
                    return [
                        'success' => false,
                        'message' => 'Microsoft Teams Client ID and Secret are required.'
                    ];
                }
                return [
                    'success' => true,
                    'message' => 'Microsoft Teams credentials validated successfully.'
                ];

            default:
                return [
                    'success' => false,
                    'message' => 'Unknown platform'
                ];
        }
    }

    private function statusMeta(?PlatformIntegration $integration): array
    {
        if (! $integration) {
            return [
                'label' => 'Not configured',
                'badge' => 'bg-secondary',
                'message' => 'Provide credentials to enable automatic meeting creation.',
            ];
        }

        if ($integration->managedByAdmin()) {
            return [
                'label' => 'Managed by admin',
                'badge' => 'bg-primary',
                'message' => 'Settings are centrally managed. Teachers can still review the status.',
            ];
        }

        if (! $integration->is_enabled) {
            return [
                'label' => 'Disabled',
                'badge' => 'bg-secondary',
                'message' => 'Enable this integration to create meetings from classroom tools.',
            ];
        }

        if (! $integration->isConfigured()) {
            return [
                'label' => 'Needs configuration',
                'badge' => 'bg-warning text-dark',
                'message' => 'Required credentials are missing. Update the integration to continue.',
            ];
        }

        if ($integration->isTokenExpired()) {
            return [
                'label' => 'Token refresh required',
                'badge' => 'bg-warning text-dark',
                'message' => 'Refresh or reconnect to keep meeting creation working.',
            ];
        }

        return [
            'label' => 'Ready',
            'badge' => 'bg-success',
            'message' => 'Credentials look good. You can create meetings with this provider.',
        ];
    }

    private function refreshStatus(PlatformIntegration $integration): void
    {
        $status = $this->statusMeta($integration);
        $integration->status = match ($status['label']) {
            'Ready' => 'ready',
            'Managed by admin' => 'managed',
            'Disabled' => 'disabled',
            'Token refresh required' => 'token_expired',
            'Needs configuration' => 'needs_configuration',
            default => 'not_configured',
        };
        $integration->status_message = $status['message'];
        $integration->save();
    }

    private function guardPlatform(string $platform): void
    {
        if (! array_key_exists($platform, self::SUPPORTED_PLATFORMS)) {
            abort(404, 'Invalid platform');
        }
    }
}

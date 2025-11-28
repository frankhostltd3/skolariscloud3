<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PlatformIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class IntegrationSettingsController extends Controller
{
    private const SUPPORTED_PLATFORMS = [
        'zoom' => [
            'label' => 'Zoom',
            'icon' => 'bi-camera-video',
            'description' => 'Professional video conferencing with HD video, recording, and breakout rooms.',
            'docs_url' => 'https://developers.zoom.us/docs/api/',
        ],
        'google_meet' => [
            'label' => 'Google Meet',
            'icon' => 'bi-google',
            'description' => 'Integrated with Google Workspace for calendar-driven meetings and classroom scheduling.',
            'docs_url' => 'https://developers.google.com/calendar',
        ],
        'microsoft_teams' => [
            'label' => 'Microsoft Teams',
            'icon' => 'bi-microsoft',
            'description' => 'Enterprise collaboration platform with chat, scheduling, and Microsoft 365 integration.',
            'docs_url' => 'https://learn.microsoft.com/graph/teams-concept-overview',
        ],
    ];

    public function index()
    {
        $tableExists = PlatformIntegration::tableExists();
        $records = collect();

        if ($tableExists) {
            $records = PlatformIntegration::query()
                ->whereIn('platform', array_keys(self::SUPPORTED_PLATFORMS))
                ->get()
                ->keyBy('platform');
        }

        $statuses = [];
        foreach (self::SUPPORTED_PLATFORMS as $key => $meta) {
            $integration = $records->get($key);
            $statuses[$key] = $this->resolveStatusMeta($integration);
        }

        return view('tenant.admin.settings.integrations', [
            'tableExists' => $tableExists,
            'platforms' => self::SUPPORTED_PLATFORMS,
            'integrations' => $records,
            'statuses' => $statuses,
        ]);
    }

    public function update(Request $request)
    {
        $tableExists = PlatformIntegration::tableExists();

        if (! $tableExists) {
            return redirect()->back()->with('error', 'Integration settings table is missing for this tenant. Run tenant migrations first.');
        }

        $payload = $request->input('platforms', []);
        $updated = collect();

        foreach (self::SUPPORTED_PLATFORMS as $key => $config) {
            $input = Arr::get($payload, $key, []);
            $isEnabled = filter_var(Arr::get($input, 'is_enabled', false), FILTER_VALIDATE_BOOLEAN);

            $validator = Validator::make(
                array_merge($input, ['is_enabled' => $isEnabled]),
                $this->rulesFor($key, $isEnabled, $input),
                [],
                $this->attributesFor($key)
            );

            $validator->validate();

            $integration = PlatformIntegration::firstOrNew(['platform' => $key]);
            $integration->is_enabled = $isEnabled;
            $integration->managed_by_admin = true;

            $this->synchroniseCredentials($integration, $key, $input, $isEnabled);

            if (! empty($input['redirect_uri'])) {
                $integration->redirect_uri = $input['redirect_uri'];
            } elseif ($integration->exists && empty($input['redirect_uri'])) {
                // Leave existing redirect URI untouched if a new value is not supplied.
            } else {
                $integration->redirect_uri = null;
            }

            $statusMeta = $this->resolveStatusMeta($integration, recheckConfiguration: true);
            $integration->status = $statusMeta['code'];
            $integration->status_message = $statusMeta['message'];

            $integration->save();
            $updated->push($integration->platform);
        }

        return redirect()
            ->route('tenant.settings.admin.integrations')
            ->with('success', 'Integration settings updated for: ' . $updated->implode(', '));
    }

    private function rulesFor(string $platform, bool $enabled, array $input): array
    {
        $hasExistingApiKey = (bool) Arr::get($input, 'has_existing_api_key', false);
        $hasExistingApiSecret = (bool) Arr::get($input, 'has_existing_api_secret', false);
        $hasExistingClientId = (bool) Arr::get($input, 'has_existing_client_id', false);
        $hasExistingClientSecret = (bool) Arr::get($input, 'has_existing_client_secret', false);

        $rules = [
            'is_enabled' => ['boolean'],
            'redirect_uri' => ['nullable', 'url', 'max:2048'],
        ];

        if ($platform === 'zoom') {
            $rules['api_key'] = $enabled && ! $hasExistingApiKey ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'];
            $rules['api_secret'] = $enabled && ! $hasExistingApiSecret ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'];
        }

        if ($platform === 'google_meet' || $platform === 'microsoft_teams') {
            $clientIdRule = $enabled && ! $hasExistingClientId ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'];
            $clientSecretRule = $enabled && ! $hasExistingClientSecret ? ['required', 'string', 'max:255'] : ['nullable', 'string', 'max:255'];
            $rules['client_id'] = $clientIdRule;
            $rules['client_secret'] = $clientSecretRule;
        }

        return $rules;
    }

    private function attributesFor(string $platform): array
    {
        return match ($platform) {
            'zoom' => [
                'api_key' => 'Zoom API Key / Account ID',
                'api_secret' => 'Zoom API Secret / Client Secret',
            ],
            'google_meet' => [
                'client_id' => 'Google OAuth Client ID',
                'client_secret' => 'Google OAuth Client Secret',
                'redirect_uri' => 'Authorized redirect URI',
            ],
            'microsoft_teams' => [
                'client_id' => 'Azure Application (client) ID',
                'client_secret' => 'Azure Client Secret',
                'redirect_uri' => 'Redirect URI',
            ],
            default => [],
        };
    }

    private function synchroniseCredentials(PlatformIntegration $integration, string $platform, array $input, bool $enabled): void
    {
        if (! $enabled) {
            // Leave credentials intact so admins can re-enable quickly later.
            return;
        }

        if ($platform === 'zoom') {
            $this->applyCredential($integration, 'api_key', $input, 'has_existing_api_key');
            $this->applyCredential($integration, 'api_secret', $input, 'has_existing_api_secret');
        }

        if ($platform === 'google_meet' || $platform === 'microsoft_teams') {
            $this->applyCredential($integration, 'client_id', $input, 'has_existing_client_id');
            $this->applyCredential($integration, 'client_secret', $input, 'has_existing_client_secret');
        }
    }

    private function applyCredential(PlatformIntegration $integration, string $attribute, array $input, string $existingFlag): void
    {
        $hasExisting = (bool) Arr::get($input, $existingFlag, false);
        $value = Arr::get($input, $attribute);

        if ($value !== null && $value !== '') {
            $integration->{$attribute} = $value;
            return;
        }

        if (! $hasExisting) {
            $integration->{$attribute} = null;
        }
    }

    private function resolveStatusMeta(?PlatformIntegration $integration, bool $recheckConfiguration = false): array
    {
        if (! $integration || (! $integration->exists && ! $recheckConfiguration)) {
            return [
                'code' => 'not_configured',
                'label' => 'Not configured',
                'message' => 'No credentials have been provided yet.',
                'badge_class' => 'bg-secondary',
            ];
        }

        if (! $integration->is_enabled) {
            return [
                'code' => 'disabled',
                'label' => 'Disabled',
                'message' => 'Integration is disabled. Enable it to allow automatic meeting creation.',
                'badge_class' => 'bg-secondary',
            ];
        }

        if (! $integration->isConfigured()) {
            return [
                'code' => 'needs_configuration',
                'label' => 'Credentials missing',
                'message' => 'Add the required credentials to activate this integration.',
                'badge_class' => 'bg-warning text-dark',
            ];
        }

        $hasToken = ! empty($integration->access_token) || ! empty($integration->refresh_token);

        if ($hasToken && $integration->isTokenExpired()) {
            return [
                'code' => 'token_expired',
                'label' => 'Token refresh required',
                'message' => 'Update or refresh tokens to ensure meetings can be created.',
                'badge_class' => 'bg-warning text-dark',
            ];
        }

        return [
            'code' => 'ready',
            'label' => 'Ready for use',
            'message' => 'Credentials look good. Teachers can create meetings using this platform.',
            'badge_class' => 'bg-success',
        ];
    }
}

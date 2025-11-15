<?php

namespace App\Http\Controllers\Settings;

use App\Enums\UserType;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePaymentSettingsRequest;
use App\Models\PaymentGatewaySetting;
use App\Services\EnvWriter;
use App\Services\PaymentGatewayConfigurator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class PaymentSettingsController extends Controller
{
    public function __construct(
        private PaymentGatewayConfigurator $configurator,
        private EnvWriter $envWriter
    ) {
    }

    public function edit(Request $request): View
    {
        $this->authorizeAdmin($request);

        $definitions = collect(config('payment_gateways.gateways', []));
        $settings = PaymentGatewaySetting::query()->get()->keyBy('gateway');

        // Only allow .env sync for super-admin/landlord (no currentSchool, must be ADMIN, and must have a special flag if needed)
        $envWritable = ! $request->attributes->get('currentSchool')
            && $this->envWriter->isWritable()
            && $request->user() && $request->user()->hasUserType(\App\Enums\UserType::ADMIN)
            && $request->user()->is_landlord ?? true;

        return view('settings.payments', [
            'definitions' => $definitions,
            'settings' => $settings,
            'envWritable' => $envWritable,
        ]);
    }

    public function update(UpdatePaymentSettingsRequest $request): RedirectResponse
    {
        $this->authorizeAdmin($request);

        $payload = $request->validated();
        $definitions = collect(config('payment_gateways.gateways', []));
        $gatewaysInput = Arr::get($payload, 'gateways', []);
        $syncEnv = Arr::get($payload, 'sync_env', []);
        $envUpdates = [];
        $isTenantContext = (bool) $request->attributes->get('currentSchool');

        $definitions->each(function (array $definition, string $gateway) use (&$envUpdates, $gatewaysInput, $syncEnv, $isTenantContext) {
            /** @var \App\Models\PaymentGatewaySetting $setting */
            $setting = PaymentGatewaySetting::query()->firstOrNew(['gateway' => $gateway]);

            $incoming = Arr::get($gatewaysInput, $gateway, []);
            $isEnabled = filter_var(Arr::get($incoming, 'is_enabled', false), FILTER_VALIDATE_BOOLEAN);
            $incomingConfig = Arr::get($incoming, 'config', []);
            $fields = $definition['fields'] ?? [];

            $mergedConfig = $this->mergeConfig($incomingConfig, $setting->config ?? [], $fields);

            $setting->fill([
                'is_enabled' => $isEnabled,
                'config' => $mergedConfig,
            ])->save();

            if (! $isTenantContext && Arr::get($syncEnv, $gateway)) {
                $envUpdates = array_merge($envUpdates, $this->buildEnvPayload($fields, $mergedConfig));
            }
        });

        if (! empty($envUpdates)) {
            \Log::info('Payment .env sync by user', [
                'user_id' => $request->user()?->id,
                'user_email' => $request->user()?->email,
                'env_keys' => array_keys($envUpdates),
                'ip' => $request->ip(),
            ]);
            $this->envWriter->put($envUpdates);
        }

        $this->configurator->apply();

        return redirect()->route('settings.payments.edit')->with('status', 'Payment gateways updated successfully.');
    }

    private function authorizeAdmin(Request $request): void
    {
        $user = $request->user();

        abort_if(! $user, 403);
        abort_if(! $user->hasUserType(UserType::ADMIN), 403);
    }

    private function mergeConfig(array $incoming, array $existing, array $fields): array
    {
        $merged = $existing;

        foreach ($fields as $key => $definition) {
            $conceal = (bool) ($definition['conceal'] ?? false);

            if (! array_key_exists($key, $incoming)) {
                if (! array_key_exists($key, $merged) && array_key_exists('default', $definition)) {
                    $merged[$key] = $definition['default'];
                }

                continue;
            }

            $value = $incoming[$key];

            if ($value === null) {
                unset($merged[$key]);
                continue;
            }

            if ($value === '') {
                if ($conceal && array_key_exists($key, $merged)) {
                    continue;
                }

                unset($merged[$key]);
                continue;
            }

            $merged[$key] = $value;
        }

        foreach ($fields as $key => $definition) {
            if (! array_key_exists($key, $merged) && array_key_exists('default', $definition)) {
                $merged[$key] = $definition['default'];
            }
        }

        return $merged;
    }

    private function buildEnvPayload(array $fields, array $config): array
    {
        $env = [];

        foreach ($fields as $key => $definition) {
            if (! array_key_exists('env', $definition)) {
                continue;
            }

            $envKey = $definition['env'];
            $value = $config[$key] ?? null;

            $env[$envKey] = $value;
        }

        return $env;
    }
}

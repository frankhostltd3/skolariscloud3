<?php

namespace Tests\Feature;

use App\Enums\UserType;
use App\Models\PaymentGatewaySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_payment_gateway_settings(): void
    {
        $admin = User::factory()->make([
            'id' => 1,
            'user_type' => UserType::ADMIN->value,
        ]);

        $payload = [
            'gateways' => [
                'paypal' => [
                    'is_enabled' => 1,
                    'config' => [
                        'mode' => 'live',
                        'client_id' => 'paypal-client',
                        'client_secret' => 'paypal-secret',
                        'webhook_id' => 'wh_123',
                    ],
                ],
                'stripe' => [
                    'is_enabled' => 0,
                    'config' => [
                        'mode' => 'test',
                        'publishable_key' => 'pk_test_123',
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($admin)->put(route('settings.payments.update'), $payload);

        $response->assertRedirect(route('settings.payments.edit'));
        $response->assertSessionHas('status');

        $paypal = PaymentGatewaySetting::query()->where('gateway', 'paypal')->first();
        $this->assertNotNull($paypal);
        $this->assertTrue($paypal->is_enabled);
        $this->assertSame('live', $paypal->config['mode']);
        $this->assertSame('paypal-client', $paypal->config['client_id']);

        $stripe = PaymentGatewaySetting::query()->where('gateway', 'stripe')->first();
        $this->assertNotNull($stripe);
        $this->assertFalse($stripe->is_enabled);
        $this->assertSame('test', $stripe->config['mode']);
    }
}

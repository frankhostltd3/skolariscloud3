<?php

namespace Tests\Feature;

use App\Enums\UserType;
use App\Models\MessagingChannelSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MessagingSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_update_messaging_settings(): void
    {
        $admin = User::factory()->make([
            'id' => 1,
            'user_type' => UserType::ADMIN->value,
        ]);

        $payload = [
            'channels' => [
                'sms' => [
                    'providers' => [
                        'twilio' => [
                            'is_enabled' => 1,
                            'config' => [
                                'account_sid' => 'AC123',
                                'auth_token' => 'secret-token',
                                'from_number' => '+15005550006',
                            ],
                        ],
                        'africastalking' => [],
                    ],
                ],
                'whatsapp' => [
                    'providers' => [
                        'meta_cloud' => [
                            'is_enabled' => 0,
                            'config' => [
                                'phone_number_id' => '12345',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($admin)->put(route('settings.messaging.update'), $payload);

        $response->assertRedirect(route('settings.messaging.edit'));
        $response->assertSessionHas('status');

        $twilio = MessagingChannelSetting::query()->where('channel', 'sms')->where('provider', 'twilio')->first();
        $this->assertNotNull($twilio);
        $this->assertTrue($twilio->is_enabled);
        $this->assertSame('AC123', $twilio->config['account_sid']);
        $this->assertSame('+15005550006', $twilio->config['from_number']);

        $meta = MessagingChannelSetting::query()->where('channel', 'whatsapp')->where('provider', 'meta_cloud')->first();
        $this->assertNotNull($meta);
        $this->assertFalse($meta->is_enabled);
        $this->assertSame('12345', $meta->config['phone_number_id']);
    }
}

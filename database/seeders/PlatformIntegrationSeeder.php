<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PlatformIntegrationSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection('tenant')->hasTable('platform_integrations')) {
            return;
        }

        $defaults = [
            'zoom' => [
                'platform' => 'zoom',
                'is_enabled' => false,
                'managed_by_admin' => false,
                'status' => 'needs_configuration',
                'status_message' => 'Zoom integration has not been configured yet.',
            ],
            'google_meet' => [
                'platform' => 'google_meet',
                'is_enabled' => false,
                'managed_by_admin' => false,
                'status' => 'needs_configuration',
                'status_message' => 'Google Meet integration has not been configured yet.',
            ],
            'microsoft_teams' => [
                'platform' => 'microsoft_teams',
                'is_enabled' => false,
                'managed_by_admin' => false,
                'status' => 'needs_configuration',
                'status_message' => 'Microsoft Teams integration has not been configured yet.',
            ],
        ];

        foreach ($defaults as $platform => $payload) {
            $existing = DB::connection('tenant')
                ->table('platform_integrations')
                ->where('platform', $platform)
                ->first();

            $timestamps = ['updated_at' => now()];

            if ($existing) {
                DB::connection('tenant')
                    ->table('platform_integrations')
                    ->where('platform', $platform)
                    ->update(array_merge($payload, $timestamps));

                continue;
            }

            DB::connection('tenant')->table('platform_integrations')->insert(array_merge(
                $payload,
                [
                    'api_key' => null,
                    'api_secret' => null,
                    'client_id' => null,
                    'client_secret' => null,
                    'redirect_uri' => null,
                    'access_token' => null,
                    'refresh_token' => null,
                    'token_expires_at' => null,
                    'last_tested_at' => null,
                    'additional_settings' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ));
        }
    }
}

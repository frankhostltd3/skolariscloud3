<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\MailSetting;
use App\Models\MessagingChannelSetting;
use App\Models\PaymentGatewaySetting;
use App\Models\School;
use App\Models\SchoolUserInvitation;
use App\Models\User;
use App\Services\TenantDatabaseManager;
use App\Support\CentralDomain;
use Database\Seeders\IntegrationHealthDemoSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (config('database.default') === 'tenant') {
            $this->seedPaymentGateways();
            $this->seedMessagingChannels();
            $this->call(\Database\Seeders\LeaveTypesSeeder::class);
            $this->call(\Database\Seeders\AcademicYearSeeder::class);
            $this->call(\Database\Seeders\EmployeeIdSettingsSeeder::class);
            $this->call(\Database\Seeders\PayrollSettingsSeeder::class);
            return;
        }

        $centralDomain = CentralDomain::base();
        $port = CentralDomain::port();
        $domainSuffix = $centralDomain . ($port ? ':' . $port : '');
        $manager = app(TenantDatabaseManager::class);

        $schools = [
            [
                'code' => 'SMATCAMPUS',
                'name' => 'SMATCAMPUS Demo School',
                'subdomain' => 'demo',
                'domain' => $centralDomain ? 'demo.' . $domainSuffix : null,
                'admin' => [
                    'name' => 'Test Admin',
                    'email' => 'test@example.com',
                    'password' => env('DEMO_ADMIN_PASSWORD', 'password'),
                ],
                'invitation_email' => 'test@example.com',
            ],
            [
                'code' => 'VICTORIANILE',
                'name' => 'Victoria Nile School',
                'subdomain' => 'victorianileschool',
                'domain' => $centralDomain ? 'victorianileschool.' . $domainSuffix : null,
                'admin' => [
                    'name' => 'Victoria Nile Admin',
                    'email' => 'admin@victorianileschool.com',
                    'password' => '5Loaves+2Fish',
                ],
            ],
        ];

        $frankhostDomain = env('FRANKHOST_DOMAIN', $centralDomain);

        if ($frankhostDomain) {
            $schools[] = [
                'code' => 'FRANKHOST',
                'name' => 'FrankHost School',
                'subdomain' => 'frankhost',
                'domain' => $frankhostDomain,
                'admin' => [
                    'name' => 'FrankHost Admin',
                    'email' => env('FRANKHOST_ADMIN_EMAIL', 'admin@frankhost.us'),
                    'password' => env('FRANKHOST_ADMIN_PASSWORD', 'admin123'),
                ],
            ];
        }

        foreach ($schools as $definition) {
            $this->bootstrapSchool($manager, $definition);
        }

        $this->seedPaymentGateways();
        $this->seedMessagingChannels();
        $this->call(IntegrationHealthDemoSeeder::class);
    }

    private function bootstrapSchool(TenantDatabaseManager $manager, array $definition): void
    {
        $school = School::query()->updateOrCreate(
            ['code' => $definition['code']],
            [
                'name' => $definition['name'],
                'subdomain' => $definition['subdomain'] ?? null,
                'domain' => $definition['domain'] ?? null,
            ]
        );

        $invitation = null;

        if (! empty($definition['invitation_email'])) {
            $invitation = SchoolUserInvitation::query()->updateOrCreate(
                [
                    'school_id' => $school->id,
                    'email' => $definition['invitation_email'],
                ],
                [
                    'user_type' => UserType::ADMIN,
                    'expires_at' => now()->addMonth(),
                ]
            );
        }

        $manager->runFor(
            $school,
            function () use ($school, $definition, $invitation) {
                $adminDetails = $definition['admin'];

                /** @var \App\Models\User $admin */
                $admin = User::query()->firstOrNew(['email' => $adminDetails['email']]);
                $admin->fill([
                    'name' => $adminDetails['name'],
                    'user_type' => UserType::ADMIN,
                    'school_id' => $school->id,
                    'approval_status' => 'approved',
                ]);

                if (! empty($adminDetails['password'])) {
                    $admin->password = $adminDetails['password'];
                }

                $admin->email_verified_at ??= now();
                $admin->save();

                if ($invitation && ! $invitation->isAccepted()) {
                    $invitation->markAccepted();
                }

                $fromHost = $definition['domain']
                    ?? config('tenancy.central_domain')
                    ?? parse_url(config('app.url'), PHP_URL_HOST)
                    ?? 'example.com';

                MailSetting::query()->firstOrCreate([], [
                    'mailer' => 'mail',
                    'from_name' => $school->name,
                    'from_address' => 'no-reply@' . ltrim($fromHost, '@'),
                    'config' => [],
                ]);

                $this->seedPaymentGateways();
                $this->seedMessagingChannels();
                $this->call(\Database\Seeders\LeaveTypesSeeder::class);
                $this->call(\Database\Seeders\AcademicYearSeeder::class);
                $this->call(\Database\Seeders\EmployeeIdSettingsSeeder::class);
                $this->call(\Database\Seeders\PayrollSettingsSeeder::class);
            },
            runMigrations: true
        );
    }

    private function seedPaymentGateways(): void
    {
        $gatewayDefinitions = config('payment_gateways.gateways', []);

        foreach ($gatewayDefinitions as $gatewayKey => $definition) {
            $fields = $definition['fields'] ?? [];
            $defaults = [];

            foreach ($fields as $fieldKey => $fieldDefinition) {
                if (array_key_exists('default', $fieldDefinition)) {
                    $defaults[$fieldKey] = $fieldDefinition['default'];
                }
            }

            PaymentGatewaySetting::query()->firstOrCreate(
                ['gateway' => $gatewayKey],
                [
                    'is_enabled' => false,
                    'config' => $defaults,
                ]
            );
        }
    }

    private function seedMessagingChannels(): void
    {
        $channelDefinitions = config('messaging.channels', []);

        foreach ($channelDefinitions as $channelKey => $channelDefinition) {
            $providers = $channelDefinition['providers'] ?? [];

            foreach ($providers as $providerKey => $definition) {
                $fields = $definition['fields'] ?? [];
                $defaults = [];

                foreach ($fields as $fieldKey => $fieldDefinition) {
                    if (array_key_exists('default', $fieldDefinition)) {
                        $defaults[$fieldKey] = $fieldDefinition['default'];
                    }
                }

                MessagingChannelSetting::query()->firstOrCreate(
                    [
                        'channel' => $channelKey,
                        'provider' => $providerKey,
                    ],
                    [
                        'is_enabled' => false,
                        'config' => $defaults,
                    ]
                );
            }
        }
    }
}

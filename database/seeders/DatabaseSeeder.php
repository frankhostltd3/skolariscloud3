<?php

namespace Database\Seeders;

use App\Enums\UserType;
use App\Models\MailSetting;
use App\Models\School;
use App\Models\SchoolUserInvitation;
use App\Models\User;
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
        // User::factory(10)->create();

        $centralDomain = config('tenancy.central_domain');

        $school = School::query()->firstOrCreate(
            ['subdomain' => 'demo'],
            [
                'name' => 'SMATCAMPUS Demo School',
                'code' => 'SMATCAMPUS',
                'domain' => $centralDomain ? 'demo.' . $centralDomain : null,
            ]
        );

        $invitation = SchoolUserInvitation::query()->firstOrCreate(
            [
                'school_id' => $school->id,
                'email' => 'test@example.com',
            ],
            [
                'user_type' => UserType::ADMIN,
                'expires_at' => now()->addMonth(),
            ]
        );

        $admin = User::query()->firstOrNew(['email' => 'test@example.com']);

        if (! $admin->exists) {
            $admin->fill([
                'name' => 'Test Admin',
                'user_type' => UserType::ADMIN,
                'school_id' => $school->id,
                'password' => Hash::make('password'),
            ])->save();
        } else {
            $admin->forceFill([
                'name' => 'Test Admin',
                'user_type' => UserType::ADMIN,
                'school_id' => $school->id,
            ])->save();
        }

        if (! $invitation->isAccepted()) {
            $invitation->markAccepted();
        }

        MailSetting::query()->firstOrCreate([], [
            'mailer' => 'mail',
            'from_name' => 'SMATCAMPUS',
            'from_address' => 'no-reply@' . (config('tenancy.central_domain') ?? 'example.com'),
            'config' => [],
        ]);
    }
}

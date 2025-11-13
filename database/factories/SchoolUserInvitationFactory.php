<?php

namespace Database\Factories;

use App\Enums\UserType;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\SchoolUserInvitation>
 */
class SchoolUserInvitationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'email' => fake()->unique()->safeEmail(),
            'user_type' => fake()->randomElement(UserType::cases()),
            'token' => null,
            'expires_at' => now()->addDays(7),
            'accepted_at' => null,
        ];
    }
}

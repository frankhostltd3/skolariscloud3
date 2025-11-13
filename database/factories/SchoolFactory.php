<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->company() . ' School';

        $subdomain = Str::slug(fake()->unique()->domainWord());
        $centralDomain = config('tenancy.central_domain');

        return [
            'name' => $name,
            'code' => Str::upper(Str::random(8)),
            'subdomain' => $subdomain,
            'domain' => $centralDomain ? $subdomain . '.' . $centralDomain : null,
            'database' => null,
            'meta' => null,
        ];
    }
}

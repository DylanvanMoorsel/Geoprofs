<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'role' => 'employee',
            'department_id' => Department::factory(),
            'manager_id' => null,
            'vacation_hours' => 200,
            'remember_token' => Str::random(10),
        ];
    }

    public function manager(): static
    {
        return $this->state(fn () => ['role' => User::ROLE_MANAGER]);
    }

    /** Medewerker van deze manager, in dezelfde afdeling. */
    public function reportingTo(User $manager): static
    {
        return $this->state(fn () => [
            'manager_id' => $manager->id,
            'department_id' => $manager->department_id,
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
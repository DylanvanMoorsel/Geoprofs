<?php

namespace Database\Factories;

use App\Models\Absence;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Absence> */
class AbsenceFactory extends Factory
{
    public function definition(): array
    {
        $start = now()->addWeek()->startOfWeek();

        return [
            'user_id' => User::factory(),
            'type' => Absence::TYPE_VACATION,
            'start_date' => $start->toDateString(),
            'end_date' => $start->copy()->addDays(2)->toDateString(),
            'status' => Absence::STATUS_PENDING,
            'reason' => null,
        ];
    }

    public function between(string $from, string $to): static
    {
        return $this->state(fn () => ['start_date' => $from, 'end_date' => $to]);
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => Absence::STATUS_APPROVED]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => Absence::STATUS_REJECTED]);
    }

    public function sick(): static
    {
        return $this->state(fn () => [
            'type' => Absence::TYPE_SICK,
            'status' => Absence::STATUS_APPROVED,
        ]);
    }
}
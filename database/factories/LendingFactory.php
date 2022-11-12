<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class LendingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $fakeTime = fake()->dateTime();

        return [
            'date_borrowed' => Carbon::create($fakeTime),
            'date_due' => Carbon::create($fakeTime)->addDays(7),
            'points' => fake()->numberBetween(-1, 2),
        ];
    }
}

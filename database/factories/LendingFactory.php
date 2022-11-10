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
        $dateBorrowed = Carbon::create(fake()->dateTime());
        $dateDue = $dateBorrowed->addDays(fake()->numberBetween(10,100));



        return [
            'date borrowed' => $dateBorrowed,
            'date due' => $dateDue,
            'points' => fake()->numberBetween(10, 100),
        ];
    }
}

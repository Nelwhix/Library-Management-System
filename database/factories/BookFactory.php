<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->sentence(),
            'edition' => fake()->randomElement(['1st', '2nd', '3rd']) . " Edition",
            'description' => fake()->paragraph(),
            'prologue' => fake()->sentence(),
            'tags' => implode(" ,", fake()->words()),
            'categories' => implode(" ,", fake()->words(5)),
        ];
    }
}

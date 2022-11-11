<?php

namespace Database\Factories;

use App\Models\Access_Level;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $age = fake()->numberBetween(7,100);

        $access_level = match(true) {
            $age >= 7 && $age < 15 => Access_Level::where('age', "7 to 15")->first(),

            $age >= 15 && $age <= 24 => Access_Level::where('age', "15 to 24")->first(),

            $age >= 25 && $age <= 49  => Access_Level::where('age', "25 to 49")->first(),

            $age >= 50 => Access_Level::where('age', "50 and above")->first(),
        };

        $access_level_id = $access_level->id;

        return [
            'firstName' => fake()->firstName(),
            'lastName' => fake()->lastName(),
            'userName' => fake()->userName(),
            'age' => $age,
            'access_level_id' => $access_level_id,
            'address' => fake()->address(),
            'points' => fake()->numberBetween(10, 100),
            'email' => fake()->unique()->safeEmail(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return static
     */
    public function unverified()
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}

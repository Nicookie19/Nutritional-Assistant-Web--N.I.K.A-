<?php

namespace Database\Factories;

use App\Models\UserExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserExperience>
 */
class UserExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'session_key' => fake()->uuid(),
            'full_name' => fake()->name(),
            'age' => fake()->numberBetween(21, 45),
            'gender' => fake()->randomElement(['Male', 'Female', 'Non-binary']),
            'activity_level' => fake()->randomElement(['Lightly Active', 'Moderately Active', 'Very Active']),
            'primary_goal' => fake()->randomElement(['Weight Loss', 'Weight Maintenance', 'Muscle Gain']),
            'height_cm' => fake()->numberBetween(150, 190),
            'current_weight_kg' => fake()->randomFloat(1, 55, 95),
            'target_weight_kg' => fake()->randomFloat(1, 50, 90),
            'starting_weight_kg' => fake()->randomFloat(1, 60, 100),
            'bmi_history' => [],
        ];
    }
}

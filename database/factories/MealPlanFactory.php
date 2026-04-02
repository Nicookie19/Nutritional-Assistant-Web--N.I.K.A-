<?php

namespace Database\Factories;

use App\Models\MealPlan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MealPlan>
 */
class MealPlanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_experience_id' => null,
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'daily_calories' => fake()->numberBetween(1700, 2400),
            'tags' => [fake()->word(), fake()->word()],
            'rating' => fake()->randomFloat(1, 4.2, 5.0),
            'is_template' => false,
            'is_active' => false,
        ];
    }
}

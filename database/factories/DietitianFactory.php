<?php

namespace Database\Factories;

use App\Models\Dietitian;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dietitian>
 */
class DietitianFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'specialization' => fake()->randomElement(['Sports Nutrition', 'Weight Management', 'Clinical Nutrition']),
            'experience_years' => fake()->numberBetween(4, 15),
            'patient_count' => fake()->numberBetween(10, 60),
            'rating' => fake()->randomFloat(1, 4.4, 5.0),
            'status' => 'active',
        ];
    }
}

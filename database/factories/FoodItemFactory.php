<?php

namespace Database\Factories;

use App\Models\FoodItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FoodItem>
 */
class FoodItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'category' => fake()->randomElement(['Protein', 'Grains', 'Vegetables', 'Fruits', 'Dairy']),
            'serving_size' => fake()->randomElement(['100g', '150g', '1 medium']),
            'calories' => fake()->numberBetween(40, 500),
            'protein' => fake()->randomFloat(1, 0, 40),
            'carbs' => fake()->randomFloat(1, 0, 70),
            'fat' => fake()->randomFloat(1, 0, 25),
            'fiber' => fake()->randomFloat(1, 0, 12),
            'is_active' => true,
        ];
    }
}

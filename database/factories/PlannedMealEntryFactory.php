<?php

namespace Database\Factories;

use App\Models\PlannedMealEntry;
use App\Models\UserExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlannedMealEntry>
 */
class PlannedMealEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_experience_id' => UserExperience::factory(),
            'scheduled_date' => now()->toDateString(),
            'meal_slot' => $this->faker->randomElement(['Breakfast', 'Lunch', 'Dinner', 'Snacks']),
            'food_name' => $this->faker->words(2, true),
            'grams' => $this->faker->numberBetween(50, 300),
            'calories' => $this->faker->numberBetween(80, 750),
        ];
    }
}

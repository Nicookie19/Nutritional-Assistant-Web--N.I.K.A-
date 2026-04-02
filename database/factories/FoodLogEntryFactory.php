<?php

namespace Database\Factories;

use App\Models\FoodItem;
use App\Models\FoodLogEntry;
use App\Models\UserExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FoodLogEntry>
 */
class FoodLogEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $foodItem = FoodItem::factory()->create();

        return [
            'user_experience_id' => UserExperience::factory(),
            'food_item_id' => $foodItem->id,
            'meal_slot' => fake()->randomElement(['Breakfast', 'Lunch', 'Dinner', 'Snacks']),
            'food_name' => $foodItem->name,
            'serving_label' => $foodItem->serving_size,
            'entry_date' => fake()->date(),
            'calories' => $foodItem->calories,
            'protein' => $foodItem->protein,
        ];
    }
}

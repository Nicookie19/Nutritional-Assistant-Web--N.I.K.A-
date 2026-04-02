<?php

namespace Database\Factories;

use App\Models\FoodItem;
use App\Models\MealPlan;
use App\Models\MealPlanItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MealPlanItem>
 */
class MealPlanItemFactory extends Factory
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
            'meal_plan_id' => MealPlan::factory(),
            'food_item_id' => $foodItem->id,
            'meal_slot' => fake()->randomElement(['Breakfast', 'Lunch', 'Dinner', 'Snacks']),
            'item_name' => $foodItem->name,
            'serving_label' => $foodItem->serving_size,
            'sort_order' => fake()->numberBetween(1, 4),
        ];
    }
}

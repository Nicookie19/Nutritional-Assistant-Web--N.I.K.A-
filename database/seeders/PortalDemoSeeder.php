<?php

namespace Database\Seeders;

use App\Models\Dietitian;
use App\Models\FeedbackRequest;
use App\Models\FoodItem;
use App\Models\MealPlan;
use App\Models\MealPlanItem;
use Illuminate\Database\Seeder;

class PortalDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $foods = collect([
            ['name' => 'Grilled Chicken Breast', 'category' => 'Protein', 'serving_size' => '100g', 'calories' => 165, 'protein' => 31, 'carbs' => 0, 'fat' => 3.6, 'fiber' => 0],
            ['name' => 'Brown Rice', 'category' => 'Grains', 'serving_size' => '100g', 'calories' => 112, 'protein' => 2.6, 'carbs' => 24, 'fat' => 0.9, 'fiber' => 1.8],
            ['name' => 'Broccoli', 'category' => 'Vegetables', 'serving_size' => '100g', 'calories' => 34, 'protein' => 2.8, 'carbs' => 7, 'fat' => 0.4, 'fiber' => 2.6],
            ['name' => 'Salmon Fillet', 'category' => 'Protein', 'serving_size' => '100g', 'calories' => 208, 'protein' => 20, 'carbs' => 0, 'fat' => 13, 'fiber' => 0],
            ['name' => 'Greek Yogurt', 'category' => 'Dairy', 'serving_size' => '100g', 'calories' => 59, 'protein' => 10, 'carbs' => 3.6, 'fat' => 0.4, 'fiber' => 0],
            ['name' => 'Banana', 'category' => 'Fruits', 'serving_size' => '1 medium', 'calories' => 89, 'protein' => 1.1, 'carbs' => 23, 'fat' => 0.3, 'fiber' => 2.6],
            ['name' => 'Almonds', 'category' => 'Nuts', 'serving_size' => '100g', 'calories' => 579, 'protein' => 21, 'carbs' => 22, 'fat' => 50, 'fiber' => 12.5],
            ['name' => 'Sweet Potato', 'category' => 'Vegetables', 'serving_size' => '100g', 'calories' => 86, 'protein' => 1.6, 'carbs' => 20, 'fat' => 0.1, 'fiber' => 3],
            ['name' => 'Eggs', 'category' => 'Protein', 'serving_size' => '2 large', 'calories' => 155, 'protein' => 13, 'carbs' => 1.1, 'fat' => 11, 'fiber' => 0],
            ['name' => 'Avocado', 'category' => 'Fruits', 'serving_size' => '100g', 'calories' => 160, 'protein' => 2, 'carbs' => 8.5, 'fat' => 15, 'fiber' => 6.7],
            ['name' => 'Oatmeal', 'category' => 'Grains', 'serving_size' => '100g', 'calories' => 389, 'protein' => 17, 'carbs' => 66, 'fat' => 7, 'fiber' => 10.6],
            ['name' => 'Spinach', 'category' => 'Vegetables', 'serving_size' => '100g', 'calories' => 23, 'protein' => 2.9, 'carbs' => 3.6, 'fat' => 0.4, 'fiber' => 2.2],
        ])->map(fn (array $food): FoodItem => FoodItem::query()->firstOrCreate(['name' => $food['name']], $food));

        $dietitian = Dietitian::query()->firstOrCreate(
            ['email' => 'sarah.mitchell@nutriassist.com'],
            [
                'name' => 'Dr. Sarah Mitchell',
                'specialization' => 'Weight Management',
                'experience_years' => 12,
                'patient_count' => 58,
                'rating' => 4.9,
                'status' => 'active',
            ],
        );

        foreach ([
            ['name' => 'Dr. Jennifer Adams', 'email' => 'j.adams@nutriassist.com', 'specialization' => 'Sports Nutrition', 'experience_years' => 8, 'patient_count' => 42, 'rating' => 4.8],
            ['name' => 'Dr. Lisa Park', 'email' => 'l.park@nutriassist.com', 'specialization' => 'Clinical Nutrition', 'experience_years' => 6, 'patient_count' => 35, 'rating' => 4.7],
        ] as $record) {
            Dietitian::query()->firstOrCreate(['email' => $record['email']], [...$record, 'status' => 'active']);
        }

        $templates = [
            [
                'name' => 'High Protein Plan',
                'description' => 'Perfect for muscle building and recovery.',
                'daily_calories' => 2100,
                'rating' => 4.8,
                'tags' => ['High Protein', 'Muscle Building'],
                'items' => [
                    'Breakfast' => [['Eggs', '2 large'], ['Greek Yogurt', '150g'], ['Banana', '1 medium']],
                    'Lunch' => [['Grilled Chicken Breast', '150g'], ['Brown Rice', '150g'], ['Broccoli', '100g']],
                    'Dinner' => [['Salmon Fillet', '150g'], ['Sweet Potato', '180g'], ['Spinach', '80g']],
                    'Snacks' => [['Almonds', '30g']],
                ],
            ],
            [
                'name' => 'Balanced Nutrition',
                'description' => 'Well-rounded meals for overall health.',
                'daily_calories' => 2000,
                'rating' => 4.8,
                'tags' => ['Balanced', 'Healthy'],
                'items' => [
                    'Breakfast' => [['Oatmeal', '80g'], ['Banana', '1 medium']],
                    'Lunch' => [['Grilled Chicken Breast', '120g'], ['Brown Rice', '120g'], ['Broccoli', '120g']],
                    'Dinner' => [['Salmon Fillet', '120g'], ['Sweet Potato', '160g'], ['Spinach', '60g']],
                    'Snacks' => [['Greek Yogurt', '120g'], ['Almonds', '20g']],
                ],
            ],
            [
                'name' => 'Plant-Based Power',
                'description' => 'Nutrient-dense vegetarian meals.',
                'daily_calories' => 1850,
                'rating' => 4.7,
                'tags' => ['Vegetarian', 'Plant-Based'],
                'items' => [
                    'Breakfast' => [['Oatmeal', '80g'], ['Banana', '1 medium'], ['Almonds', '20g']],
                    'Lunch' => [['Sweet Potato', '180g'], ['Spinach', '90g'], ['Avocado', '100g']],
                    'Dinner' => [['Brown Rice', '140g'], ['Broccoli', '140g'], ['Greek Yogurt', '100g']],
                    'Snacks' => [['Greek Yogurt', '120g'], ['Banana', '1 medium']],
                ],
            ],
        ];

        foreach ($templates as $template) {
            $mealPlan = MealPlan::query()->firstOrCreate(
                ['name' => $template['name'], 'is_template' => true],
                [
                    'description' => $template['description'],
                    'daily_calories' => $template['daily_calories'],
                    'rating' => $template['rating'],
                    'tags' => $template['tags'],
                    'is_template' => true,
                ],
            );

            foreach ($template['items'] as $mealSlot => $items) {
                foreach ($items as $index => [$foodName, $servingLabel]) {
                    $food = $foods->firstWhere('name', $foodName);

                    MealPlanItem::query()->firstOrCreate(
                        [
                            'meal_plan_id' => $mealPlan->id,
                            'meal_slot' => $mealSlot,
                            'item_name' => $foodName,
                            'serving_label' => $servingLabel,
                        ],
                        [
                            'food_item_id' => $food?->id,
                            'sort_order' => $index + 1,
                        ],
                    );
                }
            }
        }

        FeedbackRequest::query()->firstOrCreate(
            ['title' => 'Excellent Progress This Week!'],
            [
                'dietitian_id' => $dietitian->id,
                'topic' => 'progress',
                'tag' => 'New',
                'tag_tone' => 'blue',
                'priority' => 'medium',
                'status' => 'in-progress',
                'message' => 'I reviewed your food logs and your consistency is strong. Keep your current structure and add more vegetables for fiber.',
                'recommendations' => [
                    'Continue with current meal timing',
                    'Increase water intake to 10 glasses per day',
                    'Add one more vegetable serving at dinner',
                ],
                'submitted_on' => now()->subDays(4)->toDateString(),
            ],
        );
    }
}

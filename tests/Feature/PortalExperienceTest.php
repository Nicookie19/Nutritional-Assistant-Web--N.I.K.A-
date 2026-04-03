<?php

namespace Tests\Feature;

use App\Models\FeedbackRequest;
use App\Models\FoodItem;
use App\Models\MealPlan;
use App\Models\PlannedMealEntry;
use App\Models\User;
use App\Models\UserExperience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_and_customize_a_meal_plan(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['portal_session_key' => 'portal-test-user'])
            ->get(route('portal.meal-plans'))
            ->assertSuccessful();

        $experience = UserExperience::query()->where('session_key', 'portal-test-user')->firstOrFail();
        $foodItem = FoodItem::query()->firstOrFail();

        $this->actingAs($user)
            ->withSession(['portal_session_key' => 'portal-test-user'])
            ->post(route('portal.meal-plans.store'), [
                'name' => 'My Custom Cut Plan',
                'description' => 'Lean meals I can rotate all week.',
                'daily_calories' => 1900,
                'tags' => 'Cutting, Quick Prep',
            ])
            ->assertSessionHasNoErrors();

        $mealPlan = MealPlan::query()
            ->where('user_experience_id', $experience->id)
            ->where('name', 'My Custom Cut Plan')
            ->firstOrFail();

        $this->actingAs($user)
            ->withSession(['portal_session_key' => 'portal-test-user'])
            ->post(route('portal.meal-plans.items.store', $mealPlan), [
                'meal_slot' => 'Dinner',
                'food_item_id' => $foodItem->id,
                'serving_label' => '2 servings',
            ])
            ->assertRedirect(route('portal.meal-plans', ['edit_plan' => $mealPlan->id]));

        $this->actingAs($user)
            ->withSession(['portal_session_key' => 'portal-test-user'])
            ->post(route('portal.meal-plans.activate', $mealPlan))
            ->assertRedirect(route('portal.meal-plans', ['edit_plan' => $mealPlan->id]));

        $this->assertTrue($mealPlan->fresh()->is_active);
        $this->assertSame($mealPlan->id, $experience->fresh()->active_meal_plan_id);
        $this->assertTrue($mealPlan->items()->where('meal_slot', 'Dinner')->exists());
    }

    public function test_user_can_save_profile_updates_and_food_log_entries(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['portal_session_key' => 'profile-user'])
            ->get(route('portal.profile'))
            ->assertSuccessful();

        $foodItem = FoodItem::query()->firstOrFail();

        $this->actingAs($user)
            ->withSession(['portal_session_key' => 'profile-user'])
            ->post(route('portal.profile.update'), [
                'full_name' => 'Casey Rivera',
                'age' => 31,
                'gender' => 'Female',
                'activity_level' => 'Very Active',
                'primary_goal' => 'Muscle Gain',
                'height_cm' => 168,
                'current_weight_kg' => 64.5,
                'target_weight_kg' => 67.0,
            ])
            ->assertRedirect();

        $this->actingAs($user)
            ->withSession(['portal_session_key' => 'profile-user'])
            ->post(route('portal.food-log.store'), [
                'meal_slot' => 'Breakfast',
                'food_item_id' => $foodItem->id,
                'serving_label' => '1 bowl',
            ])
            ->assertRedirect(route('portal.food-log'));

        $experience = UserExperience::query()->where('session_key', 'profile-user')->firstOrFail();

        $this->assertSame('Casey Rivera', $experience->full_name);
        $this->assertTrue($experience->foodLogEntries()->where('food_name', $foodItem->name)->exists());
    }

    public function test_user_can_add_a_manual_food_log_entry(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['portal_session_key' => 'manual-food-user'])
            ->get(route('portal.food-log'))
            ->assertSuccessful();

        $this->actingAs($user)
            ->withSession(['portal_session_key' => 'manual-food-user'])
            ->post(route('portal.food-log.store'), [
                'meal_slot' => 'Lunch',
                'food_name' => 'Homemade Pasta',
                'grams' => 180,
                'calories' => 320,
            ])
            ->assertRedirect(route('portal.food-log'));

        $experience = UserExperience::query()->where('session_key', 'manual-food-user')->firstOrFail();
        $entry = $experience->foodLogEntries()->where('food_name', 'Homemade Pasta')->firstOrFail();

        $this->assertSame('180g', $entry->serving_label);
        $this->assertSame(320, $entry->calories);
        $this->assertSame('0.0', $entry->protein);
    }

    public function test_user_can_plan_meals_for_a_calendar_day(): void
    {
        $user = User::factory()->create();
        $selectedDate = '2026-04-12';

        $this->actingAs($user)
            ->withSession(['portal_session_key' => 'calendar-user'])
            ->get(route('portal.calendar', ['date' => $selectedDate]))
            ->assertSuccessful();

        $this->actingAs($user)
            ->withSession(['portal_session_key' => 'calendar-user'])
            ->post(route('portal.calendar.store'), [
                'scheduled_date' => $selectedDate,
                'entries' => [
                    'Breakfast' => ['food_name' => 'Egg Sandwich', 'grams' => 180, 'calories' => 340],
                    'Lunch' => ['food_name' => 'Chicken Rice Bowl', 'grams' => 320, 'calories' => 560],
                    'Dinner' => ['food_name' => 'Salmon Pasta', 'grams' => 280, 'calories' => 610],
                    'Snacks' => ['food_name' => 'Trail Mix', 'grams' => 60, 'calories' => 290],
                ],
            ])
            ->assertRedirect(route('portal.calendar', ['date' => $selectedDate]));

        $experience = UserExperience::query()->where('session_key', 'calendar-user')->firstOrFail();

        $this->assertTrue(PlannedMealEntry::query()
            ->where('user_experience_id', $experience->id)
            ->whereDate('scheduled_date', $selectedDate)
            ->where('meal_slot', 'Dinner')
            ->where('food_name', 'Salmon Pasta')
            ->exists());

        $this->actingAs($user)
            ->withSession(['portal_session_key' => 'calendar-user'])
            ->get(route('portal.calendar', ['date' => $selectedDate]))
            ->assertSuccessful()
            ->assertSee('Egg Sandwich')
            ->assertSee('Salmon Pasta');
    }

    public function test_admin_can_manage_food_items_and_complete_feedback(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get(route('admin.content'))
            ->assertSuccessful();

        $this->actingAs($admin)
            ->post(route('admin.food-items.store'), [
                'name' => 'Cottage Cheese',
                'category' => 'Dairy',
                'serving_size' => '100g',
                'calories' => 98,
                'protein' => 11.1,
                'carbs' => 3.4,
                'fat' => 4.3,
                'fiber' => 0,
            ])
            ->assertRedirect(route('admin.content'));

        $foodItem = FoodItem::query()->where('name', 'Cottage Cheese')->firstOrFail();
        $feedback = FeedbackRequest::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.feedback.complete', $feedback))
            ->assertRedirect(route('admin.feedback'));

        $this->assertTrue($foodItem->exists);
        $this->assertSame('completed', $feedback->fresh()->status);
    }
}

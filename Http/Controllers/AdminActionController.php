<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveFoodItemRequest;
use App\Http\Requests\SaveMealPlanRequest;
use App\Http\Requests\StoreDietitianRequest;
use App\Http\Requests\StoreMealPlanItemRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\Dietitian;
use App\Models\FeedbackRequest;
use App\Models\FoodItem;
use App\Models\MealPlan;
use App\Models\MealPlanItem;
use App\Models\UserExperience;
use Illuminate\Http\RedirectResponse;

class AdminActionController extends Controller
{
    public function updateUserExperience(UpdateProfileRequest $request, UserExperience $userExperience): RedirectResponse
    {
        $validated = $request->validated();

        $height = array_key_exists('height_cm', $validated) && $validated['height_cm'] !== null
            ? (float) $validated['height_cm']
            : (float) ($userExperience->height_cm ?? 0);

        $currentWeight = array_key_exists('current_weight_kg', $validated) && $validated['current_weight_kg'] !== null
            ? (float) $validated['current_weight_kg']
            : (float) ($userExperience->current_weight_kg ?? 0);

        $targetWeight = array_key_exists('target_weight_kg', $validated) && $validated['target_weight_kg'] !== null
            ? (float) $validated['target_weight_kg']
            : (float) ($userExperience->target_weight_kg ?? 0);

        $startingWeight = array_key_exists('starting_weight_kg', $validated) && $validated['starting_weight_kg'] !== null
            ? (float) $validated['starting_weight_kg']
            : (float) ($userExperience->starting_weight_kg ?: $currentWeight);

        $heightInMeters = $height > 0 ? $height / 100 : 0;
        $bmi = $heightInMeters > 0 && $currentWeight > 0
            ? round($currentWeight / ($heightInMeters * $heightInMeters), 1)
            : 0.0;

        $history = $userExperience->bmi_history ?? [];
        if ($currentWeight > 0) {
            $history[] = [
                'date' => now()->format('M j'),
                'weight' => number_format($currentWeight, 1).' kg',
                'bmi' => number_format($bmi, 1),
            ];
        }

        $userExperience->update([
            'full_name' => $validated['full_name'] ?? $userExperience->full_name,
            'age' => $validated['age'] ?? $userExperience->age,
            'gender' => $validated['gender'] ?? $userExperience->gender,
            'activity_level' => $validated['activity_level'] ?? $userExperience->activity_level,
            'primary_goal' => $validated['primary_goal'] ?? $userExperience->primary_goal,
            'height_cm' => $height > 0 ? $height : $userExperience->height_cm,
            'current_weight_kg' => $currentWeight > 0 ? $currentWeight : $userExperience->current_weight_kg,
            'target_weight_kg' => $targetWeight > 0 ? $targetWeight : $userExperience->target_weight_kg,
            'starting_weight_kg' => $startingWeight > 0 ? $startingWeight : $userExperience->starting_weight_kg,
            'bmi_history' => array_slice($history, -6),
        ]);

        return redirect()
            ->route('admin.users', ['edit_user' => $userExperience->id])
            ->with('status', 'User profile updated from admin hub.');
    }

    public function storeDietitian(StoreDietitianRequest $request): RedirectResponse
    {
        Dietitian::query()->create([
            ...$request->validated(),
            'patient_count' => 0,
            'rating' => 5.0,
            'status' => 'active',
        ]);

        return redirect()->route('admin.dietitians')->with('status', 'Dietitian added.');
    }

    public function saveFoodItem(SaveFoodItemRequest $request, ?FoodItem $foodItem = null): RedirectResponse
    {
        $foodItem ??= new FoodItem;
        $foodItem->fill($request->validated())->save();

        return redirect()->route('admin.content')->with('status', 'Food item saved.');
    }

    public function destroyFoodItem(FoodItem $foodItem): RedirectResponse
    {
        $foodItem->delete();

        return redirect()->route('admin.content')->with('status', 'Food item deleted.');
    }

    public function saveMealPlan(SaveMealPlanRequest $request, ?MealPlan $mealPlan = null): RedirectResponse
    {
        $mealPlan ??= new MealPlan;
        $mealPlan->fill([
            'name' => $request->string('name')->toString(),
            'description' => $request->string('description')->toString(),
            'daily_calories' => $request->integer('daily_calories'),
            'tags' => collect(explode(',', (string) $request->string('tags')))->map(fn (string $tag): string => trim($tag))->filter()->values()->all(),
            'is_template' => true,
            'rating' => $mealPlan->rating ?? 4.8,
        ])->save();

        return redirect()->route('admin.content', ['edit_meal_plan' => $mealPlan->id])->with('status', 'Meal plan saved.');
    }

    public function addMealPlanItem(StoreMealPlanItemRequest $request, MealPlan $mealPlan): RedirectResponse
    {
        abort_unless($mealPlan->is_template, 404);

        $foodItem = FoodItem::query()->findOrFail($request->integer('food_item_id'));

        $mealPlan->items()->create([
            'food_item_id' => $foodItem->id,
            'meal_slot' => $request->string('meal_slot')->toString(),
            'item_name' => $foodItem->name,
            'serving_label' => $request->string('serving_label')->toString(),
            'sort_order' => (int) $mealPlan->items()->where('meal_slot', $request->string('meal_slot')->toString())->count() + 1,
        ]);

        return redirect()->route('admin.content', ['edit_meal_plan' => $mealPlan->id])->with('status', 'Meal added to template plan.');
    }

    public function destroyMealPlanItem(MealPlan $mealPlan, MealPlanItem $mealPlanItem): RedirectResponse
    {
        abort_unless($mealPlan->is_template && $mealPlanItem->meal_plan_id === $mealPlan->id, 404);

        $mealPlanItem->delete();

        return redirect()->route('admin.content', ['edit_meal_plan' => $mealPlan->id])->with('status', 'Meal removed from template plan.');
    }

    public function destroyMealPlan(MealPlan $mealPlan): RedirectResponse
    {
        abort_unless($mealPlan->is_template, 404);

        $mealPlan->delete();

        return redirect()->route('admin.content')->with('status', 'Meal plan deleted.');
    }

    public function completeFeedback(FeedbackRequest $feedbackRequest): RedirectResponse
    {
        $feedbackRequest->update([
            'status' => 'completed',
            'is_read' => true,
        ]);

        return redirect()->route('admin.feedback')->with('status', 'Feedback request marked complete.');
    }
}

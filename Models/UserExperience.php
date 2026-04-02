<?php

namespace App\Models;

use Database\Factories\UserExperienceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'session_key',

    'active_meal_plan_id',
    'active_dietitian_id',
    'full_name',
    'age',
    'gender',
    'activity_level',
    'primary_goal',
    'height_cm',
    'current_weight_kg',
    'target_weight_kg',
    'starting_weight_kg',
    'bmi_history',
])]
class UserExperience extends Model
{
    /** @use HasFactory<UserExperienceFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'bmi_history' => 'array',
            'user_id' => 'integer',

            'current_weight_kg' => 'decimal:1',
            'target_weight_kg' => 'decimal:1',
            'starting_weight_kg' => 'decimal:1',
        ];
    }

    public function activeMealPlan(): BelongsTo
    {
        return $this->belongsTo(MealPlan::class, 'active_meal_plan_id');
    }

    public function activeDietitian(): BelongsTo
    {
        return $this->belongsTo(Dietitian::class, 'active_dietitian_id');
    }

    public function mealPlans(): HasMany
    {
        return $this->hasMany(MealPlan::class);
    }

    public function foodLogEntries(): HasMany
    {
        return $this->hasMany(FoodLogEntry::class);
    }

    public function plannedMealEntries(): HasMany
    {
        return $this->hasMany(PlannedMealEntry::class);
    }

    public function feedbackRequests(): HasMany
    {
        return $this->hasMany(FeedbackRequest::class);
    }
}

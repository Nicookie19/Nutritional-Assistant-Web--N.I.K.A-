<?php

namespace App\Models;

use Database\Factories\MealPlanFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_experience_id',
    'name',
    'description',
    'daily_calories',
    'tags',
    'rating',
    'is_template',
    'is_active',
])]
class MealPlan extends Model
{
    /** @use HasFactory<MealPlanFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'rating' => 'decimal:1',
            'is_template' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function userExperience(): BelongsTo
    {
        return $this->belongsTo(UserExperience::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(MealPlanItem::class)->orderBy('meal_slot')->orderBy('sort_order');
    }
}

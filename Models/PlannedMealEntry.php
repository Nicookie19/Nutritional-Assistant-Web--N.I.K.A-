<?php

namespace App\Models;

use Database\Factories\PlannedMealEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_experience_id',
    'scheduled_date',
    'meal_slot',
    'food_name',
    'grams',
    'calories',
])]
class PlannedMealEntry extends Model
{
    /** @use HasFactory<PlannedMealEntryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
        ];
    }

    public function userExperience(): BelongsTo
    {
        return $this->belongsTo(UserExperience::class);
    }
}

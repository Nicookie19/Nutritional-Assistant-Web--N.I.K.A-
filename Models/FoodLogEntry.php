<?php

namespace App\Models;

use Database\Factories\FoodLogEntryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_experience_id',
    'food_item_id',
    'meal_slot',
    'food_name',
    'serving_label',
    'entry_date',
    'calories',
    'protein',
])]
class FoodLogEntry extends Model
{
    /** @use HasFactory<FoodLogEntryFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'protein' => 'decimal:1',
        ];
    }

    public function userExperience(): BelongsTo
    {
        return $this->belongsTo(UserExperience::class);
    }

    public function foodItem(): BelongsTo
    {
        return $this->belongsTo(FoodItem::class);
    }
}

<?php

namespace App\Http\Requests;

use App\Models\FoodItem;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveFoodItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var FoodItem|null $foodItem */
        $foodItem = $this->route('foodItem');

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('food_items', 'name')->ignore($foodItem)],
            'category' => ['required', 'string', 'max:100'],
            'serving_size' => ['required', 'string', 'max:100'],
            'calories' => ['required', 'integer', 'min:0', 'max:5000'],
            'protein' => ['required', 'numeric', 'min:0', 'max:500'],
            'carbs' => ['required', 'numeric', 'min:0', 'max:500'],
            'fat' => ['required', 'numeric', 'min:0', 'max:500'],
            'fiber' => ['required', 'numeric', 'min:0', 'max:500'],
        ];
    }
}
